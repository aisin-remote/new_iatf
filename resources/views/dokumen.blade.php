@extends('layouts.app')

@section('title', 'Template Dokumen')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Template Dokumen</h4>
                        <div class="d-flex justify-content-end mb-3">
                            @role('admin')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addtemplate">
                                    Add New
                                </button>
                            @endrole
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Template</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Tipe Dokumen</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dokumen as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_template }}</td>
                                            <td>{{ $doc->jenis_dokumen }}</td>
                                            <td>{{ $doc->tipe_dokumen }}</td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                @role('admin')
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                        data-target="#edittemplate-{{ $doc->id }}">
                                                        Edit
                                                        <i class="fa-solid fa-edit"></i>
                                                    </button>
                                                @endrole

                                                <!-- Tombol Download -->
                                                @if ($doc->file)
                                                    <a href="{{ route('template.download', ['id' => $doc->id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Download
                                                        <i class="fa-solid fa-file-arrow-down"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-primary btn-sm" disabled>
                                                        Download
                                                        <i class="fa-solid fa-file-arrow-down"></i>
                                                    </button>
                                                @endif
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
    <div class="modal fade" id="addtemplate" tabindex="-1" role="dialog" aria-labelledby="addtemplateLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addtemplateLabel">Add Template</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('template.add') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nomor_template">Nomor Template</label>
                            <input type="text" class="form-control" id="nomor_template" name="nomor_template" required>
                        </div>
                        <div class="form-group">
                            <label for="jenis_dokumen">Pilih Jenis Dokumen</label>
                            <select class="form-control" id="jenis_dokumen" name="jenis_dokumen" required>
                                <option value="" disabled selected>Pilih jenis dokumen</option>
                                <option value="Rule">Rule</option>
                                <option value="Process">Process</option>
                                <!-- Tambahkan opsi lainnya sesuai kebutuhan -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipe_dokumen">Tipe Dokumen</label>
                            <input type="text" class="form-control" id="tipe_dokumen" name="tipe_dokumen" required>
                        </div>
                        <div class="form-group">
                            <label for="file">Pilih File</label>
                            <input type="file" class="form-control-file" id="file" name="file" required>
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
    @foreach ($dokumen as $doc)
        <div class="modal fade" id="edittemplate-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="edittemplateLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edittemplateLabel">Edit Template</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('template.edit', ['id' => $doc->id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nomor_template">Nomor Template</label>
                                <input type="text" class="form-control" id="nomor_template" name="nomor_template"
                                    value="{{ $doc->nomor_template }}" required>
                            </div>
                            <div class="form-group">
                                <label for="file">Pilih File (Opsional)</label>
                                <input type="file" class="form-control-file" id="file" name="file">
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
