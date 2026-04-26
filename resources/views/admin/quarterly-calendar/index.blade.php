@extends('layouts.app')

@section('title', 'Quarterly Calendar')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.quarterly-calendar.store') }}" method="POST">
    @csrf
    <input type="hidden" name="school_year" value="{{ $calendar->school_year }}">
    
    <div class="data-card">
        <div class="data-card-header">
            <h5><i class="fa fa-calendar me-2" style="color:var(--accent)"></i>Quarterly Calendar – SY {{ $calendar->school_year }}</h5>
            <button type="submit" class="btn-sm-custom btn-primary-sm"><i class="fa fa-save"></i> Save Dates</button>
        </div>
        
        <div class="row g-3">
            <div class="col-sm-6 col-lg-3">
                <div class="quarter-card">
                    <h4>Q1</h4>
                    <div class="dates mb-3">{{ $calendar->q1_start->format('M j') }} – {{ $calendar->q1_end->format('M j, Y') }}</div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:11px">Start Date</label>
                        <input type="date" name="q1_start" class="form-control" value="{{ $calendar->q1_start->format('Y-m-d') }}" style="font-size:12px;padding:8px;border-radius:8px">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:11px">End Date</label>
                        <input type="date" name="q1_end" class="form-control" value="{{ $calendar->q1_end->format('Y-m-d') }}" style="font-size:12px;padding:8px;border-radius:8px">
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="quarter-card" style="background:linear-gradient(135deg,#F3EEFF,white);border-color:var(--purple)">
                    <h4 style="color:var(--purple)">Q2</h4>
                    <div class="dates mb-3">{{ $calendar->q2_start->format('M j') }} – {{ $calendar->q2_end->format('M j, Y') }}</div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:11px">Start Date</label>
                        <input type="date" name="q2_start" class="form-control" value="{{ $calendar->q2_start->format('Y-m-d') }}" style="font-size:12px;padding:8px;border-radius:8px">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:11px">End Date</label>
                        <input type="date" name="q2_end" class="form-control" value="{{ $calendar->q2_end->format('Y-m-d') }}" style="font-size:12px;padding:8px;border-radius:8px">
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="quarter-card" style="background:linear-gradient(135deg,#EDFAF4,white);border-color:var(--success)">
                    <h4 style="color:var(--success)">Q3</h4>
                    <div class="dates mb-3">{{ $calendar->q3_start->format('M j') }} – {{ $calendar->q3_end->format('M j, Y') }}</div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:11px">Start Date</label>
                        <input type="date" name="q3_start" class="form-control" value="{{ $calendar->q3_start->format('Y-m-d') }}" style="font-size:12px;padding:8px;border-radius:8px">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:11px">End Date</label>
                        <input type="date" name="q3_end" class="form-control" value="{{ $calendar->q3_end->format('Y-m-d') }}" style="font-size:12px;padding:8px;border-radius:8px">
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-3">
                <div class="quarter-card" style="background:linear-gradient(135deg,#FFF7ED,white);border-color:var(--accent)">
                    <h4 style="color:var(--accent)">Q4</h4>
                    <div class="dates mb-3">{{ $calendar->q4_start->format('M j') }} – {{ $calendar->q4_end->format('M j, Y') }}</div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size:11px">Start Date</label>
                        <input type="date" name="q4_start" class="form-control" value="{{ $calendar->q4_start->format('Y-m-d') }}" style="font-size:12px;padding:8px;border-radius:8px">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:11px">End Date</label>
                        <input type="date" name="q4_end" class="form-control" value="{{ $calendar->q4_end->format('Y-m-d') }}" style="font-size:12px;padding:8px;border-radius:8px">
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</form>
@endsection