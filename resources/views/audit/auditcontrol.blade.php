@extends('layouts.app')

@section('title', 'Audit Control')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">List Audit Control</h4>
                        <div class="row mb-3">
                            <!-- Kolom untuk input pencarian dan tombol filter -->
                            <div class="col-md-12 d-flex justify-content-end">
                                <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                    placeholder="Search..." style="width: 250px;">
                                {{-- <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#auditfilterModal"
                                    style="background: #56544B">
                                    Filter
                                </button> --}}
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Audit Name</th>
                                        <th class="text-center">Start Audit</th>
                                        <th class="text-center">End Audit</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($groupedAuditControls->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center">No data available</td>
                                        </tr>
                                    @else
                                        @foreach ($groupedAuditControls as $key => $group)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ $group['audit_name'] }}</td>
                                                <td class="text-center">{{ $group['start_audit'] }}</td>
                                                <td class="text-center">{{ $group['end_audit'] }}</td>
                                                <td class="text-center">{{ $group['status'] }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('audit.details', ['audit_id' => $group['audit_id'], 'departemen_id' => $group['data']->first()->departemen->id]) }}"
                                                        class="btn btn-info btn-sm">
                                                        See Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="modal fade" id="auditfilterModal" tabindex="-1" role="dialog" aria-labelledby="auditfilterModalLabel"
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
    </div> --}}
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
