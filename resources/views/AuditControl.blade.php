@extends('layouts.app')

@section('title', 'Audit Control')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Audit Control</h4>
                        <div class="d-flex justify-content-end mb-3">
                            @role('admin')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addauditcontrol">
                                    Add New
                                </button>
                            @endrole
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Document Name</th>
                                        <th>Audit</th>
                                        <th>Reminder</th>
                                        <th>Duedate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($auditControls as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->document_audit->nama_dokumen }}</td>
                                            <td>{{ $d->audit->nama }}</td>
                                            <td>{{ $d->reminder }}</td>
                                            <td>{{ $d->duedate }}</td>
                                            <td>{{ $d->attachment }}</td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editauditcontrol-{{ $d->id }}">
                                                    Edit
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                    data-target="#deleteauditcontrol-{{ $d->id }}">
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
                            <label for="documentaudit_id">Document Name</label>
                            <select name="documentaudit_id" id="documentaudit_id" class="form-control select2"
                                style="width: 100%;">
                                <option value="" selected>Select Document Audit</option>
                                @foreach ($documentAudits as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_dokumen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="audit_id">Audit</label>
                            <select name="audit_id" id="audit_id" class="form-control select2" style="width: 100%;">
                                <option value="" selected>Select Audit</option>
                                @foreach ($audit as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="reminder">Reminder</label>
                            <input type="date" class="form-control" id="reminder" name="reminder" required>
                        </div>
                        <div class="form-group">
                            <label for="duedate">Duedate</label>
                            <input type="date" class="form-control" id="duedate" name="duedate" required>
                        </div>
                        <div class="form-group">
                            <label for="Attachment">Attachment</label>
                            <input type="file" class="form-control" id="Attachment" name="attachment" required>
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
    @foreach ($auditControls as $d)
        <div class="modal fade" id="editauditcontrol-{{ $d->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editauditcontrolLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editauditcontrolLabel">Update Audit</h5>
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
        <!-- Modal Delete -->
        <div class="modal fade" id="deleteauditcontrol-{{ $d->id }}" tabindex="-1" role="dialog"
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
