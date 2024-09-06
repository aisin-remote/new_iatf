@extends('layouts.app')

@section('title', 'Audit Control')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Audit Control</h4>
                        <div class="row mb-3">
                            <!-- Kolom untuk input pencarian dan tombol filter -->
                            <div class="col-md-12 d-flex justify-content-end">
                                <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                    placeholder="Search..." style="width: 250px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#auditfilterModal"
                                    style="background: #56544B">
                                    Filter
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Audit Name</th>
                                        <th>Item</th>
                                        <th>Departemen</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($AuditControls as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->itemAudit->audit->nama }}</td>
                                            <td>{{ $d->itemAudit->nama_item }}</td>
                                            <td>{{ $d->departemen->nama_departemen }}</td>
                                            <td>
                                                {{-- Menampilkan file yang sudah di-upload --}}
                                                @if ($d->documentAudit->count())
                                                    <div class="mb-2">
                                                        @foreach ($d->documentAudit as $document)
                                                            <div class="d-flex align-items-center mb-2">
                                                                <a href="{{ asset('storage/' . $document->attachment) }}"
                                                                    class="btn btn-info btn-sm" download>
                                                                    {{ preg_replace('/^\d+-/', '', basename($document->attachment)) }}
                                                                </a>

                                                                {{-- Tombol hapus untuk setiap file yang di-upload --}}
                                                                <form
                                                                    action="{{ route('deleteDocumentAudit', $document->id) }}"
                                                                    method="POST"
                                                                    style="display:inline-block; margin-left: 10px;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Are you sure you want to delete this document?')">Delete</button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p>No document uploaded yet.</p>
                                                @endif

                                                {{-- Form upload untuk upload file baru di bawah file yang sudah ada --}}
                                                <form action="{{ route('uploadDocumentAudit', $d->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <label for="attachments">Upload New File:</label>
                                                    <input type="file" name="attachments" id="attachments" required>
                                                    <br><button type="submit"
                                                        class="btn btn-primary btn-sm mt-2">Upload</button></br>
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

    {{-- Modal Add Template --}}
    <div class="modal fade" id="addauditcontrol" tabindex="-1" role="dialog" aria-labelledby="addauditcontrolLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addauditcontrolLabel">Add Audit Control</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('add.auditControl') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="item_audit_id">Audit</label>
                            <select name="item_audit_id" id="item_audit_id" class="form-control select2"
                                style="width: 100%;">
                                <option value="" selected>Select item</option>
                                @foreach ($itemaudit as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_item }} - {{ $d->audit->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Select Department</label>
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
                                                <input class="form-check-input kode_departemen_checkbox" type="checkbox"
                                                    id="dept_{{ $dept->id }}" name="departemen[]"
                                                    value="{{ $dept->id }}">
                                                <label class="form-check-label"
                                                    for="dept_{{ $dept->id }}">{{ $dept->nama_departemen }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal Edit Template --}}
    @foreach ($AuditControls as $d)
        <div class="modal fade" id="editauditcontrolcontrol-{{ $d->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editauditcontrolcontrolLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editauditcontrolcontrolLabel">Update Audit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('update.auditControl', ['id' => $d->id]) }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nama">Audit Name</label>
                                <input type="text" class="form-control" id="nama" name="nama"
                                    value="{{ old('nama', $d->nama) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_audit">Audit Date</label>
                                <input type="text" class="form-control" id="tanggal_audit" name="tanggal_audit"
                                    value="{{ old('tanggal_audit', $d->tanggal_audit) }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- <div class="modal fade" id="auditfilterModal" tabindex="-1" role="dialog"
            aria-labelledby="auditfilterModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('auditControl') }}" method="GET">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="auditfilterModalLabel">Filter <i class="fa-solid fa-filter"></i>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Filter berdasarkan Tanggal Upload -->
                            <div class="row my-2">
                                <div class="col-4">
                                    <label class="col-form-label">Document</label>
                                </div>
                                <div class="col">
                                    <select name="document_item_id" class="form-control select2">
                                        <option value="">Select Document Audit</option>
                                        @foreach ($documentAudits as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_dokumen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4">
                                    <label class="col-form-label">Audit</label>
                                </div>
                                <div class="col">
                                    <select name="audit_id" class="form-control select2">
                                        <option value="">Select Audit</option>
                                        @foreach ($audit as $a)
                                            <option value="{{ $a->id }}">{{ $a->nama }}</option>
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
        </div> --}}
    @endforeach
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Dapatkan elemen checkbox "Select All"
            const selectAllCheckbox = document.getElementById('select_all');

            // Dapatkan semua elemen checkbox departemen
            const checkboxes = document.querySelectorAll('input[name="departemen[]"]');

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
                    if (document.querySelectorAll('input[name="departemen[]"]:checked')
                        .length === checkboxes.length) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
        });
    </script>
@endsection
