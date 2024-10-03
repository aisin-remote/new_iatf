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
                                        <th>Uploaded</th>
                                        @role('admin')
                                            <th class="text-center">Action</th>
                                        @endrole
                                        <th class="text-center">Comment</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($auditControls as $key => $auditControl)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $auditControl->itemAudit->nama_item }}</td>
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
                                                        <input type="file" name="attachments[]" id="attachments" required
                                                            multiple> {{-- array input --}}
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
                                                                    <span>{{ preg_replace('/^\d+-/', '', basename($document->attachment)) }}</span>
                                                                    <a href="{{ asset('storage/' . $document->attachment) }}"
                                                                        class="btn btn-secondary btn-sm ml-2" target="_blank"
                                                                        title="Preview">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p>No document uploaded yet.</p>
                                                    @endif
                                                @endrole

                                            </td>
                                            @role('admin')
                                                <td class="text-center">
                                                    @if ($auditControl->status !== 'completed')
                                                        {{-- Menyembunyikan tombol jika status completed --}}
                                                        <div class="mb-2">
                                                            <form action="{{ route('approveItemAudit', $auditControl->id) }}"
                                                                method="POST"
                                                                style="display:inline-block; margin-right: 10px;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm"
                                                                    {{ $auditControl->documentAudit->isEmpty() ? 'disabled' : '' }}
                                                                    onclick="return confirm('Are you sure you want to approve this item?')">
                                                                    <i class="fa-solid fa-check"></i> Approve
                                                                </button>
                                                            </form>

                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                {{ $auditControl->documentAudit->isEmpty() ? 'disabled' : '' }}
                                                                data-toggle="modal"
                                                                data-target="#rejectModal{{ $auditControl->id }}">
                                                                <i class="fa-solid fa-times"></i> Reject
                                                            </button>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endrole
                                            <td class="text-center">{{ $auditControl->comment }}</td>
                                            <td class="text-center">
                                                <span
                                                    @if ($auditControl->status === 'uncomplete') style="color: red; font-weight: bold;" 
                                                    @elseif($auditControl->status === 'submitted') 
                                                        style="color: orange; font-weight: bold;" 
                                                    @elseif($auditControl->status === 'completed') 
                                                        style="color: green; font-weight: bold;" @endif>
                                                    {{ $auditControl->status }}
                                                </span>
                                            </td>
                                        </tr>

                                        {{-- Modal untuk menolak item audit --}}
                                        <div class="modal fade" id="rejectModal{{ $auditControl->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="rejectModalLabel{{ $auditControl->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="rejectModalLabel{{ $auditControl->id }}">Reject Item Audit
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('rejectItemAudit', $auditControl->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="comment">Comment:</label>
                                                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="modal fade" id="feedbackDokumen-{{ $doc->id }}" tabindex="-1" role="dialog"
        aria-labelledby="feedbackDokumenLabel-{{ $doc->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('dokumen.approve', ['id' => $doc->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="feedbackDokumenLabel-{{ $doc->id }}">Document Confirmation
                            Approved
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <input type="text" class="form-control" id="comment" name="comment" required>
                        </div>
                        <div class="form-group">
                            <label for="file">File (.word, .excel) <small>(Optional)</small></label>
                            <input type="file" class="form-control-file" id="file" name="file">
                            <p>Maks 20 mb</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

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
