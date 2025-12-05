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
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $team->name }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Current Image</label><br>
                    <img src="{{ asset($team->image) }}" width="80" height="80">
                </div>

                <div class="col-md-6 mt-3">
                    <div class="mb-3">
                        <label class="form-label">Change Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" value="{{ $team->designation }}" class="form-control" required>
                    </div>
                </div>

                <div>
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control sumernote">{{ $team->bio }}</textarea>
                    </div>
                </div>

                <div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $team->is_active == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $team->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

            </div>

            <button class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

@endsection
