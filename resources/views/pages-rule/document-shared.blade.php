@extends('layouts.app')
@section('title', 'Shared Document')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dokumen Shared {{ ucfirst($jenis) }} - Tipe: {{ ucfirst($tipe) }}</h4>
                        <div class="d-flex justify-content-end mb-3">
                            <!-- Input pencarian -->
                            <input type="text" class="form-control form-control-sm w-25 mr-2" id="searchInput"
                                placeholder="Search...">

                            <!-- Tombol Filter -->
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploaddraftModal" style="background: #56544B">
                                Filter
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Judul Dokumen</th>
                                        <th>Upload By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sharedDocuments as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_dokumen }}</td>
                                            <td>{{ $doc->nama_dokumen }}</td>
                                            <td>{{ $doc->user->departemen->nama_departemen }}</td>
                                            <td>
                                                <!-- Tombol Download -->
                                                <a href="{{ route('previewAndDownload.share', ['id' => $doc->id]) }}"
                                                    class="btn btn-primary btn-sm" target="blank">
                                                    Download <i class="fa-solid fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No data available</td>
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
@endsection
