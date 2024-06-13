@extends('layouts.app')
@section('title', 'Dokumen-Iatf')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Dokumen {{ ucfirst($jenis) }} - Tipe: {{ ucfirst($tipe) }}</h4>
                        <a class="btn btn-primary btn-sm"
                            href="{{ route('dokumen.download', ['jenis' => $jenis, 'tipe' => $tipe]) }}">
                            Download
                            <i class="fa-solid fa-file-arrow-down"></i>
                        </a
                        @role('admin')
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploaddokumen"
                            style="margin-left: 4px">
                            Upload
                        </button>
                        @endrole
                        <p class="card-description"></p>
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDokumenModal">
                                Add New
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Nama Dokumen</th>
                                        <th>Revisi</th>
                                        <th>Tanggal Upload</th>
                                        <th>status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($indukDokumenList as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_dokumen }}</td>
                                            <td>{{ $doc->nama_dokumen }}</td>
                                            <td>{{ $doc->revisi_log }}</td>
                                            <td>{{ $doc->tgl_upload }}</td>
                                            <td>{{ $doc->status }}</td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-warning btn-sm" data-toggle="modal"
                                                    data-target="#editDokumen-{{ $doc->id }}">
                                                    Edit
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>

                                                <!-- Tombol Download -->
                                                <a href="{{ route('download.rule', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                                                    class="btn btn-primary btn-sm">
                                                    Download
                                                    <i class="fa-solid fa-file-arrow-down"></i>
                                                </a>
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

    <!-- Modal untuk mengunggah file -->
    <div class="modal fade" id="uploaddokumen" tabindex="-1" role="dialog" aria-labelledby="uploaddokumenLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploaddokumenLabel">Upload Template {{ $tipe }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dokumen.upload', ['jenis' => $jenis, 'tipe' => $tipe]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
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

    <!-- Add Document Modal -->
    <div class="modal fade" id="addDokumenModal" tabindex="-1" role="dialog" aria-labelledby="addDokumenModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('tambah.rule') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDokumenModalLabel">Add New Dokumen {{ ucfirst($tipe) }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_dokumen">Nama Dokumen</label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" required>
                        </div>
                        <div class="form-group">
                            <label for="rule_id">Kode Proses</label>
                            <select class="form-control" id="rule_id" name="rule_id" required>
                                <option value="">Pilih Kode Proses</option>
                                @foreach ($kodeProses as $item)
                                    <option value="{{ $item['id'] }}">
                                        {{ $item['kode_proses'] }} - {{ $item['nama_proses'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="file">File</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                        <input type="hidden" name="tipe_dokumen" value="{{ $tipe }}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @foreach ($indukDokumenList as $doc)
        <div class="modal fade" id="editDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editDokumenLabel-{{ $doc->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('edit.rule', ['jenis' => $jenis, 'tipe' => $tipe, 'id' => $doc->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDokumenLabel">Edit Dokumen {{ ucfirst($tipe) }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nama_dokumen">Nama Dokumen</label>
                                <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="rule_id">Kode Proses</label>
                                <select class="form-control" id="rule_id" name="rule_id" required>
                                    <option value="">Pilih Kode Proses</option>
                                    @foreach ($kodeProses as $item)
                                        <option value="{{ $item['id'] }}">
                                            {{ $item['kode_proses'] }} - {{ $item['nama_proses'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="file">File</label>
                                <input type="file" class="form-control" id="file" name="file" required>
                            </div>
                            <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                            <input type="hidden" name="tipe_dokumen" value="{{ $tipe }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
