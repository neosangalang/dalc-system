@extends('layouts.app')

@section('title', 'Manage Academic Calendar')

@section('content')
<div class="container-fluid p-0">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Academic Calendar</h4>
            <p class="text-muted mb-0">Set the start and end dates for each quarter. These dates will automatically appear on the Teacher Dashboard.</p>
        </div>
    </div>

    <div class="data-card p-4 p-md-5 border-top border-4 border-primary">
        <form action="{{ route('admin.quarters.update') }}" method="POST">
            @csrf

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3" style="width: 15%;">Quarter</th>
                            <th class="py-3" style="width: 30%;">Start Date</th>
                            <th class="py-3" style="width: 30%;">End Date</th>
                            <th class="py-3 text-center" style="width: 25%;">Current Active Quarter</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quarters as $quarter)
                            <tr>
                                <td class="fw-bold fs-5 text-primary">{{ $quarter->name }}</td>
                                
                                <td>
                                    <input type="date" name="quarters[{{ $quarter->id }}][start_date]" 
                                           class="form-control" 
                                           value="{{ $quarter->start_date }}">
                                </td>
                                
                                <td>
                                    <input type="date" name="quarters[{{ $quarter->id }}][end_date]" 
                                           class="form-control" 
                                           value="{{ $quarter->end_date }}">
                                </td>
                                
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="radio" 
                                               name="active_quarter_id" 
                                               value="{{ $quarter->id }}" 
                                               style="transform: scale(1.5); cursor: pointer;"
                                               {{ $quarter->is_active ? 'checked' : '' }} required>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="border-radius: 8px;">
                    <i class="fa fa-save me-2"></i> Save Calendar Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection