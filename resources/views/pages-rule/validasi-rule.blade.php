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
                                    @php
                                        $filteredDocs = $indukDokumenList->filter(function ($doc) {
                                            return in_array($doc->status, [
                                                'waiting approval',
                                                'waiting final approval',
                                            ]);
                                        });
                                    @endphp

                                    @forelse ($filteredDocs as $doc)
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

                                                @if ($doc->status == 'waiting approval')
                                                    <!-- Tombol Download Draft -->
                                                    <a href="{{ route('download.draft', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-file-download"></i> Download
                                                    </a>
                                                    <!-- Tombol Approve Draft -->
                                                    <button class="btn btn-success btn-sm" data-toggle="modal"
                                                        data-target="#approveDraftDokumen-{{ $doc->id }}">
                                                        <i class="fa-solid fa-check"></i> Approve
                                                    </button>
                                                @elseif ($doc->status == 'waiting final approval')
                                                    <!-- Tombol Download Final Document -->
                                                    <a href="{{ route('download.doc.final', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-file-download"></i> Download
                                                    </a>
                                                    <!-- Tombol Approve Document -->
                                                    <button class="btn btn-success btn-sm" data-toggle="modal"
                                                        data-target="#approveDokumen-{{ $doc->id }}">
                                                        <i class="fa-solid fa-check"></i> Approve
                                                    </button>
                                                    <!-- Tombol Reject Document -->
                                                    <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                        data-target="#rejectDokumen-{{ $doc->id }}">
                                                        <i class="fa-solid fa-circle-xmark"></i> Reject
                                                    </button>
                                                @else
                                                    <p class="text-muted">No data available</p>
                                                @endif

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No data available</td>
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
    @foreach ($indukDokumenList as $doc)
        <div class="modal fade" id="approveDraftDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="approveDraftDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('dokumen.approve', ['id' => $doc->id]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveDraftDokumenLabel-{{ $doc->id }}">Document Confirmation
                                Approved
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <input type="text" class="form-control" id="comment" name="comment" required>
                            </div>
                            <div class="form-group">
                                <label for="file">File (optional)</label>
                                <input type="file" class="form-control-file" id="file" name="file_draft">
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
        <div class="modal fade" id="approveDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
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
                        <form action="{{ route('final.approve', ['id' => $doc->id]) }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-circle-check"></i> Approve
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="rejectDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="rejectDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectDokumenLabel-{{ $doc->id }}">Konfirmasi Approve Dokumen
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <input type="text" class="form-control" id="comment" name="comment" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <form action="{{ route('final.reject', ['id' => $doc->id]) }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-circle-check"></i> Approve
                            </button>
                        </form>
                    </div>
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
