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
                                        <th>Status</th>
                                        @role('admin')
                                            <th>Action</th>
                                        @endrole
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
                                                @role('admin')
                                                    @if ($doc->statusdoc == 'not yet active')
                                                        <!-- Jika status_doc adalah "belum aktif" -->
                                                        <a href="{{ route('dokumen.update', ['id' => $doc->id, 'action' => 'activate']) }}"
                                                            class="btn btn-primary btn-sm">Activate</a>
                                                        <a href="{{ route('dokumen.update', ['id' => $doc->id, 'action' => 'obsolate']) }}"
                                                            class="btn btn-danger btn-sm">Obsolate</a>
                                                    @elseif ($doc->statusdoc == 'active')
                                                        <!-- Jika statusdoc adalah "active" -->
                                                        <a href="{{ route('dokumen.update', ['id' => $doc->id, 'action' => 'obsolate    ']) }}"
                                                            class="btn btn-danger btn-sm">Obsolate</a>
                                                    @elseif ($doc->statusdoc == 'obsolate')
                                                        <!-- Jika statusdoc adalah "obsolate" -->
                                                        <a href="{{ route('dokumen.update', ['id' => $doc->id, 'action' => 'activate']) }}"
                                                            class="btn btn-primary btn-sm">Activate</a>
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
