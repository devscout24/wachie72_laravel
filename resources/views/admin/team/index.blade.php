@extends('backend.master')

@section('body')
<div class="d-flex justify-content-between mt-4 mb-3">
    <h4>Team Members</h4>
    <a href="{{ route('admin.team.create') }}" class="btn btn-primary">Add New</a>
</div>

<div class="card">
    <div class="card-body">
        <table id="teamTable" class="table table-bordered" style="width:100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Image</th>
                <th>Designation</th>
                <th>Bio</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#teamTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.team.index') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'designation', name: 'designation' },
                { data: 'bio', name: 'bio' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Delete confirmation
        $(document).on('click', '.delete-button', function() {
            if (confirm('Are you sure you want to delete this team member?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush