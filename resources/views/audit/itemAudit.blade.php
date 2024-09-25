@extends('layouts.app')

@section('title', 'Master Data Audit')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Master Data Item Audit</h4>
                        <div class="d-flex justify-content-end mb-3">
                            @role('admin')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#adddocaudit">
                                    Add New
                                </button>
                            @endrole
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Item Name</th>
                                        <th>Requirement</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itemAudit as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->nama_item }}</td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#edititemaudit-{{ $d->id }}">
                                                    Edit
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" data-toggle="modal"
                                                    data-target="#deleteitemaudit-{{ $d->id }}">
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
    <div class="modal fade" id="adddocaudit" tabindex="-1" role="dialog" aria-labelledby="adddocauditLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adddocauditLabel">Add Item Audit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('add.itemAudit') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_item">Item Name</label>
                            <input type="text" class="form-control" id="nama_item" name="nama_item" required>
                        </div>
                        <div class="form-group">
                            <label for="requirement">Requirement</label>
                            <textarea class="form-control" id="requirement" name="requirement" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="example_requirement">Example Requirement <span style="color: red;">*</span></label>
                            <input type="file" class="form-control" id="example_requirement" name="example_requirement">
                        </div>
                        <div class="form-group">
                            <small><span style="color: red;">*</span> Optional</small>
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

    {{-- Modal Edit Template --}}
    @foreach ($itemAudit as $d)
        <div class="modal fade" id="edititemaudit-{{ $d->id }}" tabindex="-1" role="dialog"
            aria-labelledby="edititemauditLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edititemauditLabel">Update Item Audit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('update.itemAudit', ['id' => $d->id]) }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nama_item">Item Name</label>
                                <input type="text" class="form-control" id="nama_item" name="nama_item"
                                    value="{{ old('nama_item', $d->nama_item) }}" required>
                            </div>
                            <div class="form-group">
                                <label for="audit_id">Nama Audit</label>
                                <select name="audit_id" id="audit_id" class="form-control select2" style="width: 100%;">
                                    <option value="" selected>Select Audit</option>
                                    @foreach ($audit as $d)
                                        <option value="{{ $d->id }}">{{ $d->nama }}
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
        <div class="modal fade" id="deleteitemaudit-{{ $d->id }}" tabindex="-1" role="dialog"
            aria-labelledby="deleteitemauditModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteitemauditModalLabel">Delete Confirmation</h5>
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
