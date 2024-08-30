@extends('layouts.app')

@section('title', 'Reminder')

@section('content')
    <div class="content -wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Set Reminder Document</h4>
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addsetreminder">
                                Set new reminder
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Document Name</th>
                                        <th>Set reminder</th>
                                        <th>Due date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>WI</td>
                                        <td> - </td>
                                        <td> - </td>
                                        <td>
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                data-target="#updatereminder">
                                                Update
                                                <i class="fa-solid fa-edit"></i>
                                            </button>
                                            <!-- Tombol Download -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addsetreminder" tabindex="-1" role="dialog" aria-labelledby="addsetreminderLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addsetreminderLabel">Add Departemen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_dokumen">Document Name</label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" required>
                        </div>
                        <div class="form-group">
                            <label for="reminder_date">Set Reminder</label>
                            <input type="date" class="form-control" id="reminder_date" name="reminder_date" required>
                        </div>
                        <div class="form-group">
                            <label for="due_date">Due date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updatereminder" tabindex="-1" role="dialog" aria-labelledby="updatereminderLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatereminderLabel">Add Departemen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_dokumen">Document Name</label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" required>
                        </div>
                        <div class="form-group">
                            <label for="reminder_date">Set Reminder</label>
                            <input type="date" class="form-control" id="reminder_date" name="reminder_date" required>
                        </div>
                        <div class="form-group">
                            <label for="due_date">Due date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
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

@endsection
