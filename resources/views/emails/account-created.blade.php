<h2>Welcome to DALC, {{ $user->name }}!</h2>
<p>Your account has been successfully created.</p>
<p><strong>Username:</strong> {{ $user->username }}</p>
<p><strong>Password:</strong> {{ $password }}</p>
<br>
<p>Please log in at: <a href="{{ route('login') }}">{{ route('login') }}</a></p>
<p>We recommend changing your password after your first login.</p>