@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/contacts/show.css') }}">
	<link rel="stylesheet" href="{{ asset('css/contacts/edit.css') }}">
@endpush

@section('content')
	<div class="contact-edit-container">
		<header class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Create New Contact</h1>
				<p class="module-subtitle">Add a new person to your CRM database</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('contacts.index') }}" class="btn-action">Cancel</a>
			</div>
		</header>

		<form action="{{ route('contacts.store') }}" method="POST" class="contact-edit-form">
			@csrf

			<div class="contact-edit-grid">
				{{-- Section 1: Identity --}}
				<section class="contact-form-card {{ $errors->hasAny(['first_name', 'last_name', 'job_title', 'account_id']) ? 'has-errors' : '' }}">
					<h4 class="contact-form-header">👤 Basic Identity</h4>
					<div class="form-grid-2">
						<div class="form-group">
							<label for="first_name">First Name <span class="required">*</span></label>
							<input type="text" name="first_name" id="first_name"
							       class="form-input @error('first_name') is-invalid @enderror"
							       placeholder="e.g. John" value="{{ old('first_name') }}" required>
							@error('first_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
						<div class="form-group">
							<label for="last_name">Last Name</label>
							<input type="text" name="last_name" id="last_name"
							       class="form-input @error('last_name') is-invalid @enderror"
							       placeholder="e.g. Doe" value="{{ old('last_name') }}">
							@error('last_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
					</div>

					<div class="form-grid-2 mt-3">
						<div class="form-group">
							<label for="job_title">Job Title</label>
							<input type="text" name="job_title" id="job_title"
							       class="form-input @error('job_title') is-invalid @enderror"
							       placeholder="e.g. Project Manager" value="{{ old('job_title') }}">
							@error('job_title') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
						<div class="form-group">
							<label for="account_id">Account (Company) <span class="required">*</span></label>
							<select name="account_id" id="account_id" class="form-select @error('account_id') is-invalid @enderror">
								<option value="" disabled {{ old('account_id') ? '' : 'selected' }}>Select an Account...</option>
								@foreach($accounts as $account)
									<option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
										{{ $account->name }}
									</option>
								@endforeach
							</select>
							@error('account_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
					</div>
				</section>

				{{-- Section 2: Contact & Location --}}
				<section class="contact-form-card {{ $errors->hasAny(['email', 'phone', 'address', 'city', 'country']) ? 'has-errors' : '' }}">
					<h4 class="contact-form-header">📍 Contact & Location</h4>
					<div class="form-grid-2">
						<div class="form-group">
							<label for="email">Email Address</label>
							<input type="email" name="email" id="email"
							       class="form-input @error('email') is-invalid @enderror"
							       placeholder="john.doe@example.com" value="{{ old('email') }}">
							@error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
						<div class="form-group">
							<label for="phone">Phone Number</label>
							<input type="text" name="phone" id="phone"
							       class="form-input @error('phone') is-invalid @enderror"
							       placeholder="+1 234 567 890" value="{{ old('phone') }}">
							@error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
					</div>

					<div class="form-group mt-3">
						<label for="address">Street Address</label>
						<input type="text" name="address" id="address"
						       class="form-input @error('address') is-invalid @enderror"
						       value="{{ old('address') }}">
						@error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
					</div>

					<div class="form-grid-2 mt-3">
						<div class="form-group">
							<label for="city">City</label>
							<input type="text" name="city" id="city"
							       class="form-input @error('city') is-invalid @enderror"
							       value="{{ old('city') }}">
							@error('city') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
						<div class="form-group">
							<label for="country">Country</label>
							<input type="text" name="country" id="country"
							       class="form-input @error('country') is-invalid @enderror"
							       value="{{ old('country') }}">
							@error('country') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
					</div>
				</section>

				{{-- Section 3: Internal --}}
				<section class="contact-form-card {{ $errors->hasAny(['owner_id', 'is_primary', 'notes']) ? 'has-errors' : '' }}">
					<h4 class="contact-form-header">📋 Internal Details</h4>
					<div class="form-grid-2 align-end">
						<div class="form-group">
							<label for="owner_id">Assigned Owner</label>
							<select name="owner_id" id="owner_id" class="form-select @error('owner_id') is-invalid @enderror">
								@foreach($users as $user)
									<option value="{{ $user->id }}" {{ (old('owner_id') ?? auth()->id()) == $user->id ? 'selected' : '' }}>
										{{ $user->profile->full_name ?? $user->name }}
									</option>
								@endforeach
							</select>
							@error('owner_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>
						<div class="form-group" style="justify-content: end;">
							<div class="toggle-container">
								<input type="hidden" name="is_primary" value="0">
								<input type="checkbox" name="is_primary" id="is_primary" value="1"
								       class="toggle-input" {{ old('is_primary') ? 'checked' : '' }}>
								<label for="is_primary" class="toggle-label">
									<span class="toggle-switch"></span>
									<span class="toggle-text">Primary Contact</span>
								</label>
							</div>
							@error('is_primary') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
						</div>
					</div>
					<div class="form-group mt-3">
						<label for="notes">Internal Notes</label>
						<textarea name="notes" id="notes" class="form-input @error('notes') is-invalid @enderror" rows="5">{{ old('notes') }}</textarea>
						@error('notes') <span class="invalid-feedback">{{ $message }}</span> @enderror
					</div>
				</section>
			</div>

			<div class="contact-form-actions">
				<button type="reset" class="btn-action edit" style="margin-right: 10px;">Clear Form</button>
				<button type="submit" class="btn-save">Create Contact</button>
			</div>
		</form>
	</div>
@endsection