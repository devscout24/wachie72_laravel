@extends('backend.master')

@section('body')


<div class="d-flex justify-content-between mt-4 mb-3">
    <h4>Edit Team Member</h4>
    <a href="{{ route('admin.team.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.team.update', $team->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $team->name }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="designation" class="form-label">Designation</label>
                        <input type="text" name="designation" id="designation" class="form-control" value="{{ $team->designation }}" required>
                    </div>
                </div>


                <div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea name="bio" id="bio" class="form-control sumernote" rows="4" required>{{ $team->bio }}</textarea>
                    </div>
                </div>

                <div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="1" {{ $team->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $team->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>


@endsection

@push('scripts')
    <script>
    $(document).ready(function() {
        $('.sumernote').summernote({
            height: 200
        });
    });
    </script>
@endpush