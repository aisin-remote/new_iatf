@extends('layouts.app')

@section('title', 'Template Dokumen')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Document Templates</h4>
                        <div class="d-flex justify-content-end mb-3">
                            <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                placeholder="Search..." style="width: 300px;">
                            @role('admin')
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addtemplate">
                                    Add New
                                </button>
                            @endrole
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger" id="error-alert">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Template Number</th>
                                        <th>Document Title</th>
                                        <th>Effective Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dokumen as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $doc->nomor_template }}</td>
                                            <td>{{ $doc->tipe_dokumen }}</td>
                                            <td>{{ $doc->tgl_efektif ?? '-' }}</td>
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
                                                @if ($doc->file_pdf)
                                                    <a href="{{ route('template.preview', ['id' => $doc->id, 'preview' => true]) }}"
                                                        class="btn btn-info btn-sm" target="_blank">

                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-info btn-sm" disabled>

                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>
                                                @endif
                                                @if ($doc->template)
                                                    <a href="{{ route('template.download', ['id' => $doc->id, 'preview' => false]) }}"
                                                        class="btn btn-success btn-sm" target="_blank">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                @else
                                                    <button class=  "btn btn-success btn-sm" disabled>
                                                        <i class="fa-solid fa-download"></i>
                                                    </button>
                                                @endif
                                                @role('admin')
                                                    <form action="{{ route('template.delete', $doc->id) }}" method="POST"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endrole
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
                            <label for="nomor_template">Template Number</label>
                            <input type="text" class="form-control" id="nomor_template" name="nomor_template" required>
                        </div>
                        <div class="form-group">
                            <label for="tipe_dokumen">Document Type</label>
                            <input type="text" class="form-control" id="tipe_dokumen" name="tipe_dokumen" required>
                        </div>
                        <div class="form-group">
                            <label for="code">Document Code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="form-group">
                            <label for="tgl_efektif">Effective date</label>
                            <input type="date" class="form-control" id="tgl_efektif" name="tgl_efektif" required>
                        </div>
                        <div class="form-group">
                            <label for="file_pdf">Select Preview (.pdf)</label>
                            <input type="file" class="form-control-file" id="file_pdf" name="file_pdf" required>
                            <p>Maks 10 mb</p>
                        </div>
                        <div class="form-group">
                            <label for="template">Select Template (.word, .excel)</label>
                            <input type="file" class="form-control-file" id="template" name="template">
                            <p>Maks 10 mb</p>
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
                        <h5 class="modal-title" id="edittemplateLabel">Select Template (.word, .excel)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('template.edit', ['id' => $doc->id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nomor_template">Template Number</label>
                                <input type="text" class="form-control" id="nomor_template" name="nomor_template"
                                    value="{{ $doc->nomor_template }}" required>
                            </div>
                            <div class="form-group">
                                <label for="tgl_efektif">Effective date</label>
                                <input type="date" class="form-control" id="tgl_efektif" name="tgl_efektif"
                                    value="{{ $doc->tgl_efektif }}" required>
                            </div>
                            <div class="form-group">
                                <label for="file_pdf">Select Preview (.pdf)</label>
                                <input type="file" class="form-control-file" id="file_pdf" name="file_pdf">
                                @if ($doc->file_pdf)
                                    <p>Current file: <a href="{{ asset('storage/' . $doc->file_pdf) }}"
                                            target="_blank">{{ basename($doc->file_pdf) }}</a></p>
                                @endif
                                <p>Maks 10 mb</p>
                            </div>
                            <div class="form-group">
                                <label for="template">Select Template (.word, .excel)</label>
                                <input type="file" class="form-control-file" id="template" name="template">
                                @if ($doc->template)
                                    <p>Current file: <a href="{{ asset('storage/' . $doc->template) }}"
                                            target="_blank">{{ basename($doc->template) }}</a></p>
                                @endif
                                <p>Maks 10 mb</p>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        setTimeout(function() {
            document.getElementById('error-alert')?.remove();
        }, 3000);
        $(document).ready(function() {
            // Event handler untuk pencarian
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#documentTableBody tr').each(function() {
                    var row = $(this);
                    var text = row.text().toLowerCase();
                    row.toggle(text.indexOf(value) > -1);
                });
            });

            // Menghandle pagination agar pencarian bekerja
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $.get(url, function(data) {
                    $('#documentTableBody').html($(data).find('#documentTableBody').html());
                });
            });
        });
    </script>
@endsection
