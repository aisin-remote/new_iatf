@extends('layouts.app')

@section('title', 'List Document Control')
@section('sidebar')

@endsection
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">List Document Control</h4>
                        <div class="d-flex justify-content-end mb-3">
                            @role('admin')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                                    Add <i class="fa-solid fa-plus"></i>
                                </button>
                            @endrole
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="app_table">
                                <thead style="height: 3rem; background-color: #4B49AC;" class="text-white">
                                    <tr>
                                        <th width="50px">No</th>
                                        <th>Document Name</th>
                                        <th>Department</th>
                                        <th>Obsolete</th>
                                        <th>Set Reminder</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody style="display: table-row-group;border-color: inherit;">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add Template --}}
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Add Document Control</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name_create">Document Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_create" name="name_create" required>
                    </div>
                    <div class="form-group">
                        <label for="department_create">Department <span class="text-danger">*</span></label>
                        <select class="form-control" name="department_create" id="department_create" required>
                            <option value="">-- Choose --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->nama_departemen }}">{{ $department->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="obsolete_create">Obsolete <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="obsolete_create" name="obsolete_create" required>
                    </div>
                    <div class="form-group">
                        <label for="set_reminder_create">Set Reminder <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="set_reminder_create" name="set_reminder_create"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submit_create">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Add Document Control</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_edit">
                    <div class="form-group">
                        <label for="name_edit">Document Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name_edit" name="name_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="department_edit">Department <span class="text-danger">*</span></label>
                        <select class="form-control" name="department_edit" id="department_edit" required>
                            <option value="">-- Choose --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->nama_departemen }}">{{ $department->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="obsolete_edit">Obsolete <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="obsolete_edit" name="obsolete_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="set_reminder_edit">Set Reminder <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="set_reminder_edit" name="set_reminder_edit"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submit_edit">Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Add Document Control</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure want to delte this item?
                    <input type="hidden" id="id_delete">
                    <div class="form-group">
                        <input type="text" readonly class="form-control-plaintext" id="name_delete">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" id="submit_delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendors/datatables/css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/toastr/toastr.min.css') }}">

    <style>
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('vendors/datatables/js/datatables.min.js') }}"></script>
    <script src="{{ asset('vendors/toastr/toastr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if (session()->has('success'))
                toastr['success']("{{ Session('success') }}")
            @endif
        })
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#app_table').DataTable({
                'lengthChange': true,
                'processing': true,
                'serverSide': false,
                'orderable': true,
                ajax: {
                    url: "{{ route('document_control.list_ajax') }}",
                },
                columns: [{
                        data: null,
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row, meta) {
                            var rowIndex = meta.row + meta.settings._iDisplayStart + 1;
                            return rowIndex;
                        },
                        className: "text-center" // Menetapkan kelas CSS 'text-center'
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'department',
                        name: 'department',
                    },
                    {
                        data: 'obsolete',
                        name: 'obsolete',
                    },
                    {
                        data: 'set_reminder',
                        name: 'set_reminder',
                    },
                    {
                        orderable: false,
                        searchable: false,
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div class="text-center">
                                    <button class="btn btn-warning btn-sm btn-edit" data-toggle="modal" data-target="#editModal" data-id="${data.id}" data-name="${data.name}" data-department="${data.department}" data-obsolete="${data.obsolete}" data-set_reminder="${data.set_reminder}">Edit <i class="fa-solid fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-toggle="modal" data-target="#deleteModal" data-id="${data.id}" data-name="${data.name}">Delete <i class="fa-solid fa-trash-alt"></i></button>
                                </div>
                        `;
                        }
                    },
                ],
            });

            $('#createModal').on('show.bs.modal', function() {
                // Kosongkan semua input dalam modal saat modal ditampilkan
                $('#name_create').val('');
                $('#department_create').val('');
                $('#obsolete_create').val('');
                $('#set_reminder_create').val('');

                var createButton = document.getElementById('submit_create');
                createButton.removeAttribute('disabled');
                createButton.innerHTML = 'Submit';
            });

            // CREATE
            $('#submit_create').on('click', function(e) {
                $.ajax({
                    url: "{{ route('document_control.store') }}",
                    type: "POST",
                    data: {
                        name: $('#name_create').val(),
                        department: $('#department_create').val(),
                        obsolete: $('#obsolete_create').val(),
                        set_reminder: $('#set_reminder_create').val(),
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr['success'](response.message || 'Document added successfully!');
                        table.ajax.reload();
                        $('#createModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                messages.forEach(function(message) {
                                    toastr['error'](message);
                                });
                            });
                        } else {
                            toastr['error'](xhr.responseText || error);
                        }
                    }
                });
            });

            // EDIT
            $('#app_table').on('click', '.btn-edit', function() {
                var id_edit = $(this).data('id');
                var name_edit = $(this).data('name');
                var department_edit = $(this).data('department');
                var obsolete_edit = $(this).data('obsolete');
                var set_reminder_edit = $(this).data('set_reminder');

                var editButton = document.getElementById('submit_edit');
                editButton.removeAttribute('disabled');
                editButton.innerHTML = 'Update';

                $('#id_edit').val(id_edit)
                $('#name_edit').val(name_edit)
                $('#department_edit').val(department_edit)
                $('#obsolete_edit').val(obsolete_edit)
                $('#set_reminder_edit').val(set_reminder_edit)
            })

            $('#submit_edit').on('click', function() {
                let id_edit = $('#id_edit').val();
                let name_edit = $('#name_edit').val();
                let department_edit = $('#department_edit').val();
                let obsolete_edit = $('#obsolete_edit').val();
                let set_reminder_edit = $('#set_reminder_edit').val();
                $.ajax({
                    url: "{{ route('document_control.update') }}",
                    type: "POST",
                    data: {
                        id: id_edit,
                        name: name_edit,
                        department: department_edit,
                        obsolete: obsolete_edit,
                        set_reminder: set_reminder_edit,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr['success'](response)
                        table.ajax.reload();
                        $('#editModal').modal('hide')
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                    }
                });
            });

            // DELETE
            $('#app_table').on('click', '.btn-delete', function() {
                var id_delete = $(this).data('id');
                var name_delete = $(this).data('name');

                var deleteButton = document.getElementById('submit_delete');
                deleteButton.removeAttribute('disabled');
                deleteButton.innerHTML = 'Delete';

                $('#id_delete').val(id_delete)
                $('#name_delete').val(name_delete)
            })

            $('#submit_delete').on('click', function() {
                let id_delete = $('#id_delete').val();
                $.ajax({
                    url: "{{ route('document_control.delete') }}",
                    type: "POST",
                    data: {
                        id: id_delete,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr['success'](response)
                        table.ajax.reload();
                        $('#deleteModal').modal('hide')
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var createButton = document.getElementById('submit_create');
            var spinner = '<i class="fa-solid fa-spinner spin"></i>';

            createButton.addEventListener('click', function() {
                createButton.setAttribute('disabled', 'true');
                createButton.innerHTML = spinner + ' Submitting...';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editButton = document.getElementById('submit_edit');
            var spinner = '<i class="fa-solid fa-spinner spin"></i>';

            editButton.addEventListener('click', function() {
                editButton.setAttribute('disabled', 'true');
                editButton.innerHTML = spinner + ' Updating...';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteButton = document.getElementById('submit_delete');
            var spinner = '<i class="fa-solid fa-spinner spin"></i>';

            deleteButton.addEventListener('click', function() {
                deleteButton.setAttribute('disabled', 'true');
                deleteButton.innerHTML = spinner + ' Deleting...';
            });
        });
    </script>
@endpush
