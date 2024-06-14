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
                                                    <form action="{{ route('dokumen.approve', ['id' => $doc->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check-circle"></i> 
                                                        </button>
                                                    </form>

                                                    <!-- Tombol Reject -->
                                                    <form action="{{ route('dokumen.rejected', ['id' => $doc->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times-circle"></i> w
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
@endsection
