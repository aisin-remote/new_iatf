@extends('layouts.app')
@section('title', 'Dokumen-Iatf')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dokumen {{ ucfirst($jenis) }} - Tipe: {{ ucfirst($tipe) }}</h4>
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#filterModal">
                                Filter
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Nama Dokumen</th>
                                        <th>Revisi</th>
                                        <th>Tanggal Upload</th>
                                        <th>Upload By</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($indukDokumenList->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center">No data available</td>
                                        </tr>
                                        @foreach ($indukDokumenList as $doc)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $doc->nomor_dokumen }}</td>
                                                <td>{{ $doc->nama_dokumen }}</td>
                                                <td>{{ $doc->revisi_log }}</td>
                                                <td>{{ $doc->tgl_upload }}</td>
                                                <td>{{ $doc->user->departemen->nama_departemen }}</td>
                                                <td>{{ $doc->status }}</td>
                                                <!-- Tombol Edit -->
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Actions">
                                                        <!-- Tombol Download -->
                                                        <a href="{{ route('download.draft', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="fas fa-file-download"></i>
                                                        </a>
                                                        <!-- Tombol approval -->
                                                        <button class="btn btn-success btn-sm" data-toggle="modal"
                                                            data-target="#approveDokumen-{{ $doc->id }}">
                                                            <i class="fa-solid fa-check"></i>
                                                        </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach ($indukDokumenList as $doc)
        {{-- <div class="modal fade" id="approveDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="approveDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveDokumenLabel-{{ $doc->id }}">Konfirmasi Approve Dokumen
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menyetujui dokumen ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <form action="{{ route('dokumen.approve', ['id' => $doc->id]) }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-circle-check"></i> Approve
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="modal fade" id="approveDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="approveDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('dokumen.approve', ['id' => $doc->id]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveDokumenLabel-{{ $doc->id }}">Document Confirmation
                                Approved
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="file_draft">Upload File Draft (Opsional)</label>
                                <input type="file" name="file_draft"
                                    class="form-control @error('file_draft') is-invalid @enderror">
                                @error('file_draft')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        function approveDocument(id) {
            if (confirm('Are you sure you want to approve this document?')) {
                fetch(`/document/approve/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        _method: 'POST'
                    })
                }).then(response => {
                    if (response.ok) {
                        document.getElementById(`document-${id}`).remove();
                        alert('Dokumen berhasil diapprove.');
                    } else {
                        alert('Gagal mengapprove dokumen.');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengapprove dokumen.');
                });
            }
        }
    </script>
@endsection
