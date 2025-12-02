@extends('backend.master')

@section('body')
    <div class="d-flex justify-content-between mt-4 mb-3">
        <h4>Create New Property</h4>
        <a href="{{ route('admin.property.index') }}" class="btn btn-secondary">Property List</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.property.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <!-- MAIN IMAGE -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Main Image <small class="text-muted">(Multiple Allowed)</small></label>
                        <input type="file" name="main_image[]" class="form-control" multiple>
                        @error('main_image.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- MULTIPLE IMAGES -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Multiple Images <small class="text-muted">(Multiple
                                Allowed)</small></label>
                        <input type="file" name="multiple_image[]" class="form-control" multiple>
                        @error('multiple_image.*')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- TITLE -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <!-- LOCATION -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>

                    <!-- PRICE -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price ($)</label>
                        <input type="number" name="price" step="0.01" class="form-control" required>
                    </div>

                    <!-- CLEANING FEE -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cleaning Fee ($)</label>
                        <input type="number" name="cleaning_fee" step="0.01" class="form-control">
                    </div>

                    <!-- BEDROOMS -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bedrooms</label>
                        <input type="number" name="bedrooms" class="form-control" value="0" required>
                    </div>

                    <!-- BATHROOMS -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bathrooms</label>
                        <input type="number" name="bathrooms" class="form-control" value="0" required>
                    </div>

                    <!-- GUESTS -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Max Guests</label>
                        <input type="number" name="max_guests" class="form-control" value="0" required>
                    </div>

                    <!-- AMENITIES -->
                    <div class="mb-3">
                        <label for="amenitySelect" class="form-label">Amenities</label>
                        <select name="amenity_id[]" id="amenitySelect"
                            class="form-control @error('amenity_id') is-invalid @enderror" multiple="multiple" required>
                            @foreach ($amenities as $amenity)
                                <option value="{{ $amenity->id }}"
                                    {{ isset($property) && $property->amenities->pluck('id')->contains($amenity->id) ? 'selected' : (collect(old('amenity_id'))->contains($amenity->id) ? 'selected' : '') }}>
                                    {{ $amenity->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('amenity_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- DESCRIPTION -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control sumernote" rows="5"></textarea>
                    </div>

                    <!-- STATUS -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                </div>

                <button type="submit" class="btn btn-success mt-3">Create Property</button>

            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.sumernote').summernote({
                height: 200
            });
            $('#amenitySelect').select2({
                placeholder: "Select amenities",
                allowClear: true,
                width: '100%',
            });
        });
    </script>
@endpush
