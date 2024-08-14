@extends('layouts.app')

@section('title', 'Master Data Departemen')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Master Data Departemen</h4>
                        <div class="d-flex justify-content-end mb-3">
                            @role('admin')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#adddepartemen">
                                    Add New
                                </button>
                            @endrole
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Template Number</th>
                                        <th>Document Title</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kode_proses as $d)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $d->kode_proses }}</td>
                                            <td>{{ $d->nama_proses }}</td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#edittemplate-{{ $d->id }}">
                                                    Edit
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <!-- Tombol Download -->
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
    <div class="modal fade" id="adddepartemen" tabindex="-1" role="dialog" aria-labelledby="adddepartemenLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adddepartemenLabel">Add Departemen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('add.rulecode') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kode_proses">Code</label>
                            <input type="text" class="form-control" id="kode_proses" name="kode_proses" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_proses">Process Name</label>
                            <input type="text" class="form-control" id="nama_proses" name="nama_proses" required>
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
    @foreach ($kode_proses as $d)
        <div class="modal fade" id="edittemplate-{{ $d->id }}" tabindex="-1" role="dialog"
            aria-labelledby="edittemplateLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edittemplateLabel">Select Template (.word, .excel)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('template.edit', ['id' => $d->id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nomor_template">Template Number</label>
                                <input type="text" class="form-control" id="nomor_template" name="nomor_template"
                                    value="{{ $d->nomor_template }}" required>
                            </div>
                            <div class="form-group">
                                <label for="tgl_efektif">Effective date</label>
                                <input type="date" class="form-control" id="tgl_efektif" name="tgl_efektif" required>
                            </div>
                            <div class="form-group">
                                <label for="file">Select Preview (.pdf)</label>
                                <input type="file" class="form-control-file" id="file" name="file">
                            </div>
                            <div class="form-group">
                                <label for="template">Select Template (.word, .excel)</label>
                                <input type="file" class="form-control-file" id="template" name="template">
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
    @endforeach
@endsection
