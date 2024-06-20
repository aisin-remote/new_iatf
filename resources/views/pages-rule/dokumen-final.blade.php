@extends('layouts.app')
@section('title', 'Dokumen-Iatf')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dokumen Final</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Nama Dokumen</th>
                                        <th>Upload By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dokumenfinal as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_dokumen }}</td>
                                            <td>{{ $doc->nama_dokumen }}</td>
                                            <td>{{ $doc->user->departemen->nama_departemen }}</td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                {{-- <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editDokumen-{{ $doc->id }}">
                                                    Edit
                                                    <i class="fa-solid fa-edit"></i>
                                                </button> --}}

                                                <!-- Tombol Download -->
                                                <a href="{{ route('download.final', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                                                    class="btn btn-primary btn-sm">
                                                    Download
                                                    <i class="fa-solid fa-file-arrow-down"></i>
                                                </a>
                                                {{-- @if ($doc->status == 'approved')
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                        data-target="#uploadfinalModal-{{ $doc->id }}">
                                                        Upload Final
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary btn-sm" disabled>
                                                        Upload Final
                                                    </button>
                                                @endif --}}
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
