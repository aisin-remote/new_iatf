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
                            <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#filterModal">
                                Filter <i class="fa fa-filter" aria-hidden="true"></i>
                            </button>
                            @role('admin')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                                    Add <i class="fa-solid fa-plus"></i>
                                </button>
                            @endrole
                        </div>

                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped table-bordered" id="app_table" width="100%">
                                <thead style="height: 3rem; background-color: #4B49AC;" class="text-white">
                                    <tr>
                                        <th width="50px">No</th>
                                        <th>Document Name</th>
                                        <th>Department</th>
                                        <th>review</th>
                                        <th>Set Reminder</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        @role('admin')
                                            <th>Approval</th>
                                        @endrole
                                        <th>Comment</th>
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
                        <select class="form-control select2" multiple="multiple" name="department_create[]"
                            id="department_create" style="width: 100%;" required>
                            @foreach ($departments as $department)
                                <option value="{{ $department->nama_departemen }}">{{ $department->nama_departemen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="review_create">Review <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="review_create" name="review_create" required>
                    </div>
                    <div class="form-group">
                        <label for="set_reminder_create">Set Reminder <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="set_reminder_create" name="set_reminder_create"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="comment_create">Comment <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="comment_create" name="comment_create" rows="4" required></textarea>
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
                        <label for="review_edit">Review <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="review_edit" name="review_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="set_reminder_edit">Set Reminder <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="set_reminder_edit" name="set_reminder_edit"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="comment_edit">Comment <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="comment_edit" name="comment_edit" rows="4" required></textarea>
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
                    <h5 class="modal-title" id="deleteModalLabel">Delete Document Control</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure want to delete this item?
                    <input type="hidden" id="id_delete">
                    <div class="form-group mb-0">
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

    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Document Control</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure want to approve this item?
                    <input type="hidden" id="id_approve">
                    <div class="form-group mb-0">
                        <input type="text" readonly class="form-control-plaintext" id="name_approve">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="submit_approve">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Document Control</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id_reject" name="id_reject">
                    <div class="form-group">
                        <label for="comment_reject">Comment</label>
                        <textarea class="form-control" id="comment_reject" name="comment_reject" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" id="submit_reject">Reject</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Document Control</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <input type="text" readonly class="form-control-plaintext" id="name_upload">
                    </div>
                    <div class="form-group mb-0">
                        <label for="file_edit">File Document <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file_edit" name="file_edit" accept=".pdf"
                            required>
                    </div>
                    <input type="hidden" id="id_upload">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="submit_upload">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfViewer" src="" width="100%" height="500px"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Documents</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="form-group">
                            <label for="departmentSelect">Select Department</label>
                            <select class="form-control" id="departmentSelect">
                                <option value="">-- Select Department --</option>
                                <!-- Tambahkan opsi departemen secara dinamis di sini -->
                                @foreach ($departments as $department)
                                    <option value="{{ $department->nama_departemen }}">{{ $department->nama_departemen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="statusSelect">Select Status</label>
                            <select class="form-control" id="statusSelect">
                                <option value="">-- Select Status --</option>
                                <!-- Tambahkan opsi status secara dinamis di sini -->
                                <option value="Uncomplete">Uncomplete</option>
                                <option value="Submitted">Submitted</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </form>
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
            $('#department_create').select2({
                // placeholder: "--- Choose ---",
                allowClear: true
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            @if (session()->has('success'))
                toastr['success']("{{ Session('success') }}")
            @endif
        });
    </script>
    <script>
        const textarea1 = document.getElementById('comment_create');

        // Fungsi untuk mengatur tinggi textarea berdasarkan isinya
        function autoResizeTextarea() {
            // Reset tinggi agar bisa dihitung ulang
            this.style.height = 'auto';
            // Set tinggi baru berdasarkan scrollHeight (tinggi konten di dalam textarea)
            this.style.height = (this.scrollHeight) + 'px';
        }

        // Ketika textarea diisi atau ada perubahan (input event), panggil fungsi resize
        textarea1.addEventListener('input', autoResizeTextarea);
    </script>
    <script>
        const textarea2 = document.getElementById('comment_edit');

        // Fungsi untuk mengatur tinggi textarea berdasarkan isinya
        function autoResizeTextarea() {
            // Reset tinggi agar bisa dihitung ulang
            this.style.height = 'auto';
            // Set tinggi baru berdasarkan scrollHeight (tinggi konten di dalam textarea)
            this.style.height = (this.scrollHeight) + 'px';
        }

        // Ketika textarea diisi atau ada perubahan (input event), panggil fungsi resize
        textarea2.addEventListener('input', autoResizeTextarea);
    </script>
    <script>
        const textarea3 = document.getElementById('comment_reject');

        // Fungsi untuk mengatur tinggi textarea berdasarkan isinya
        function autoResizeTextarea() {
            // Reset tinggi agar bisa dihitung ulang
            this.style.height = 'auto';
            // Set tinggi baru berdasarkan scrollHeight (tinggi konten di dalam textarea)
            this.style.height = (this.scrollHeight) + 'px';
        }

        // Ketika textarea diisi atau ada perubahan (input event), panggil fungsi resize
        textarea3.addEventListener('input', autoResizeTextarea);
    </script>
    <script>
        $(document).ready(function() {
            var isAdmin = @json(auth()->user()->hasRole('admin')); // Dapatkan informasi apakah user adalah admin

            var columnsConfig = [{
                    data: null,
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row, meta) {
                        var rowIndex = meta.row + meta.settings._iDisplayStart + 1;
                        return rowIndex;
                    },
                    className: "text-center"
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
                    data: 'review',
                    name: 'review',
                },
                {
                    data: 'set_reminder',
                    name: 'set_reminder',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    orderable: false,
                    searchable: false,
                    data: null,
                    render: function(data, type, row, meta) {
                        var viewButtonDisabled = data.file ? '' : 'disabled';

                        return `<div class="text-center">
                                    @role('admin')
                                        <button class="btn btn-warning btn-sm btn-edit" data-toggle="modal" data-target="#editModal" data-id="${data.id}" data-name="${data.name}" data-department="${data.department}" data-review="${data.review}" data-set_reminder="${data.set_reminder}" data-comment="${data.comment}"> <i class="fa-solid fa-edit"></i></button>
                                        <button class="btn btn-danger btn-sm btn-delete" data-toggle="modal" data-target="#deleteModal" data-id="${data.id}" data-name="${data.name}"> <i class="fa-solid fa-trash-alt"></i></button>
                                    @endrole

                                    <button class="btn btn-success btn-sm btn-upload" data-toggle="modal" data-target="#uploadModal" data-id="${data.id}" data-name="${data.name}"> <i class="fa-solid fa-upload"></i></button>
                                    <button class="btn btn-info btn-sm btn-view" data-toggle="modal" data-target="#viewModal" data-id="${data.id}" data-name="${data.name}" ${viewButtonDisabled}> <i class="fa-solid fa-eye"></i></button>
                                </div>`;
                    }
                },
                {
                    data: 'comment',
                    name: 'comment',
                }
            ];

            // Hanya tambahkan kolom approval jika user adalah admin
            if (isAdmin) {
                columnsConfig.splice(7, 0, {
                    orderable: false,
                    searchable: false,
                    data: null,
                    render: function(data, type, row, meta) {
                        const isSubmitted = data.status === 'Submitted';
                        return `<div class="text-center">
                        <button class="btn btn-success btn-sm btn-approve" data-toggle="modal" data-target="#approveModal" data-id="${data.id}" data-name="${data.name}" data-department="${data.department}"data-status="${data.status}" ${!isSubmitted ? 'disabled' : ''}> <i class="fa-solid fa-check"></i></button>
                        <button class="btn btn-danger btn-sm btn-reject" data-toggle="modal" data-target="#rejectModal" data-id="${data.id}" data-name="${data.name}" data-department="${data.department}" data-status="${data.status}" ${!isSubmitted ? 'disabled' : ''}> <i class="fa-solid fa-x"></i></button>
                    </div>`;
                    }
                });
            }

            var table = $('#app_table').DataTable({
                'lengthChange': true,
                'processing': true,
                'serverSide': false,
                'orderable': true,
                scrolX: true,
                ajax: {
                    url: "{{ route('document_review.list_ajax') }}",
                },
                columns: columnsConfig
            });

            $('#createModal').on('show.bs.modal', function() {
                $('#name_create').val('');
                $('#department_create').val([]);
                $('#review_create').val('');
                $('#set_reminder_create').val('');
                $('#comment_create').val('');

                var createButton = document.getElementById('submit_create');
                createButton.removeAttribute('disabled');
                createButton.innerHTML = 'Submit';
            });

            // CREATE
            $('#submit_create').on('click', function(e) {
                var reviewDate = new Date($('#review_create').val());
                var setReminderDate = new Date($('#set_reminder_create').val());

                if (setReminderDate >= reviewDate) {
                    toastr['error']('Tanggal Reminder harus lebih awal dari Obselete');
                    var createButton = document.getElementById('submit_create');
                    createButton.removeAttribute('disabled');
                    createButton.innerHTML = 'Submit';
                    return;
                }

                var selectedDepartments = $('#department_create').val();

                $.ajax({
                    url: "{{ route('document_review.store') }}",
                    type: "POST",
                    data: {
                        name: $('#name_create').val(),
                        department: selectedDepartments,
                        review: $('#review_create').val(),
                        set_reminder: $('#set_reminder_create').val(),
                        comment: $('#comment_create').val(),
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
                            var createButton = document.getElementById('submit_create');
                            createButton.removeAttribute('disabled');
                            createButton.innerHTML = 'Submit';
                        } else {
                            toastr['error'](xhr.responseText || error);
                            var createButton = document.getElementById('submit_create');
                            createButton.removeAttribute('disabled');
                            createButton.innerHTML = 'Submit';
                        }
                    }
                });
            });

            // EDIT
            $('#app_table').on('click', '.btn-edit', function() {
                var id_edit = $(this).data('id');
                var name_edit = $(this).data('name');
                var department_edit = $(this).data('department');
                var review_edit = $(this).data('review');
                var set_reminder_edit = $(this).data('set_reminder');
                var comment_edit = $(this).data('comment');

                var editButton = document.getElementById('submit_edit');
                editButton.removeAttribute('disabled');
                editButton.innerHTML = 'Update';

                $('#id_edit').val(id_edit);
                $('#name_edit').val(name_edit);
                $('#department_edit').val(department_edit);
                $('#review_edit').val(review_edit);
                $('#set_reminder_edit').val(set_reminder_edit);
                $('#comment_edit').val(comment_edit);
            });

            $('#submit_edit').on('click', function() {
                let id_edit = $('#id_edit').val();
                let name_edit = $('#name_edit').val();
                let department_edit = $('#department_edit').val();
                let review_edit = $('#review_edit').val();
                let set_reminder_edit = $('#set_reminder_edit').val();
                let comment_edit = $('#comment_edit').val();
                $.ajax({
                    url: "{{ route('document_review.update') }}",
                    type: "POST",
                    data: {
                        id: id_edit,
                        name: name_edit,
                        department: department_edit,
                        review: review_edit,
                        set_reminder: set_reminder_edit,
                        comment: comment_edit,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr['success'](response);
                        table.ajax.reload();
                        $('#editModal').modal('hide');
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

                $('#id_delete').val(id_delete);
                $('#name_delete').val(name_delete);
            });

            $('#submit_delete').on('click', function() {
                let id_delete = $('#id_delete').val();
                $.ajax({
                    url: "{{ route('document_review.delete') }}",
                    type: "POST",
                    data: {
                        id: id_delete,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr['success'](response);
                        table.ajax.reload();
                        $('#deleteModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                    }
                });
            });

            // APPROVE
            $('#app_table').on('click', '.btn-approve', function() {
                var id_approve = $(this).data('id');
                var name_approve = $(this).data('name');
                var status_approve = $(this).data('status'); // Get the status

                if (status_approve !== 'Submitted') {
                    alert('This document cannot be approved. Status: ' + status_approve);
                    return;
                }

                var approveButton = document.getElementById('submit_approve');
                approveButton.removeAttribute('disabled');
                approveButton.innerHTML = 'Approve';

                $('#id_approve').val(id_approve);
                $('#name_approve').val(name_approve);
            });

            $('#submit_approve').on('click', function() {
                let id_approve = $('#id_approve').val();
                $.ajax({
                    url: "{{ route('document_review.approve') }}",
                    type: "POST",
                    data: {
                        id: id_approve,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr['success'](response);
                        table.ajax.reload();
                        $('#approveModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                        var approveButton = document.getElementById('submit_approve');
                        approveButton.removeAttribute('disabled');
                        approveButton.innerHTML = 'Approve';
                    }
                });
            });


            // REJECT
            $('#app_table').on('click', '.btn-reject', function() {
                var id_reject = $(this).data('id');
                var name_reject = $(this).data('name');
                var status_reject = $(this).data('status'); // Get the status

                if (status_reject !== 'Submitted') {
                    alert('This document cannot be rejected. Status: ' + status_reject);
                    return;
                }

                var rejectButton = document.getElementById('submit_reject');
                rejectButton.removeAttribute('disabled');
                rejectButton.innerHTML = 'Reject';

                $('#id_reject').val(id_reject);
                $('#name_reject').val(name_reject);
            });

            // Submit Reject Button
            $('#submit_reject').on('click', function() {
                let id_reject = $('#id_reject').val();
                let comment_reject = $('#comment_reject').val();

                if (comment_reject === '') {
                    alert('Comment is required');
                    return;
                }

                $.ajax({
                    url: "{{ route('document_review.reject') }}",
                    type: "POST",
                    data: {
                        id: id_reject,
                        comment_reject: comment_reject,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        toastr['success'](response);
                        table.ajax.reload();
                        $('#rejectModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                        var rejectButton = document.getElementById('submit_reject');
                        rejectButton.removeAttribute('disabled');
                        rejectButton.innerHTML = 'Reject';
                    }
                });
            });

            // UPLOAD
            $('#app_table').on('click', '.btn-upload', function() {
                var id_upload = $(this).data('id');
                var name_upload = $(this).data('name');

                var uploadButton = document.getElementById('submit_upload');
                uploadButton.removeAttribute('disabled');
                uploadButton.innerHTML = 'Upload';

                $('#id_upload').val(id_upload);
                $('#name_upload').val(name_upload);
            });

            $('#submit_upload').on('click', function() {
                let id_upload = $('#id_upload').val();
                let file = $('#file_edit')[0].files[0];

                let formData = new FormData();
                formData.append('id', id_upload);
                formData.append('file', file);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('document_review.upload') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr['success'](response);
                        table.ajax.reload();
                        $('#uploadModal').modal('hide');
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                        var uploadButton = document.getElementById('submit_upload');
                        uploadButton.removeAttribute('disabled');
                        uploadButton.innerHTML = 'Upload';
                    }
                });
            });

            $('#app_table').on('click', '.btn-view', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');

                $('#viewModalLabel').text('View Document: ' + name);

                $.ajax({
                    url: "{{ route('document_review.file') }}",
                    type: "GET",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        $('#pdfViewer').attr('src', response.file_url);
                        $('#viewModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        toastr['error']('Unable to load file');
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var approveButton = document.getElementById('submit_approve');
            var spinner = '<i class="fa-solid fa-spinner spin"></i>';

            approveButton.addEventListener('click', function() {
                approveButton.setAttribute('disabled', 'true');
                approveButton.innerHTML = spinner + ' Approving...';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var rejectButton = document.getElementById('submit_reject');
            var spinner = '<i class="fa-solid fa-spinner spin"></i>';

            rejectButton.addEventListener('click', function() {
                rejectButton.setAttribute('disabled', 'true');
                rejectButton.innerHTML = spinner + ' Rejecting...';
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var uploadButton = document.getElementById('submit_upload');
            var spinner = '<i class="fa-solid fa-spinner spin"></i>';

            uploadButton.addEventListener('click', function() {
                uploadButton.setAttribute('disabled', 'true');
                uploadButton.innerHTML = spinner + ' Uploading...';
            });
        });
    </script>
@endpush
