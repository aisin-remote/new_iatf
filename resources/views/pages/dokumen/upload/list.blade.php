@extends('layouts.app')
@section('title', 'Upload Dokumen')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $title ?? __('Upload Dokumen') }}</h4>
                        <div class="text-right mb-4">
                            <!-- Tombol "Add Dokumen" dengan atribut data-toggle dan data-target untuk memicu modal -->
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#addDocumentModal">Add Dokumen</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Upload by</th>
                                        <th>Updated at</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>WIS</td>
                                        <td>ITD</td>
                                        <td>27/03/2024</td>
                                        <td>diterima</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk memilih jenis dokumen dan mengunggah file -->
    <div class="modal fade" id="addDocumentModal" tabindex="-1" role="dialog" aria-labelledby="addDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocumentModalLabel">Add Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="documentType">Jenis Dokumen</label>
                            <select class="form-control" id="documentType">
                                <option value="1">WIS</option>
                                <option value="2">KTP</option>
                                <!-- Tambahkan opsi lain sesuai kebutuhan -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="documentFile">Pilih File</label>
                            <input type="file" class="form-control-file" id="documentFile">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <!-- Tombol untuk mengunggah file -->
                    <button type="button" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </div>

@endsection
