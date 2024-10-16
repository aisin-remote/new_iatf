@extends('layouts.app')
@section('title', 'Dokumen Final')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dokumen Share {{ ucfirst($jenis) }} - {{ ucfirst($tipe) }}</h4>
                        <div class="d-flex justify-content-end mb-3">
                            <!-- Input pencarian -->
                            <input type="text" class="form-control form-control-sm w-25 mr-2" id="searchInput"
                                placeholder="Search...">

                            <!-- Tombol Filter -->
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filterModal"
                                style="background: #56544B">
                                Filter
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Document Number</th>
                                        <th>Document Title</th>
                                        <th>Upload By</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sharedDocuments as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_dokumen }}</td>
                                            <td>{{ $doc->nama_dokumen }}</td>
                                            <td>
                                                @if ($doc->user_id)
                                                    {{ $doc->user->departemen->nama_departemen }}
                                                @else
                                                    {{ $doc->departemen->nama_departemen }}
                                                @endif
                                            </td>
                                            <td>{{ $doc->statusdoc }}</td>
                                            <td>
                                                @if ($doc->statusdoc == 'active' && $doc->active_doc)
                                                    @php
                                                        // Menggunakan nama file yang disimpan di kolom active_doc
                                                        $previewUrl = route('documents.preview', ['id' => $doc->id]);
                                                    @endphp
                                                    <a href="{{ $previewUrl }}" target="_blank"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                @endif
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
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('document.share', ['jenis' => $jenis, 'tipe' => $tipe]) }}" method="GET">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter Documents</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row my-2">
                            <div class="col-4">
                                <label class="col-form-label">Start Date / Upload Date</label>
                            </div>
                            <div class="col">
                                <input type="text" name="date_from" class="form-control input" placeholder="From">
                            </div>
                            <label class="col-form-label px-3">to</label>
                            <div class="col">
                                <input type="text" name="date_to" class="form-control input" placeholder="To">
                            </div>
                        </div>
                        <div class="row my-2">
                            <div class="col-4">
                                <label class="col-form-label">Departemen</label>
                            </div>
                            <div class="col">
                                <select name="departemen_id" id="departemen_id" class="form-control select2"
                                    style="width: 100%;">
                                    <option value="" selected>Select Departemen</option>
                                    @foreach ($departments as $departemen)
                                        @if (
                                            !in_array($departemen->nama_departemen, [
                                                'Marketing (AII)',
                                                'Human Resource Development (AII)',
                                                'Purchasing Group (AII)',
                                                'IRL-GA (AII)',
                                            ]))
                                            <option value="{{ $departemen->id }}"
                                                {{ request('departemen_id') == $departemen->id ? 'selected' : '' }}>
                                                {{ $departemen->nama_departemen }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
