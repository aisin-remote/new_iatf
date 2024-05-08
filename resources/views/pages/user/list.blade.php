@extends('layouts.app')
@section('title', 'User')
@section('content')
    <div class="content-wrapper">
        <div class="row">

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $title ?? __('User') }}</h4>
                        <div class="text-right mb-4">
                            <a href="{{ route('user-create') }}" class="btn btn-primary">Add User</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Departement</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Created at</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td scope="row">{{ $loop->iteration }}</td>
                                            <td>{{ $user->departemen }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>{{ $user->created_at }}</td>
                                            <td>
                                                <a href="#editModal{{ $user->id }}" class="edit"
                                                    data-toggle="modal"><i class="material-icons" data-toggle="tooltip"
                                                        title="" data-original-title="Edit"></i></a>
                                                <a href="#deleteModal{{ $user->id }}" class="delete"
                                                    data-toggle="modal"><i class="material-icons" data-toggle="tooltip"
                                                        title="" data-original-title="Delete"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <!-- Modal Edit User -->
                                @foreach ($users as $user)
                                    <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('user-update', ['id' => $user->id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Edit User</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Departemen</label>
                                                            <input type="text" class="form-control" name="departemen"
                                                                value="{{ $user->departemen }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Username</label>
                                                            <input type="text" class="form-control" name="username"
                                                                value="{{ $user->username }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="exampleSelectRole">Role</label>
                                                            <select class="form-control" id="exampleSelectRole"
                                                                name="role">
                                                                <option value="admin"
                                                                    {{ old('role') == 'admin' ? 'selected' : ($user->role == 'admin' ? 'selected' : '') }}>
                                                                    Admin</option>
                                                                <option value="departemen"
                                                                    {{ old('role') == 'departemen' ? 'selected' : ($user->role == 'departement' ? 'selected' : '') }}>
                                                                    Departemen</option>
                                                            </select>
                                                            @error('role')
                                                                <small class="text-danger">{{ $message }}</small>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endsection
