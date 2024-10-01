@extends('layouts.app')
@section('title', 'Dokumen-Iatf')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Document {{ ucfirst($jenis) }} - Type: {{ ucfirst($tipe) }}</h4>
                        <div class="d-flex justify-content-end mb-3">
                            <!-- Input pencarian -->
                            <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                placeholder="Search..." style="width: 300px;">
                            <!-- Tombol Filter -->
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filterModal"
                                style="background: #56544B">
                                Filter
                            </button>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger" id="error-alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Dcocument Number</th>
                                        <th>Document Title</th>
                                        <th>Revision</th>
                                        <th>Upload Date</th>
                                        <th>Upload By</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $filteredDocs = $indukDokumenList->filter(function ($doc) {
                                            return in_array($doc->status, ['Waiting check by MS']);
                                        });
                                    @endphp

                                    @forelse ($filteredDocs as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_dokumen }}</td>
                                            <td>{{ $doc->nama_dokumen }}</td>
                                            <td>{{ $doc->revisi_log }}</td>
                                            <td>{{ $doc->tgl_upload }}</td>
                                            <td>{{ $doc->user->departemen->nama_departemen }}</td>
                                            <td>{{ $doc->status }}</td>
                                            <!-- Tombol Edit -->
                                            <td>
                                                @if ($doc->status == 'Waiting check by MS' || $doc->status == 'Finish check by MS')
                                                    <!-- Tombol Download Draft -->
                                                    <a href="{{ route('download.rule', ['id' => $doc->id]) }}"
                                                        class="btn btn-success btn-sm" target="_blank">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                    <!-- Tombol Approve Draft -->
                                                    <button class="btn btn-success btn-sm" data-toggle="modal"
                                                        data-target="#feedbackDokumen-{{ $doc->id }}">
                                                        <i class="fa-solid fa-comment"></i> Feedback
                                                    </button>
                                                @else
                                                    <p class="text-muted">No data available</p>
                                                @endif

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No data available</td>
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
                <form action="{{ route('filter.documents') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter <i class="fa-solid fa-filter"></i></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Filter berdasarkan Tanggal Upload -->
                        <div class="row my-2">
                            <div class="col-4">
                                <label class="col-form-label">Start Date</label>
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
                                <label class="col-form-label">Department</label>
                            </div>
                            <div class="col">
                                <select name="departemen_id" id="departemen_id" class="form-control select2"
                                    style="width: 100%;">
                                    <option value="" selected>Select Department</option>
                                    @foreach ($allDepartemen as $departemen)
                                        <option value="{{ $departemen->nama_departemen }}"
                                            {{ request('departemen_id') == $departemen->nama_departemen ? 'selected' : '' }}>
                                            {{ $departemen->nama_departemen }}
                                        </option>
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
    @foreach ($indukDokumenList as $doc)
        <div class="modal fade" id="feedbackDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="feedbackDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('dokumen.approve', ['id' => $doc->id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="feedbackDokumenLabel-{{ $doc->id }}">Document Confirmation
                                Approved
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <input type="text" class="form-control" id="comment" name="comment" required>
                            </div>
                            <div class="form-group">
                                <label for="file">File (.word, .excel) <small>(Optional)</small></label>
                                <input type="file" class="form-control-file" id="file" name="file">
                                <p>Maks 20 mb</p>
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
        <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('rule.validate', ['jenis' => $jenis, 'tipe' => $tipe]) }}" method="GET">
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
                                    <input type="text" name="date_from" class="form-control input"
                                        placeholder="From">
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
                                    <select name="departemen" id="departemen_id" class="form-control select2"
                                        style="width: 100%;">
                                        <option value="" selected>Select Departemen</option>
                                        @foreach ($allDepartemen as $departemen)
                                            <option value="{{ $departemen->id }}"
                                                {{ request('departemen_id') == $departemen->id ? 'selected' : '' }}>
                                                {{ $departemen->nama_departemen }}
                                            </option>
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
    @endforeach
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        setTimeout(function() {
            document.getElementById('error-alert')?.remove();
        }, 3000);
    </script>
    <script>
        $(document).ready(function() {
            // Event handler untuk pencarian
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#documentTableBody tr').each(function() {
                    var row = $(this);
                    var text = row.text().toLowerCase();
                    row.toggle(text.indexOf(value) > -1);
                });
            });

            // Menghandle pagination agar pencarian bekerja
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $.get(url, function(data) {
                    $('#documentTableBody').html($(data).find('#documentTableBody').html());
                });
            });
        });
    </script>
@endsection
