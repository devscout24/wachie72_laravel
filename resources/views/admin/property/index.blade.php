@extends('backend.master')

@section('body')
    <div class="d-flex justify-content-between mt-4 mb-3">
        <h4>Property List</h4>
        <a href="{{ route('admin.property.create') }}" class="btn btn-primary">+ Add Property</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table id="propertyTable" class="table table-bordered nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Main Image</th>
                        <th>Multiple Image</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Beds</th>
                        <th>Baths</th>
                        <th>Guests</th>
                        <th>Amenities</th>
                        <th>Description</th>
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
        $(function() {
            var table = $('#propertyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.property.index') }}",
                pagingType: "full_numbers",
                scrollX: true, // horizontal scroll
                scrollCollapse: true, // allow table to shrink if less data
                fixedColumns: {
                    leftColumns: 1 // keep index column fixed
                },
                autoWidth: false,
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'multiple_image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'location'
                    },
                    {
                        data: 'price'
                    },
                    {
                        data: 'bedrooms'
                    },
                    {
                        data: 'bathrooms'
                    },
                    {
                        data: 'max_guests'
                    },
                    {
                        data: 'amenity_id'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // DELETE
            $(document).on('click', '.delete', function() {
                let id = $(this).data('id');
                if (!confirm("Delete this property?")) return;

                $.ajax({
                    url: "{{ url('admin/property') }}/" + id,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        table.ajax.reload();
                        alert(res.message);
                    }
                });
            });
        });
    </script>
@endpush
