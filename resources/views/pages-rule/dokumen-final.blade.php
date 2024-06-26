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
                                        <th>Status Doc</th>
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
                                            <td>{{ $doc->statusdoc }}</td>
                                            <td>
                                                <!-- Jika status dokumen "active", tampilkan tombol untuk mengubah status menjadi "obsolate" -->
                                                @if ($doc->statusdoc === 'active')
                                                    <form action="{{ route('update.statusdoc', ['id' => $doc->id]) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="statusdoc" value="obsolate">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            Obsolate <i class="fa-solid fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Jika status dokumen "obsolate", tampilkan tombol untuk mengubah status menjadi "active" -->
                                                @if ($doc->statusdoc === 'obsolate')
                                                    <form action="{{ route('update.statusdoc', ['id' => $doc->id]) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="statusdoc" value="active">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            Active <i class="fa-solid fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

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
