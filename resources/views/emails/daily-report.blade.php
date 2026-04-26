<h2>Daily Progress Report for {{ $log->student->first_name }}</h2>
<p><strong>Date:</strong> {{ $log->log_date->format('F d, Y') }}</p>
<hr>
<p><strong>Teacher's Notes:</strong></p>
<p>{{ $log->notes }}</p>
<br>
<p><strong>AI Assistant Summary & Recommendations:</strong></p>
<p>{{ $log->ai_generated_report }}</p>
<br>
<p>Log in to your Guardian Dashboard to view more details and progress analytics!</p>