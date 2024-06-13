@extends('layouts.app')
@section('title', 'Dokumen-Iatf')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dokumen {{ ucfirst($jenis) }} - Tipe: {{ ucfirst($tipe) }}</h4>
                        <a class="btn btn-primary btn-sm"
                            href="{{ route('dokumen.download', ['jenis' => $jenis, 'tipe' => $tipe]) }}">
                            Download
                            <i class="fa-solid fa-file-arrow-down"></i>
                        </a>

                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploaddokumen"
                            style="margin-left: 4px">
                            Upload
                        </button>
                        <p class="card-description"></p>
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-sm" style="background: #808080; color: white;" data-toggle="modal"
                                data-target="#filterModal">
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
                                        <th>status</th>
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
                                            <td>
                                                <!-- Tombol Edit -->
                                                <a href="{{ route('download.rule', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                                                    class="btn btn-primary btn-sm">
                                                    Download
                                                    <i class="fa-solid fa-file-arrow-down"></i>
                                                </a>

                                                <!-- Tombol Approval -->
                                                <form action="{{ route('dokumen.approve', ['id' => $doc->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-success btn-sm">Approve <i
                                                            class="fa-solid fa-circle-check"></i></button>
                                                </form>

                                                <!-- Tombol Reject -->
                                                <form action="{{ route('dokumen.rejected', ['id' => $doc->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="submit" class="btn btn-danger btn-sm">Reject <i
                                                            class="fa-solid fa-circle-xmark"></i></button>
                                                </form>
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

    <!-- Modal untuk mengunggah file -->
    <div class="modal fade" id="uploaddokumen" tabindex="-1" role="dialog" aria-labelledby="uploaddokumenLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploaddokumenLabel">Upload Template {{ $tipe }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dokumen.upload', ['jenis' => $jenis, 'tipe' => $tipe]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">Pilih File</label>
                            <input type="file" class="form-control-file" id="file" name="file" required>
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
@endsection
