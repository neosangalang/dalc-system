<!DOCTYPE html>
<html>
<head>
    <title>Archive: {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        body { font-family: sans-serif; }
        .section { margin-bottom: 20px; }
        .log-entry { border-bottom: 1px solid #ccc; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Master Archive: {{ $student->first_name }} {{ $student->last_name }}</h1>
    <p><strong>Diagnosis:</strong> {{ $student->exceptionality }}</p>
    <hr>

    <div class="section">
        <h2>IEP Goals</h2>
        @foreach($student->iepGoals as $goal)
            <p><strong>{{ $goal->domain }}:</strong> {{ $goal->goal_description }}</p>
        @endforeach
    </div>

    <div class="section">
        <h2>Daily Logs Summary</h2>
        @foreach($student->dailyLogs as $log)
            <div class="log-entry">
                <p><strong>Date:</strong> {{ $log->log_date }}</p>
                <p><strong>Notes:</strong> {{ $log->notes }}</p>
            </div>
        @endforeach
    </div>
</body>
</html>