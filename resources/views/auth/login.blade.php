@extends('layouts.guest')

@section('content')
	<div class="login-card">
		<div class="login-header">
			<h1 style="font-size: 1.8rem; color: var(--secondary);">🚀 CRM_Pro</h1>
			<p style="color: var(--text-muted);">Καλώς ορίσατε! Παρακαλώ συνδεθείτε.</p>
		</div>

		<form action="{{ route('login') }}" method="POST">
			@csrf

			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
			</div>

			<div class="form-group">
				<label for="password">Κωδικός</label>
				<input type="password" id="password" name="password" class="form-control" required>
			</div>

			@if ($errors->any())
				<div class="error-msg">
					{{ $errors->first() }}
				</div>
			@endif

			<div style="margin-bottom: 20px;">
				<label style="display: flex; align-items: center; font-weight: normal; cursor: pointer;">
					<input type="checkbox" name="remember" style="margin-right: 10px;"> Να με θυμάσαι
				</label>
			</div>

			<button type="submit" class="btn-primary-block">
				Είσοδος στο Σύστημα
			</button>
		</form>
	</div>
@endsection