@extends('layouts.app')
@section('title', 'create user')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $title ?? __('Create User') }}</h4>
                        <form action="{{ route('user-store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="exampleSelectDepartemen">departemen</label>
                                <select class="form-control" id="exampleSelectDepartemen" name="departemen">
                                    <option selected disabled>Choose...</option>
                                    <option value="HR & GA">HR & GA</option>
                                    <option value="IR & LEGAL">IR & LEGAL</option>
                                    <option value="MARKETING">MARKETING</option>
                                    <option value="FINANCE & ACCOUNTING">FINANCE & ACCOUNTING</option>
                                    <option value="PURCHASING & EXIM">PURCHASING & EXIM</option>
                                    <option value="NEW PROJECT & LOCALIZATION">NEW PROJECT & LOCALIZATION</option>
                                    <option value="BODY COMPONENT">BODY COMPONENT</option>
                                    <option value="PROD UNIT MACHINING">PROD UNIT MACHINING</option>
                                    <option value="PPIC">PPIC</option>
                                    <option value="ENGINERING BODY">ENGINERING BODY</option>
                                    <option value="ENGINERING UNIT">ENGINERING UNIT</option>
                                    <option value="MAINTENANCE">MAINTENANCE</option>
                                    <option value="QA BODY COMPONENT">QA BODY COMPONENT</option>
                                    <option value="MANAGEMENT SYSTEM">MANAGEMENT SYSTEM</option>
                                    <option value="QA ENGINE COMPONENT">QA ENGINE COMPONENT</option>
                                    <option value="IT DEVELOPMENT">IT DEVELOPMENT</option>
                                    <option value="PRODUCTION SYSTEM & DEVELOPMENT">PRODUCTION SYSTEM & DEVELOPMENT</option>
                                    <option value="PROD UNIT DC">PROD UNIT DC</option>
                                    <option value="ENGINEERING & QUALITY ELECTRICAL COMPONENT">ENGINEERING & QUALITY
                                        ELECTRICAL COMPONENT</option>
                                    <option value="PPIC ELECTRIC">PPIC ELECTRIC</option>
                                    <option value="PRODUCTION ELECTRIC">PRODUCTION ELECTRIC</option>
                                    <option value="MAINTENANCE ELECTRIC">MAINTENANCE ELECTRIC</option>
                                </select>
                                @error('departemen')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputUsername">Username</label>
                                <input type="text" class="form-control" id="exampleInputUsername" placeholder="Username"
                                    name="username">
                                @error('username')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword4">Password</label>
                                <input type="password" class="form-control" id="exampleInputPassword4"
                                    placeholder="Password" name="password">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="exampleSelectRole">Role</label>
                                <select class="form-control" id="exampleSelectRole" name="role">
                                    <option selected disabled>Choose...</option>
                                    <option value="admin">Admin</option>
                                    <option value="departemen">Departemen</option>
                                </select>
                                @error('role')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a href="{{ route('user') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
