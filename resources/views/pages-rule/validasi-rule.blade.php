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
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($indukDokumenList as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_dokumen }}</td>
                                            <td>{{ $doc->nama_dokumen }}</td>
                                            <td>{{ $doc->revisi_log }}</td>
                                            <td>{{ $doc->tgl_upload }}</td>
                                            <td>{{ $doc->status }}</td>
                                            <!-- Tombol Edit -->
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Actions">
                                                    <!-- Tombol Download -->
                                                    <a href="{{ route('download.rule', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fas fa-file-download"></i>
                                                    </a>

                                                    <!-- Tombol Approval -->
                                                    <button class="btn btn-success btn-sm" data-toggle="modal"
                                                        data-target="#approveDokumen-{{ $doc->id }}">

                                                        <i class="fa-solid fa-circle-check"></i>
                                                    </button>
                                                    <!-- Tombol Reject -->
                                                    <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                        data-target="#rejectDokumen-{{ $doc->id }}">

                                                        <i class="fa-solid fa-circle-xmark"></i>
                                                    </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach ($indukDokumenList as $doc)
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
        </div>
        <div class="modal fade" id="rejectDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="rejectDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('dokumen.rejected', ['id' => $doc->id]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectDokumenLabel-{{ $doc->id }}">Konfirmasi Rejected Dokumen
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="dommand">Command</label>
                                <input type="text" class="form-control" id="command" name="command" required>
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
        // Tangkap form submit event
        $('form').submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Tampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi Approve Dokumen',
                text: "Apakah Anda yakin ingin menyetujui dokumen ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Approve'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lanjutkan submit form jika user menekan Approve di SweetAlert
                    $(this).unbind('submit').submit(); // Unbind submit event handler and submit form
                }
            });
        });
    </script>
@endsection
