<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Dokumen;
use App\Models\IndukDokumen;
use App\Models\RuleCode;
use App\Models\User;
use App\Notifications\DokumenStatusNotification;
// use App\Notifications\DokumenMasukNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
// use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class DocruleController extends Controller
{
    public function index($jenis, $tipe)
    {
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;

        // Ambil dokumen berdasarkan jenis, tipe, departemen user yang login, dan status selain 'final approved'
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->whereHas('indukDokumen.user.departemen', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->whereHas('indukDokumen', function ($query) {
                $query->where('status', '!=', 'final approved');
            })
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->whereHas('user.departemen', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->where('status', '!=', 'final approved')
            ->get();

        $kodeProses = RuleCode::all();
        $departemens = Departemen::all();

        return view('pages-rule.dokumen-rule', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses', 'departemens'));
    }
    public function store(Request $request)
    {
        // Validasi request
        $request->validate([
            'nama_dokumen' => 'required',
            'file_draft' => 'required|file',
            'rule_id' => 'required|exists:rules,id',
            'jenis_dokumen' => 'required',
            'tipe_dokumen' => 'required',
            'revisi_ke' => 'nullable|integer',
            'departemen' => 'nullable|array',
            'departemen.*' => 'exists:departemens,id',
        ]);

        // Proses penyimpanan dokumen
        $file = $request->file('file_draft');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = 'dokumen/' . $filename;
        $file->storeAs('dokumen', $filename, 'public');

        $userId = auth()->id();
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;
        $nomor_revisi = $request->filled('revisi_ke') ? $request->revisi_ke : 0;

        $rule = RuleCode::find($request->rule_id);
        if (!$rule) {
            return redirect()->back()->with('error', 'Rule tidak valid.');
        }
        $kode_proses = $rule->kode_proses;

        $documentId = Dokumen::where('jenis_dokumen', $request->jenis_dokumen)
            ->where('tipe_dokumen', $request->tipe_dokumen)
            ->value('id');

        if (!$documentId) {
            return redirect()->back()->with('error', 'Jenis dan tipe dokumen tidak valid.');
        }

        $latestDokumen = IndukDokumen::where('dokumen_id', $documentId)
            ->orderBy('id', 'desc')
            ->first();

        $currentNumber = $latestDokumen ? intval(substr($latestDokumen->nomor_dokumen, -5, 3)) : 0;
        $newNumber = $currentNumber + 1;
        $number = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $nomorDokumen = sprintf(
            '%s-%s-%s-%s-%02d',
            strtoupper(substr($request->tipe_dokumen, 0, 3)),
            strtoupper(substr($departemen_user, 0, 3)),
            strtoupper($kode_proses),
            $number,
            $nomor_revisi
        );

        $dokumen = new IndukDokumen();
        $dokumen->nama_dokumen = $request->nama_dokumen;
        $dokumen->dokumen_id = $documentId;
        $dokumen->file_draft = $path;
        $dokumen->revisi_log = $nomor_revisi;
        $dokumen->nomor_dokumen = $nomorDokumen;
        $dokumen->tgl_upload = now();
        $dokumen->user_id = $userId;
        $dokumen->rule_id = $request->rule_id;
        $dokumen->status = 'waiting approval';
        $dokumen->comment = 'Dokumen baru dengan nama "' . $dokumen->nama_dokumen . '" telah diunggah.';
        $dokumen->save();

        if ($request->has('departemen')) {
            $dokumen->departments()->attach($request->departemen);
        }

        // Membuat notifikasi
        $adminUsers = User::role('admin')->get();
        $user = User::find($userId);

        Notification::send($adminUsers, new DokumenStatusNotification($dokumen));
        Notification::send($user, new DokumenStatusNotification($dokumen));

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function update(Request $request, $jenis, $tipe, $id)
    {
        // Validasi request
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'file_draft' => 'nullable|file|max:10240', // Optional file update, max 10MB
            'rule_id' => 'required|exists:rule,id', // Pastikan rule_id ada di tabel rules
            'jenis_dokumen' => 'required', // Ini mungkin diambil dari form atau logika lain
            'tipe_dokumen' => 'required', // Ini mungkin diambil dari form atau logika lain
            'departemen' => 'nullable|array',
            'departemen.*' => 'exists:departemen,id',
        ]);

        // Cari dokumen yang akan diperbarui
        $dokumen = IndukDokumen::findOrFail($id);

        // Tambahkan revisi_log
        $nomor_revisi = $dokumen->revisi_log + 1;

        // Ambil user ID dari pengguna yang sedang login
        $userId = auth()->id();

        // Ambil informasi departemen dari pengguna yang login
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;

        // Ambil kode proses dari input rule_id
        $rule = RuleCode::find($request->rule_id);
        if (!$rule) {
            return redirect()->back()->with('error', 'Rule tidak valid.');
        }
        $kode_proses = $rule->kode_proses;

        // Ambil document_id dari tabel Dokumen berdasarkan jenis dan tipe dokumen
        $documentId = Dokumen::where('jenis_dokumen', $request->jenis_dokumen)
            ->where('tipe_dokumen', $request->tipe_dokumen)
            ->value('id');

        // Pastikan documentId ditemukan
        if (!$documentId) {
            return redirect()->back()->with('error', 'Jenis dan tipe dokumen tidak valid.');
        }

        // Ambil file baru jika ada
        if ($request->hasFile('file_draft')) {
            // Hapus file lama jika ada
            if (!is_null($dokumen->file_draft) && Storage::disk('public')->exists($dokumen->file_draft)) {
                Storage::disk('public')->delete($dokumen->file_draft);
            }

            // Simpan file baru
            $file = $request->file('file_draft');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'dokumen/' . $filename;
            $file->storeAs('dokumen', $filename, 'public');

            // Atur path file baru
            $dokumen->file_draft = $path;
        }

        // Atur nama dokumen, kode proses, dan rule_id
        $dokumen->nama_dokumen = $request->nama_dokumen;
        $dokumen->rule_id = $request->rule_id;

        // Gunakan nilai revisi_log yang baru untuk menghasilkan nomor dokumen
        $nomorDokumen = sprintf(
            '%s-%s-%s-%03d-%02d',
            strtoupper(substr($request->tipe_dokumen, 0, 3)), // Mengambil tiga karakter pertama dari tipe dokumen
            strtoupper(substr($departemen_user, 0, 3)), // Mengambil tiga karakter pertama dari departemen
            strtoupper($kode_proses),
            $documentId, // Nomor list dokumen, tetap sama
            $nomor_revisi // Menggunakan revisi_log yang baru
        );
        $dokumen->nomor_dokumen = $nomorDokumen;

        // Update nilai revisi_log yang baru
        $dokumen->revisi_log = $nomor_revisi;

        // Simpan perubahan
        $dokumen->save();

        // Handle departemen jika ada
        if ($request->has('departemen')) {
            $dokumen->departments()->sync($request->departemen);
        }

        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('rule.index', ['jenis' => $jenis, 'tipe' => $tipe])->with('success', 'Dokumen berhasil diperbarui.');
    }
    public function final_upload(Request $request, $id)
    {
        // Validasi request
        $request->validate([
            'file_final' => 'required|file',
        ]);

        // Ambil dokumen berdasarkan ID yang diterima
        $dokumen = IndukDokumen::findOrFail($id);

        // Ambil file yang diunggah
        $file = $request->file('file_final');

        // Generate nama file unik dengan timestamp
        $filename = time() . '_' . $file->getClientOriginalName();

        // Tentukan path penyimpanan file
        $path = 'dokumen/' . $filename;

        // Simpan file di penyimpanan publik
        $file->storeAs('dokumen', $filename, 'public');

        // Update kolom file_final di database
        $dokumen->file_final = $path;

        // Update status dokumen menjadi "pending final approved"
        $dokumen->status = 'waiting final approval';

        // Simpan perubahan
        $dokumen->save();

        // Redirect atau kembalikan respons sukses
        return redirect()->back()->with('success', 'Dokumen final berhasil diunggah. Status dokumen sekarang adalah "pending final approved".');
    }
    public function download_draft($jenis, $tipe, $id)
    {
        // Lakukan validasi jika diperlukan
        // Misalnya, pastikan jenis dan tipe sesuai dengan kebutuhan bisnis Anda.

        // Ambil dokumen berdasarkan ID
        $dokumen = IndukDokumen::find($id);

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_draft;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download file dengan nama yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }
    public function validate_index($jenis, $tipe)
    {
        // Ambil dokumen yang sesuai dengan jenis dan tipe dokumen
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih dan memiliki status "waiting approval"
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->where('status', 'waiting approval')
            ->get();

        // Ambil semua kode proses
        $kodeProses = RuleCode::all();

        // Return view dengan data yang sudah difilter
        return view('pages-rule.validasi-rule', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses'));
    }

    public function approveDocument(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
            'file_draft' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah status dokumen adalah "waiting approval"
        if ($dokumen->status != 'waiting approval') {
            return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
        }

        // Lakukan perubahan status menjadi "draft approved"
        $dokumen->status = 'draft approved'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

        // Buat comment dengan nama dokumen yang di-approve
        $dokumen->comment = 'Your draft "' . $dokumen->nama_dokumen . '" has been approved. ' . $request->input('comment');

        //Code untuk menambahkan file yang salah
        if ($request->hasFile('file_draft')) {
            // Hapus file draft lama jika ada
            if ($dokumen->file_draft) {
                Storage::delete($dokumen->file_draft);
            }

            // Upload file draft baru
            $filePath = $request->file('file_draft')->store('dokumen');
            $dokumen->file_draft = $filePath;
        }

        // Simpan perubahan
        $dokumen->save();

        // Kirim notifikasi
        $user = $dokumen->user; // Mengambil user yang terkait dengan dokumen
        $user->notify(new DokumenStatusNotification($dokumen, 'draft approved', $request->input('comment')));

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Dokumen berhasil diapprove.');
    }

    // public function RejectedDocument(Request $request, $id)
    // {
    //     // Temukan dokumen berdasarkan ID
    //     $dokumen = IndukDokumen::findOrFail($id);

    //     // Validasi input comment
    //     $request->validate([
    //         'comment' => 'required|string|max:255',
    //     ]);

    //     if ($dokumen->status != 'waiting approval') {
    //         return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
    //     }

    //     // Lakukan perubahan status menjadi "rejected"
    //     $dokumen->status = 'draft rejected'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

    //     // Simpan comment dari input form ke kolom comment
    //     $dokumen->comment = 'Your draft "' . $dokumen->nama_dokumen . '" has been rejected. ' . $request->input('comment');

    //     // Simpan perubahan
    //     $dokumen->save();

    //     // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
    //     return redirect()->back()->with('success', 'Dokumen berhasil direject. Tolong upload kembali dokumen yang benar.');
    // }
    public function share_document()
    {
        // Mendapatkan user yang sedang login
        $user = auth()->user();

        // Mendapatkan nama departemen dari user
        $departemen_user = $user->departemen->nama_departemen;

        // Mengambil dokumen yang dibagikan berdasarkan departemen user dan memiliki status final approved
        $sharedDocuments = IndukDokumen::whereHas('departments', function ($query) use ($departemen_user) {
            $query->where('nama_departemen', $departemen_user);
        })
            ->where('status', 'final approved')
            ->whereNotNull('file_final')
            ->orderByDesc('created_at')
            ->get();

        return view('pages-rule.document-shared', compact('sharedDocuments'));
    }

    public function downloadSharedDocument($id)
    {
        $user = auth()->user();
        $departemen_user = $user->departemen->nama_departemen;

        // Periksa apakah dokumen tersebut dibagikan kepada departemen user yang sedang login
        // dan memiliki status final approved serta kolom file_final tidak null
        $dokumen = IndukDokumen::where('id', $id)
            ->whereHas('departments', function ($query) use ($departemen_user) {
                $query->where('nama_departemen', $departemen_user);
            })
            ->where('status', 'final approved')
            ->whereNotNull('file_final')
            ->first();

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan atau tidak diizinkan untuk diakses.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_final;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download file dengan nama yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }

    public function Validate_final($jenis, $tipe)
    {
        $dokumen = Dokumen::where('jenis_dokumen', $jenis)
            ->where('tipe_dokumen', $tipe)
            ->get();

        // Ambil induk dokumen yang sesuai dengan dokumen yang telah dipilih
        $indukDokumenList = IndukDokumen::whereIn('dokumen_id', $dokumen->pluck('id'))
            ->where('status', ['waiting final approval'])
            ->get();

        $kodeProses = RuleCode::all();

        return view('pages-rule.validasi_final', compact('jenis', 'tipe', 'dokumen', 'indukDokumenList', 'kodeProses'));
    }
    public function finalapproved(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Periksa apakah status dokumen adalah "waiting final approval"
        if ($dokumen->status != 'waiting final approval') {
            return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
        }

        // Lakukan perubahan status menjadi "approved"
        $dokumen->status = 'final approved'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

        // Buat comment dengan nama dokumen yang di-approve
        $dokumen->comment = 'Your "' . $dokumen->nama_dokumen . '" has been approved by MS.';

        // Simpan perubahan
        $dokumen->save();

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Dokumen berhasil diapprove.');
    }
    public function finalrejected(Request $request, $id)
    {
        // Temukan dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Validasi input comment
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        // Periksa apakah status dokumen adalah "waiting approval"
        if ($dokumen->status != 'waiting final approval') {
            return redirect()->back()->with('error', 'Dokumen tidak dalam status waiting approval.');
        }

        // Lakukan perubahan status menjadi "final rejected"
        $dokumen->status = 'final rejected'; // Sesuaikan dengan kolom status di tabel IndukDokumen Anda

        // Buat pesan comment
        $pesanCommand = 'Your document "' . $dokumen->nama_dokumen . '" has been rejected. ' . $request->input('comment') . '. Please upload the correct document.';

        // Simpan pesan comment ke dalam kolom comment
        $dokumen->comment = $pesanCommand;

        // Simpan perubahan
        $dokumen->save();

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Dokumen berhasil direject. Tolong upload kembali dokumen yang benar.');
    }

    public function final_doc()
    {
        // Mengambil semua dokumen yang memiliki status final approved
        $dokumenfinal = IndukDokumen::where('status', 'final approved')
            ->whereNotNull('file_final')
            ->orderByDesc('created_at')
            ->get();

        return view('pages-rule.dokumen-final', compact('dokumenfinal'));
    }
    public function downloadfinal($id)
    {
        // Lakukan validasi jika diperlukan
        // Misalnya, pastikan jenis dan tipe sesuai dengan kebutuhan bisnis Anda.

        // Ambil dokumen berdasarkan ID
        $dokumen = IndukDokumen::find($id);

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Periksa apakah dokumen memiliki file final
        if (is_null($dokumen->file_final)) {
            return redirect()->back()->with('error', 'Dokumen belum memiliki file final atau tidak diizinkan untuk diunduh.');
        }

        // Periksa apakah dokumen memiliki status final approved
        if ($dokumen->status != 'final approved') {
            return redirect()->back()->with('error', 'Dokumen belum disetujui final atau tidak diizinkan untuk diunduh.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_final;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download file dengan nama yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }
    public function DownloadDocFinal($id)
    {
        // Ambil dokumen berdasarkan ID
        $dokumen = IndukDokumen::find($id);

        if (!$dokumen) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Periksa apakah dokumen memiliki status final approved
        if ($dokumen->status != 'waiting final approved') {
            return redirect()->back()->with('error', 'Dokumen belum disetujui final atau tidak diizinkan untuk diunduh.');
        }

        // Periksa apakah dokumen memiliki file final
        if (is_null($dokumen->file_final)) {
            return redirect()->back()->with('error', 'Dokumen belum memiliki file final atau tidak diizinkan untuk diunduh.');
        }

        // Ambil path file dari database
        $path = $dokumen->file_final;

        // Periksa apakah path tidak null dan merupakan string
        if (is_null($path) || !is_string($path)) {
            return redirect()->back()->with('error', 'Path file tidak valid.');
        }

        // Periksa apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di storage.');
        }

        // Tentukan nama file yang akan diunduh
        $downloadFilename = $dokumen->nomor_dokumen . '_' . $dokumen->nama_dokumen . '.' . pathinfo($path, PATHINFO_EXTENSION);

        // Lakukan download file dengan nama yang ditentukan
        return Storage::disk('public')->download($path, $downloadFilename);
    }
    public function updateStatusDoc(Request $request, $id)
    {
        // Validasi request
        $request->validate([
            'statusdoc' => 'required|in:active,obsolate', // Memastikan statusdoc adalah active atau obsolate
        ]);

        // Cari dokumen berdasarkan ID
        $dokumen = IndukDokumen::findOrFail($id);

        // Ubah status dokumen berdasarkan nilai statusdoc yang diterima
        $dokumen->statusdoc = $request->statusdoc;
        $dokumen->save();

        // Redirect atau kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Status dokumen berhasil diperbarui.');
    }

    // Metode lain dalam DokumenController
    // ...
}
