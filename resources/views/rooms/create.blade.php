@extends('layouts.dashboard')

@section('title', 'Add New Room')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-door-open me-2"></i>
                        Add New Room
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Create a new room for your library</p>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('libraries.rooms.store', $library) }}" method="POST">
                        @csrf
                        
                        @if(isset($libraries) && $libraries->count() > 1)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="library_id" class="form-label required-field">Library</label>
                                    <select class="form-select @error('library_id') is-invalid @enderror" 
                                            id="library_id" 
                                            name="library_id" 
                                            required>
                                        <option value="">Select library</option>
                                        @foreach($libraries as $lib)
                                            <option value="{{ $lib->id }}" {{ (string) old('library_id', $library->id) === (string) $lib->id ? 'selected' : '' }}>
                                                {{ $lib->name }} ({{ $lib->location ?? 'No location' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('library_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @elseif(isset($libraries) && $libraries->count() === 1)
                            @php($singleLibrary = $libraries->first())
                            <input type="hidden" id="library_id" name="library_id" value="{{ $singleLibrary->id }}">
                            <div class="library-info p-2 border rounded mb-3">
                                <h6 class="mb-1"><i class="fas fa-building me-2"></i>Library: {{ $singleLibrary->name }}</h6>
                                <p class="mb-0 small"><i class="fas fa-map-marker-alt me-1"></i>{{ $singleLibrary->location ?? 'No location specified' }}</p>
                            </div>
                        @elseif(isset($library))
                            <input type="hidden" id="library_id" name="library_id" value="{{ $library->id }}">
                            <div class="library-info p-2 border rounded mb-3">
                                <h6 class="mb-1"><i class="fas fa-building me-2"></i>Library: {{ $library->name }}</h6>
                                <p class="mb-0 small"><i class="fas fa-map-marker-alt me-1"></i>{{ $library->location ?? 'No location specified' }}</p>
                            </div>
                        @endif
                        
                        <div class="row">
                            <!-- Room Name -->
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label required-field">Room Name</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Enter room name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description"
                                          name="description"
                                          rows="3"
                                          placeholder="Describe this room (optional)">{{ old('description') }}</textarea>
                                <div class="form-text">Optional: Add details about the room's purpose or features</div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="{{ isset($library) ? route('libraries.show', $library) : route('libraries.index') }}"
                                   class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i>Create Room
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('name').focus();
    
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
        submitBtn.disabled = true;
    });
});
</script>
@endpush
