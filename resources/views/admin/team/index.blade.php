@extends('backend.master')

@section('body')
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h4 class="mb-0">Team Members</h4>
        <a href="{{ route('admin.team.create') }}" class="btn btn-primary">Add New</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="teamTable" class="table table-striped table-hover table-bordered w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Designation</th>
                        <th>Bio</th>
                        <th>Status</th>
                        <th width="150px">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            $('#teamTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.team.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'designation',
                        name: 'designation'
                    },
                    {
                        data: 'bio',
                        name: 'bio',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Delete confirmation
            $(document).on('click', '.delete-button', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this team member?')) {
                    $(this).closest('form').submit();
                }
            });

        });
    </script>
@endpush
