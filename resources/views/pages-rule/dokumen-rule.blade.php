@extends('layouts.app')
@section('title', 'Validasi Rule')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dokumen {{ ucfirst($jenis) }} - Tipe: {{ ucfirst($tipe) }}</h4>
                        <p class="card-description"></p>
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploaddraftModal">
                                Upload Draft
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Judul Dokumen</th>
                                        <th>Revisi</th>
                                        <th>Tanggal Upload</th>
                                        <th>status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($indukDokumenList as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_dokumen }}</td>
                                            <td>{{ $doc->nama_dokumen }}</td>
                                            <td>{{ $doc->revisi_log }}</td>
                                            <td>{{ $doc->tgl_upload }}</td>
                                            <td>{{ $doc->status }}</td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                {{-- <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editDokumen-{{ $doc->id }}">
                                                    Edit
                                                    <i class="fa-solid fa-edit"></i>
                                                </button> --}}

                                                <!-- Tombol Download -->
                                                <a href="{{ route('download.draft', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}?preview=true"
                                                    class="btn btn-primary btn-sm" target="_blank">
                                                    Preview
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>

                                                @if ($doc->status == 'draft approved' || $doc->status == 'final rejected')
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                        data-target="#uploadfinalModal-{{ $doc->id }}">
                                                        Upload Final
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary btn-sm" disabled>
                                                        Upload Final
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Draft Modal -->
    <div class="modal fade" id="uploaddraftModal" tabindex="-1" role="dialog" aria-labelledby="uploaddraftModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('tambah.rule') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploaddraftModalLabel">Upload Draft</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form untuk Upload Draft -->
                        <div class="form-group">
                            <label for="nama_dokumen">Judul Dokumen</label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" required>
                        </div>
                        <div class="form-group">
                            <label for="status_dokumen">Status Dokumen</label>
                            <select class="form-control" id="status_dokumen" name="status_dokumen" required>
                                <option value="">Pilih Status Dokumen</option>
                                <option value="baru">Baru</option>
                                <option value="revisi">Revisi</option>
                            </select>
                        </div>
                        <div class="form-group" id="revisi_ke_group" style="display: none;">
                            <label for="revisi_ke">Revisi ke</label>
                            <input type="number" class="form-control" id="revisi_ke" name="revisi_ke">
                        </div>
                        <div class="form-group">
                            <label for="rule_id">Kode Proses</label>
                            <select class="form-control" id="rule_id" name="rule_id" required>
                                <option value="">Pilih Kode Proses</option>
                                @foreach ($kodeProses as $item)
                                    <option value="{{ $item['id'] }}">
                                        {{ $item['kode_proses'] }} - {{ $item['nama_proses'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Share Document To:</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select_all">
                                            <label class="form-check-label" for="select_all">Select All</label>
                                        </div>
                                    </div>
                                    @foreach ($departemens as $dept)
                                        <div class="col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="dept_{{ $dept->id }}" name="departemen[]"
                                                    value="{{ $dept->id }}">
                                                <label class="form-check-label"
                                                    for="dept_{{ $dept->id }}">{{ $dept->nama_departemen }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="file">File</label>
                            <input type="file" class="form-control" id="file" name="file_draft" required>
                        </div>
                        <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                        <input type="hidden" name="tipe_dokumen" value="{{ $tipe }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- upload final draft --}}
    @foreach ($indukDokumenList as $doc)
        <div class="modal fade" id="uploadfinalModal-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="uploadfinalModalLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('upload.final', ['id' => $doc->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadfinalModalLabel-{{ $doc->id }}">Upload Final</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="file_final">File</label>
                                <input type="file" class="form-control-file" id="file_final" name="file_final"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach


    {{-- edit dokumen modal --}}
    {{-- @foreach ($indukDokumenList as $doc)
        <div class="modal fade" id="editDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('edit.rule', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDokumenLabel">Edit Dokumen {{ ucfirst($tipe) }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nama_dokumen">Nama Dokumen</label>
                                <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="rule_id">Kode Proses</label>
                                <select class="form-control" id="rule_id" name="rule_id" required>
                                    <option value="">Pilih Kode Proses</option>
                                    @foreach ($kodeProses as $item)
                                        <option value="{{ $item['id'] }}">
                                            {{ $item['kode_proses'] }} - {{ $item['nama_proses'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Share Document To:</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        @foreach ($departemens as $dept)
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="dept_{{ $dept->id }}" name="departemen[]"
                                                        value="{{ $dept->id }}">
                                                    <label class="form-check-label"
                                                        for="dept_{{ $dept->id }}">{{ $dept->nama_departemen }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="file">File</label>
                                <input type="file" class="form-control" id="file" name="file_draft" required>
                            </div>

                            <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                            <input type="hidden" name="tipe_dokumen" value="{{ $tipe }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach --}}
    <script>
        document.getElementById('status_dokumen').addEventListener('change', function() {
            var revisiGroup = document.getElementById('revisi_ke_group');
            if (this.value === 'revisi') {
                revisiGroup.style.display = 'block';
                document.getElementById('revisi_ke').required = true;
            } else {
                revisiGroup.style.display = 'none';
                document.getElementById('revisi_ke').required = false;
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Dapatkan elemen checkbox "Select All"
            const selectAllCheckbox = document.getElementById('select_all');

            // Dapatkan semua elemen checkbox yang lainnya
            const checkboxes = document.querySelectorAll('input[name="departemen[]"]');

            // Tambahkan event listener ke checkbox "Select All"
            selectAllCheckbox.addEventListener('change', function() {
                // Atur status semua checkbox berdasarkan status checkbox "Select All"
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            // Tambahkan event listener ke setiap checkbox lainnya
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Jika ada satu checkbox yang tidak dipilih, hapus centang dari "Select All"
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    }

                    // Jika semua checkbox lainnya dipilih, beri centang pada "Select All"
                    if (document.querySelectorAll('input[name="departemen[]"]:checked').length ===
                        checkboxes.length) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
        });
    </script>

@endsection
