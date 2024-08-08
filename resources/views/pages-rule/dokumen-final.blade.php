@extends('layouts.app')
@section('title', 'Dokumen Final')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Final Document {{ ucfirst($jenis) }} - {{ ucfirst($tipe) }}</h4>
                        <div class="container">
                            <div class="row mb-3">
                                <!-- Kolom untuk tombol Upload Old Documents -->
                                <div class="col-md-6 d-flex align-items-center">
                                    @role('admin')
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadoldDoc">
                                            Upload Old Documents
                                        </button>
                                    @endrole
                                </div>

                                <!-- Kolom untuk input pencarian dan tombol filter -->
                                <div class="col-md-6 d-flex justify-content-end align-items-center">
                                    <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                        placeholder="Search..." style="width: 300px;">
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filterModal"
                                        style="background: #56544B">
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Document Number</th>
                                        <th>Document Title</th>
                                        <th>Upload By</th>
                                        <th>Status</th>
                                        @if (
                                            $dokumenfinal->contains(function ($doc) {
                                                return is_null($doc->file_pdf);
                                            }))
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dokumenfinal as $doc)
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
                                                @if (is_null($doc->file_pdf))
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                        data-target="#uploadFinalModal-{{ $doc->id }}">
                                                        Upload Final
                                                    </button>
                                                @elseif ($doc->statusdoc == 'active' && $doc->active_doc)
                                                    @php
                                                        // Menggunakan nama file yang disimpan di kolom active_doc
                                                        $fileUrl = asset('storage/' . $doc->active_doc);
                                                    @endphp
                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                @elseif ($doc->statusdoc == 'obsolete' && $doc->obsolete_doc)
                                                    @php
                                                        // Menggunakan nama file yang disimpan di kolom obsolete_doc
                                                        $fileUrl = asset('storage/' . $doc->obsolete_doc);
                                                    @endphp
                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                @elseif ($doc->file_pdf)
                                                    @php
                                                        // Menggunakan nama file yang disimpan di kolom file_pdf
                                                        $fileUrl = asset('storage/' . $doc->file_pdf);
                                                    @endphp
                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                @endif

                                                @role('admin')
                                                    <!-- Tombol untuk mengaktifkan dokumen -->
                                                    @if ($doc->file_pdf && ($doc->statusdoc == 'not yet active' || $doc->statusdoc == 'obsolete'))
                                                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                            data-target="#activateDokumen-{{ $doc->id }}">
                                                            activate
                                                        </button>
                                                    @else
                                                        <button class="btn btn-primary btn-sm" disabled>
                                                            activate
                                                        </button>
                                                    @endif
                                                    <!-- Tombol untuk mengobsoletkan dokumen -->
                                                    @if ($doc->file_pdf && ($doc->statusdoc == 'active' || $doc->statusdoc == 'not yet active'))
                                                        <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                            data-target="#obsolateDokumen-{{ $doc->id }}">
                                                            obsolate
                                                        </button>
                                                    @else
                                                        <button class="btn btn-danger btn-sm" disabled>
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
        <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('filter.documents') }}" method="GET">
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
                                <div class="col-3">
                                    <input type="date" name="date_from" class="form-control input"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col text-center">
                                    <label class="col-form-label">to</label>
                                </div>
                                <div class="col-3">
                                    <input type="date" name="date_to" class="form-control input"
                                        value="{{ request('date_to') }}">
                                </div>
                            </div>

                            <!-- Filter berdasarkan Departemen (Hanya untuk admin) -->
                            @role('admin')
                                <div class="row my-2">
                                    <div class="col-4">
                                        <label class="col-form-label">Department</label>
                                    </div>
                                    <div class="col">
                                        <select name="departemen" id="departemen" class="form-control select2"
                                            style="width: 100%;">
                                            <option value="" selected>Select Department</option>
                                            <option value="Aisin Indonesia"
                                                {{ request('departemen_id') == 'i' ? 'selected' : '' }}>
                                                Aisin Indonesia</option>
                                            <option value="Quality Body"
                                                {{ request('departemen_id') == '2' ? 'selected' : '' }}>
                                                Quality Body</option>
                                            <option value="Quality Unit"
                                                {{ request('departemen_id') == '3' ? 'selected' : '' }}>
                                                Quality Unit</option>
                                            <option value="Quality Electric"
                                                {{ request('departemen_id') == '4' ? 'selected' : '' }}>
                                                Quality Electric</option>
                                            <option value="PPIC Receiving"
                                                {{ request('departemen_id') == '5' ? 'selected' : '' }}>
                                                PPIC Receiving</option>
                                            <option value="PPIC Delivery"
                                                {{ request('departemen_id') == '6' ? 'selected' : '' }}>
                                                PPIC Delivery</option>
                                            <option value="PPIC Electric"
                                                {{ request('departemen_id') == '7' ? 'selected' : '' }}>
                                                PPIC Electric</option>
                                            <option value="Engineering Body"
                                                {{ request('departemen_id') == '8' ? 'selected' : '' }}>
                                                Engineering Body</option>
                                            <option value="Engineering Unit"
                                                {{ request('departemen_id') == '9' ? 'selected' : '' }}>
                                                Engineering Unit</option>
                                            <option value="Engineering Electric"
                                                {{ request('departemen_id') == '10' ? 'selected' : '' }}>
                                                Engineering Electric</option>
                                            <option value="Maintenance"
                                                {{ request('departemen_id') == '11' ? 'selected' : '' }}>
                                                Maintenance</option>
                                            <option value="Maintenance Electric"
                                                {{ request('departemen_id') == '12' ? 'selected' : '' }}>
                                                Maintenance Electric</option>
                                            <option value="Production Unit"
                                                {{ request('departemen_id') == '13' ? 'selected' : '' }}>
                                                Production Unit</option>
                                            <option value="Production Body"
                                                {{ request('departemen_id') == '14' ? 'selected' : '' }}>
                                                Production Body</option>
                                            <option value="Production Electric"
                                                {{ request('departemen_id') == '15' ? 'selected' : '' }}>
                                                Production Electric</option>
                                            <option value="Production System Development"
                                                {{ request('departemen_id') == '16' ? 'selected' : '' }}>
                                                Production System Development</option>
                                            <option value="IT Development"
                                                {{ request('departemen_id') == '17' ? 'selected' : '' }}>
                                                IT Development</option>
                                            <option value="Management System"
                                                {{ request('departemen_id') == '18' ? 'selected' : '' }}>
                                                Management System</option>
                                            <option value="Management Representative"
                                                {{ request('departemen_id') == '19' ? 'selected' : '' }}>
                                                Management Representative</option>
                                        </select>
                                    </div>
                                </div>
                            @endrole

                            <!-- Filter berdasarkan Status Dokumen -->
                            <div class="row my-2">
                                <div class="col-4">
                                    <label class="col-form-label">Document Status</label>
                                </div>
                                <div class="col">
                                    <select name="statusdoc" id="statusdoc" class="form-control select2"
                                        style="width: 100%;">
                                        <option value="" selected>Select Document Status</option>
                                        <option value="active" {{ request('statusdoc') == 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="obsolete"
                                            {{ request('statusdoc') == 'obsolete' ? 'selected' : '' }}>
                                            Obsolete</option>
                                        <option value="not yet active"
                                            {{ request('statusdoc') == 'not yet active' ? 'selected' : '' }}>
                                            Not Yet Active</option>
                                        <!-- Tambahkan opsi status lain jika diperlukan -->
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
        <!-- Modal Upload Old Documents -->
        <div class="modal fade" id="uploadoldDoc" tabindex="-1" role="dialog" aria-labelledby="uploaddraftModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('add.oldDoc') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploaddraftModalLabel">Upload Old Documents</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Form untuk Upload Draft -->
                            <div class="form-group">
                                <label for="nama_dokumen">Document Title</label>
                                <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="department">Department</label>
                                <select class="form-control" id="department" name="department" required>
                                    <option value="">Select Department</option>
                                    @foreach ($alldepartmens as $d)
                                        <option value="{{ $d['id'] }}">
                                            {{ $d->nama_departemen }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nomor_list">Number Document</label>
                                <input type="text" class="form-control" id="nomor_list" name="nomor_list" required>
                            </div>
                            <div class="form-group">
                                <label for="status_dokumen">Document Status</label>
                                <select class="form-control" id="status_dokumen" name="status_dokumen" required>
                                    <option value="">Select Document Status</option>
                                    <option value="baru">New</option>
                                    <option value="revisi">Revision</option>
                                </select>
                            </div>
                            <div class="form-group" id="revisi_ke_group" style="display: none;">
                                <label for="revisi_ke">Revision Number</label>
                                <input type="number" class="form-control" id="revisi_ke" name="revisi_ke">
                            </div>

                            <div class="form-group">
                                <label for="rule_id">Process Code</label>
                                <select class="form-control" id="rule_id" name="rule_id" required>
                                    <option value="">Pilih Kode Proses</option>
                                    @foreach ($kodeProses as $item)
                                        <option value="{{ $item['id'] }}">
                                            {{ $item['kode_proses'] }} - {{ $item['nama_proses'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Share Document To:</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select_all">
                                                <label class="form-check-label" for="select_all">Select All</label>
                                            </div>
                                        </div>
                                        @foreach ($uniqueDepartemens as $dept)
                                            <div class="col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="dept_{{ $dept->code }}" name="kode_departemen[]"
                                                        value="{{ $dept->code }}">
                                                    <label class="form-check-label"
                                                        for="dept_{{ $dept->code }}">{{ $dept->code }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="file">Choose File</label>
                                <input type="file" class="form-control" id="file" name="file" required>
                            </div>
                            <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                            <input type="hidden" name="tipe_dokumen" value="{{ $tipe }}">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Upload</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Upload Final Document -->
        @foreach ($dokumenfinal as $doc)
            <div class="modal fade" id="uploadFinalModal-{{ $doc->id }}" tabindex="-1" role="dialog"
                aria-labelledby="uploadFinalModalLabel-{{ $doc->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ route('upload.final', $doc->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadFinalModalLabel-{{ $doc->id }}">Upload Final
                                    Document
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="file">Choose File</label>
                                    <input type="file" class="form-control" id="file" name="file" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Upload</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach ($dokumenfinal as $doc)
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
                                <div class="form-group">
                                    <label for="tgl_efektif">Effective Date</label>
                                    <input type="date" class="form-control" id="tgl_efektif" name="tgl_efektif"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Activate</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Modal untuk obsolate dokumen -->
        @foreach ($dokumenfinal as $doc)
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
                                <div class="form-group">
                                    <label for="tgl_obsolete">Obsolete Date</label>
                                    <input type="date" class="form-control" id="tgl_obsolete" name="tgl_obsolete"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Obsolate</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.getElementById('status_dokumen').addEventListener('change', function() {
                var revisiGroup = document.getElementById('revisi_ke_group');
                if (this.value === 'revisi') {
                    revisiGroup.style.display = 'block';
                    document.getElementById('revisi_ke').required = true;
                } else {
                    revisiGroup.style.display = 'none';
                    document.getElementById('revisi_ke').required = false;
                }
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Dapatkan elemen checkbox "Select All"
                const selectAllCheckbox = document.getElementById('select_all');

                // Dapatkan semua elemen checkbox departemen
                const checkboxes = document.querySelectorAll('input[name="kode_departemen[]"]');

                // Tambahkan event listener untuk checkbox "Select All"
                selectAllCheckbox.addEventListener('change', function() {
                    // Set status semua checkbox departemen sesuai dengan status checkbox "Select All"
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                });

                // Tambahkan event listener untuk setiap checkbox departemen
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        // Jika ada satu checkbox yang tidak dipilih, hapus centang dari "Select All"
                        if (!this.checked) {
                            selectAllCheckbox.checked = false;
                        }

                        // Jika semua checkbox departemen dipilih, beri centang pada "Select All"
                        if (document.querySelectorAll('input[name="kode_departemen[]"]:checked')
                            .length === checkboxes.length) {
                            selectAllCheckbox.checked = true;
                        }
                    });
                });
            });
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
