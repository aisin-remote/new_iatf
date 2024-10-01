@extends('layouts.app')

@section('title', 'Audit Control')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Detail Audit {{ $audit->nama }} </h4>
                        <div class="row mb-3">
                            <div class="col-md-12 d-flex justify-content-end">
                                <input type="text" class="form-control form-control-sm mr-2" id="searchInput"
                                    placeholder="Search..." style="width: 250px;">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#auditfilterModal"
                                    style="background: #56544B">
                                    Filter
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="documentTableBody">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Item Name</th>
                                        <th class="text-center">Status</th>
                                        <th class="">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($auditControls as $key => $auditControl)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $auditControl->itemAudit->nama_item }}</td>
                                            <td class="text-center">{{ $auditControl->status }}</td>
                                            <td>
                                                @role('guest')
                                                    @if ($auditControl->documentAudit->count())
                                                        <div class="mb-2">
                                                            @foreach ($auditControl->documentAudit as $document)
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <a href="{{ asset('storage/' . $document->attachment) }}"
                                                                        class="btn btn-info btn-sm" download>
                                                                        {{ preg_replace('/^\d+-/', '', basename($document->attachment)) }}
                                                                    </a>
                                                                    <a href="{{ asset('storage/' . $document->attachment) }}"
                                                                        class="btn btn-secondary btn-sm ml-2" target="_blank"
                                                                        title="Preview">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    {{-- Tombol hapus untuk setiap file yang di-upload --}}
                                                                    <form
                                                                        action="{{ route('deleteDocumentAudit', $document->id) }}"
                                                                        method="POST"
                                                                        style="display:inline-block; margin-left: 10px;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                                            onclick="return confirm('Are you sure you want to delete this document?')"><i
                                                                                class="fa-solid fa-trash"></i></button>
                                                                    </form>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p>No document uploaded yet.</p>
                                                    @endif
                                                    {{-- Form upload untuk upload file baru --}}
                                                    <form action="{{ route('uploadDocumentAudit', $auditControl->id) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <label for="attachments">Upload New File:</label>
                                                        <input type="file" name="attachments" id="attachments" required
                                                            multiple>
                                                        <br>
                                                        <button type="submit"
                                                            class="btn btn-primary btn-sm mt-2">Upload</button>
                                                    </form>
                                                @endrole
                                                @role('admin')
                                                    @if ($auditControl->documentAudit->count())
                                                        <div class="mb-2">
                                                            @foreach ($auditControl->documentAudit as $document)
                                                                <div class="d-flex align-items-center mb-2">
                                                                    {{-- Tombol Approve --}}
                                                                    <form
                                                                        action="{{ route('approveDocumentAudit', $document->id) }}"
                                                                        method="POST"
                                                                        style="display:inline-block; margin-right: 10px;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success btn-sm"
                                                                            onclick="return confirm('Are you sure you want to approve this document?')">
                                                                            <i class="fa-solid fa-check"></i> Approve
                                                                        </button>
                                                                    </form>

                                                                    {{-- Tombol Reject --}}
                                                                    <form
                                                                        action="{{ route('rejectDocumentAudit', $document->id) }}"
                                                                        method="POST" style="display:inline-block;">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                                            onclick="return confirm('Are you sure you want to reject this document?')">
                                                                            <i class="fa-solid fa-times"></i> Reject
                                                                        </button>
                                                                    </form>

                                                                    {{-- Dropdown untuk Preview --}}
                                                                    <div class="dropdown ml-2">
                                                                        <button class="btn btn-info btn-sm dropdown-toggle"
                                                                            type="button"
                                                                            id="dropdownPreviewButton{{ $document->id }}"
                                                                            data-toggle="dropdown" aria-haspopup="true"
                                                                            aria-expanded="false">
                                                                            Preview
                                                                        </button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="dropdownPreviewButton{{ $document->id }}">
                                                                            <a class="dropdown-item"
                                                                                href="{{ asset('storage/' . $document->attachment) }}"
                                                                                target="_blank">
                                                                                {{ preg_replace('/^\d+-/', '', basename($document->attachment)) }}
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p>No document uploaded yet.</p>
                                                    @endif
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('tbody tr').each(function() {
                    var row = $(this);
                    var text = row.text().toLowerCase();
                    row.toggle(text.indexOf(value) > -1);
                });
            });

            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $.get(url, function(data) {
                    $('tbody').html($(data).find('tbody').html());
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const selectAllCheckbox = document.getElementById('select_all');
            const checkboxes = document.querySelectorAll('input[name="departemen[]"]');

            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    }
                    if (document.querySelectorAll('input[name="departemen[]"]:checked').length ===
                        checkboxes.length) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
        });
    </script>
@endsection
