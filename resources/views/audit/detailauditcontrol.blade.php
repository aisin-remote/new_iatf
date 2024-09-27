@extends('layouts.app')

@section('title', 'Audit Control')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detail Audit {{ $AuditControls->first()->audit->nama }} </h4>
                        <div class="row mb-3">
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
                                        <th>Item Audit</th>
                                        <th>
                                            @role('admin')
                                                Uploaded
                                            @endrole
                                        </th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($uploadedItems as $key => $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item['itemAudit']->nama_item }}</td>
                                            <td>
                                                @role('admin')
                                                    <!-- Cek role pengguna -->
                                                    {{ $item['uploaded'] }} / {{ $item['total'] }}
                                                @endrole
                                            </td>
                                            <td>{{ $item['status'] }}</td>
                                            <td>
                                                @role('guest')
                                                    <!-- Cek role pengguna -->
                                                    <form action="{{ route('uploadDocumentAudit', $item['audit_id']) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <label for="attachments">Upload New File:</label>
                                                        <input type="file" name="attachments" id="attachments" required>
                                                        <br>
                                                        <button type="submit"
                                                            class="btn btn-primary btn-sm mt-2">Upload</button>
                                                        <br>
                                                    </form>
                                                @endrole
                                                @role('admin')
                                                    <a href="{{ route('audit.item.details', ['audit_id' => $item['audit_id'], 'item_audit_id' => $item['itemAudit']->id]) }}"
                                                        class="btn btn-info btn-sm">
                                                        See Detail
                                                    </a>
                                                @endrole
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#documentTableBody tr').each(function() {
                    var row = $(this);
                    var text = row.text().toLowerCase();
                    row.toggle(text.indexOf(value) > -1);
                });
            });

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
            const selectAllCheckbox = document.getElementById('select_all');
            const checkboxes = document.querySelectorAll('input[name="departemen[]"]');

            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    }
                    if (document.querySelectorAll('input[name="departemen[]"]:checked').length ===
                        checkboxes.length) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
        });
    </script>
@endsection
