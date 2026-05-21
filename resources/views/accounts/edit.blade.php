@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/accounts/forms.css') }}">
@endpush

@section('content')
	<div class="main-content-container">
		<div class="accounts-header">
			<div class="header-title-group">
				<h2 class="card-title">Επεξεργασία Account</h2>
				<p class="text-muted">Ενημέρωση στοιχείων για την {{ $account->name }}</p>
			</div>
			<a href="{{ route('accounts.index') }}" class="btn-secondary-custom" style="text-decoration:none; color: var(--text-main);">
				← Επιστροφή
			</a>
		</div>

		<form action="{{ route('accounts.update', $account->id) }}" method="POST" class="card">
			@csrf
			@method('PUT')

			<div class="form-grid">
				<div class="form-section">
					<h3 class="section-title">Βασικές Πληροφορίες</h3>

					<div class="form-group">
						<label for="name">Όνομα Εταιρείας *</label>
						<input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $account->name) }}" required>
						@error('name') <span class="error-text">{{ $message }}</span> @enderror
					</div>

					<div class="form-row">
						<div class="form-group">
							<label for="industry">Κλάδος</label>
							<input type="text" name="industry" id="industry" class="form-control" value="{{ old('industry', $account->industry) }}">
						</div>
						<div class="form-group">
							<label for="website">Website</label>
							<input type="url" name="website" id="website" class="form-control" value="{{ old('website', $account->website) }}">
						</div>
					</div>

					<div class="form-row">
						<div class="form-group">
							<label for="annual_revenue">Ετήσιος Τζίρος (€)</label>
							<input type="number" step="0.01" name="annual_revenue" id="annual_revenue" class="form-control" value="{{ old('annual_revenue', $account->annual_revenue) }}">
						</div>
						<div class="form-group">
							<label for="employee_count">Αριθμός Υπαλλήλων</label>
							<input type="number" name="employee_count" id="employee_count" class="form-control" value="{{ old('employee_count', $account->employee_count) }}">
						</div>
					</div>
				</div>

				<div class="form-section">
					<h3 class="section-title">Επικοινωνία & Τοποθεσία</h3>

					<div class="form-row">
						<div class="form-group">
							<label for="email">Email</label>
							<input type="email" name="email" id="email" class="form-control" value="{{ old('email', $account->email) }}">
						</div>
						<div class="form-group">
							<label for="phone">Τηλέφωνο</label>
							<input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $account->phone) }}">
						</div>
					</div>

					<div class="form-group">
						<label for="address">Διεύθυνση</label>
						<input type="text" name="address" id="address" class="form-control" value="{{ old('address', $account->address) }}">
					</div>

					<div class="form-row">
						<div class="form-group">
							<label for="city">Πόλη</label>
							<input type="text" name="city" id="city" class="form-control" value="{{ old('city', $account->city) }}">
						</div>
						<div class="form-group">
							<label for="postal_code">Τ.Κ.</label>
							<input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $account->postal_code) }}">
						</div>
					</div>
				</div>
			</div>

			<div class="form-footer">
				<div class="form-group mb-0">
					<label class="switch-label">
						<input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
						<span>Ενεργός Λογαριασμός</span>
					</label>
				</div>
				<button type="submit" class="btn-primary-custom">Αποθήκευση Αλλαγών</button>
			</div>
		</form>
	</div>
@endsection