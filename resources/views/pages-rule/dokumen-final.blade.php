@extends('layouts.app')
@section('title', 'Dokumen Final')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dokumen Final {{ ucfirst($jenis) }} - {{ ucfirst($tipe) }}</h4>
                        <div class="d-flex justify-content-end mb-3">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Nama Dokumen</th>
                                        <th>Upload By</th>
                                        <th>Status</th>
                                        <th>Pdf File</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dokumenfinal as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_dokumen }}</td>
                                            <td>{{ $doc->nama_dokumen }}</td>
                                            <td>{{ $doc->user->departemen->nama_departemen }}</td>
                                            <td>{{ $doc->statusdoc }}</td>
                                            <td>
                                                @if ($doc->file_pdf)
                                                    {{ basename($doc->file_pdf) }}
                                                @else
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                        data-target="#uploadFinalModal-{{ $doc->id }}">
                                                        Upload Final
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($doc->file_pdf)
                                                    <a href="{{ route('document.previewsAndDownload', ['id' => $doc->id]) }}"
                                                        class="btn btn-info btn-sm" target="_blank">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                @endif
                                                @role('admin')
                                                    <!-- Tombol untuk mengaktifkan dokumen -->
                                                    @if ($doc->statusdoc == 'not yet active' || $doc->statusdoc == 'obsolete')
                                                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                            data-target="#activateDokumen-{{ $doc->id }}">
                                                            activate
                                                        </button>
                                                    @endif
                                                    <!-- Tombol untuk mengobsoletkan dokumen -->
                                                    @if ($doc->statusdoc == 'active' || $doc->statusdoc == 'not yet active')
                                                        <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                            data-target="#obsolateDokumen-{{ $doc->id }}">
                                                            obsolate
                                                        </button>
                                                    @endif
                                                @endrole
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

    <!-- Modal -->
    @foreach ($dokumenfinal as $doc)
        <div class="modal fade" id="uploadFinalModal-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="uploadFinalModalLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('upload.final', ['id' => $doc->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadFinalModalLabel-{{ $doc->id }}">Upload Final Document
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="file">File (PDF only)</label>
                                <input type="file" class="form-control" id="file" name="file" required>
                                @if ($errors->has('file'))
                                    <div class="alert alert-danger mt-2">
                                        {{ $errors->first('file') }}
                                    </div>
                                @endif
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
        <div class="modal fade" id="activateDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="activateDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('activate.document', ['id' => $doc->id]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="activateDokumenLabel-{{ $doc->id }}">Activate Document
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="activationDate" class="form-label">Activation Date</label>
                                <input type="date" class="form-control" id="activationDate" name="activation_date"
                                    required>
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
        <div class="modal fade" id="obsolateDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="obsolateDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('obsolete.document', ['id' => $doc->id]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="obsolateDokumenLabel-{{ $doc->id }}">Obsolate Document
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="obsolatedDate" class="form-label">Obsolated Date</label>
                                <input type="date" class="form-control" id="obsolatedDate" name="obsoleted_date"
                                    required>
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
@endsection
