@extends('layouts.app')

@section('title', 'Master Data Audit')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Master Data Audit</h4>
                        <div class="d-flex justify-content-end mb-3">
                            @role('admin')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addaudit">
                                    Add New
                                </button>
                            @endrole
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Audit Name</th>
                                        <th>Reminder</th>
                                        <th>Audit Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($audit as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->nama }}</td>
                                            <td>{{ $d->reminder }} s.d {{ $d->duedate }}</td>
                                            <td>{{ $d->start_audit }} s.d {{ $d->end_audit }}</td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editaudit-{{ $d->id }}">
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
    <div class="modal fade" id="addaudit" tabindex="-1" role="dialog" aria-labelledby="addauditLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addauditLabel">Add Audit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('add.audit') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Audit Name</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="reminder">Reminder</label>
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <input type="date" class="form-control" id="reminder" name="reminder" required>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span>To</span>
                                </div>
                                <div class="col-md-5">
                                    <input type="date" class="form-control" id="duedate" name="duedate" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="Audit_Date">Audit Date</label>
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <input type="date" class="form-control" id="start_audit" name="start_audit" required>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span>To</span>
                                </div>
                                <div class="col-md-5">
                                    <input type="date" class="form-control" id="end_audit" name="end_audit" required>
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
    @foreach ($audit as $d)
        <div class="modal fade" id="editaudit-{{ $d->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editauditLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editauditLabel">Update Audit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('update.audit', ['id' => $d->id]) }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nama">Audit Name</label>
                                <input type="text" class="form-control" id="nama"
                                    name="nama"value="{{ old('nama', $d->nama) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="reminder">Reminder Set</label>
                                <input type="date" class="form-control" id="reminder" name="reminder"
                                    value="{{ old('reminder', $d->reminder) }}"required>
                            </div>
                            <div class="form-group">
                                <label for="duedate">Due Date</label>
                                <input type="date" class="form-control" id="duedate" name="duedate"
                                    value="{{ old('duedate', $d->duedate) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_audit">Audit Date</label>
                                <input type="date" class="form-control" id="tanggal_audit" name="tanggal_audit"
                                    value="{{ old('tanggal_audit', $d->tanggal_audit) }}"required>
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
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Confirmation</h5>
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
@endsection
