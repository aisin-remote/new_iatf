@extends('layouts.app')

@section('title', 'Audit Control')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Audit Control</h4>
                        {{-- <div class="row mb-3"> --}}
                        <!-- Kolom untuk tombol Upload Old Documents -->
                        <div class="d-flex justify-content-end mb-3">
                            @role('admin')
                                {{-- <div class="col-md-6 d-flex justify-content-end align-items-center"> --}}
                                <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                    placeholder="Search..." style="width: 300px;">
                                <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#auditfilterModal"
                                    style="background: #56544B;">
                                    Filter
                                </button>
                                {{-- </div> --}}
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addauditcontrol">
                                    Add New
                                </button>
                            @endrole
                        </div>
                        {{-- </div> --}}

                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Audit Name</th>
                                        <th>Item</th>
                                        <th>Departemen</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($AuditControls as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->audit->nama }}</td>
                                            <td>{{ $d->itemAudit->nama_item }}</td>
                                            <td>{{ $d->departemen->nama_departemen }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editauditcontrol-{{ $d->id }}">
                                                    Edit
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                    data-target="#deleteaudit-{{ $d->id }}">
                                                    Delete
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                </button>
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
                            <label for="audit_id">Audit</label>
                            <select name="audit_id" id="audit_id" class="form-control select2" style="width: 100%;">
                                <option value="" selected>Select item</option>
                                @foreach ($audit as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="item_audit_id">Item Audit</label>
                            <select name="item_audit_id[]" id="item_audit_id" class="form-control select2"
                                multiple="multiple" style="width: 100%;">
                                <option value="" disabled>Select item</option>
                                @foreach ($itemaudit as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_item }}</option>
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
                                                    for="dept_{{ $dept->id }}">{{ $dept->aliases }}</label>
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
        <div class="modal fade" id="editauditcontrol-{{ $d->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editauditcontrolLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editauditcontrolLabel">Update Audit Control</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('update.auditControl', ['id' => $d->id]) }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="audit_id">Audit</label>
                                <select name="audit_id" id="audit_id" class="form-control select2"
                                    style="width: 100%;">
                                    <option value="" selected>Select item</option>
                                    @foreach ($audit as $d)
                                        <option value="{{ $d->id }}">{{ $d->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="item_audit_id">Audit</label>
                                <select name="item_audit_id" id="item_audit_id" class="form-control select2"
                                    style="width: 100%;">
                                    <option value="" selected>Select item</option>
                                    @foreach ($itemaudit as $d)
                                        <option value="{{ $d->id }}">{{ $d->nama_item }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="departemen">Department</label>
                                <select name="departemen" id="departemen" class="form-control select2"
                                    style="width: 100%;">
                                    <option value="" selected>Select Department</option>
                                    @foreach ($uniqueDepartemens as $d)
                                        <option value="{{ $d->id }}">{{ $d->aliases }}
                                        </option>
                                    @endforeach
                                </select>
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
        <!-- Modal Delete -->
        <div class="modal fade" id="deleteaudit-{{ $d->id }}" tabindex="-1" role="dialog"
            aria-labelledby="deleteauditModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteauditModalLabel">Delete Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this audit?
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('delete.audit', $d->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    {{-- Modal Filter --}}
    <div class="modal fade" id="auditfilterModal" tabindex="-1" role="dialog" aria-labelledby="auditfilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('masterdata.auditControl') }}" method="GET" id="filterForm">
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
                        <div class="form-group">
                            <label for="audit_id">Audit</label>
                            <select name="audit_id" id="audit_id" class="form-control select2" style="width: 100%;">
                                <option value="" selected>Select item</option>
                                @foreach ($audit as $d)
                                    <option value="{{ $d->id }}"
                                        {{ request('audit_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="item_audit_id">Item Audit</label>
                            <select name="item_audit_id" id="item_audit_id" class="form-control select2"
                                style="width: 100%;">
                                <option value="" selected>Select item</option>
                                @foreach ($itemaudit as $d)
                                    <option value="{{ $d->id }}"
                                        {{ request('item_audit_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Departemen">Departemen</label>
                            <select name="departemen" id="departemen" class="form-control select2" style="width: 100%;">
                                <option value="" selected>Select item</option>
                                @foreach ($uniqueDepartemens as $d)
                                    <option value="{{ $d->id }}"
                                        {{ request('departemen') == $d->id ? 'selected' : '' }}>
                                        {{ $d->aliases }}</option>
                                @endforeach
                            </select>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#item_audit_id').select2({
                placeholder: "Select item",
                allowClear: true
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
    <script>
        window.onload = function() {
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href.split('?')[0]);
            }
        };
    </script>
    {{-- <script>
        window.onload = function() {
            // Clear selected values in the filters when the page is loaded
            document.getElementById('audit_id').selectedIndex = 0;
            document.getElementById('item_audit_id').selectedIndex = 0;
            document.getElementById('departemen').selectedIndex = 0;

            // Clear URL parameters to reset filters
            if (history.replaceState) {
                var url = new URL(window.location);
                url.searchParams.delete('audit_id');
                url.searchParams.delete('item_audit_id');
                url.searchParams.delete('departemen');
                history.replaceState(null, null, url);
            }
        };
    </script> --}}
@endsection
