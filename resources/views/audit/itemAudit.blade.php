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
                                {{-- <div class="col-md-6 d-flex justify-content-end align-items-center"> --}}
                                <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                    placeholder="Search..." style="width: 300px;">
                                </button>
                                {{-- </div> --}}
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#additemaudit">
                                    Add New
                                </button>
                            @endrole
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
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
                                            <td>{{ $d->requirement }}</td>
                                            <td>
                                                @php
                                                    $fileUrl = $d->example_requirement
                                                        ? asset('storage/' . $d->example_requirement)
                                                        : null;
                                                @endphp

                                                @if ($fileUrl)
                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye"></i> Preview
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" onclick="showAlert()"
                                                        class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye"></i> Preview
                                                    </a>
                                                @endif

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
    <div class="modal fade" id="additemaudit" tabindex="-1" role="dialog" aria-labelledby="additemauditLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="additemauditLabel">Add Item Audit</h5>
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
                            <label for="example_requirement">Example Requirement <span
                                    style="color: red;">(opsional)</span></label>
                            <input type="file" class="form-control" id="example_requirement" name="example_requirement">
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
                                <label for="requirement">Requirement</label>
                                <textarea class="form-control" id="requirement" name="requirement" rows="4">{{ old('requirement', $d->requirement) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="example_requirement">Example Requirement <span
                                        style="color: rgb(255, 0, 0);">(opsional)</span></label>
                                <input type="file" class="form-control" id="example_requirement"
                                    name="example_requirement"
                                    value="{{ old('example_requirement', $d->example_requirement) }}">
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
                        Are you sure you want to delete this item audit?
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
    <script>
        function showAlert() {
            alert('Dokumen tidak ditemukan');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#documentTableBody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>

@endsection
