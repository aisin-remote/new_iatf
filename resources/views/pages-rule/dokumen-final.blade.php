@extends('layouts.app')
@section('title', 'Dokumen Final')
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
                                        <th>Status</th>
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
                                                <a href="{{ route('preview-download.final', ['id' => $doc->id, 'preview' => true]) }}"
                                                    class="btn btn-info btn-sm" target="_blank">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                @role('admin')
                                                    <!-- Tombol untuk mengaktifkan dokumen -->
                                                    @if ($doc->statusdoc == 'not yet active' || $doc->statusdoc == 'obsolate')
                                                        <form action="{{ route('activate.document', ['id' => $doc->id]) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm">Activate</button>
                                                        </form>
                                                    @endif
                                                    <!-- Tombol untuk mengobsoletkan dokumen -->
                                                    @if ($doc->statusdoc == 'active' || $doc->statusdoc == 'not yet active')
                                                        <form action="{{ route('obsolete.document', ['id' => $doc->id]) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit"
                                                                class="btn btn-danger btn-sm">Obsolate</button>
                                                        </form>
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
@endsection
