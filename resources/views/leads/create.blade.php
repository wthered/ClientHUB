@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/leads/create.css') }}">
	<style>
        .form-input.is-invalid, .form-select.is-invalid, .form-textarea.is-invalid {
            border-color: #ef4444;
        }

        .error-text {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
        }
	</style>
@endpush

@section('content')
	<div class="main-content">
		<header class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">{{ __('leads.create_title') ?? 'Create New Lead' }}</h1>
				<p class="module-subtitle">
					<i class="nav-icon">👤</i> {{ __('leads.create_subtitle') ?? 'Enter potential customer details to start the pipeline.' }}
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('leads.index') }}" class="btn-action">{{ __('leads.cancel') ?? 'Cancel' }}</a>
				<button type="submit" form="lead-create-form" class="btn-action edit">{{ __('leads.save') ?? 'Save Lead' }}</button>
			</div>
		</header>

		<form action="{{ route('leads.store') }}" method="POST" id="lead-create-form">
			@csrf

			<div class="form-grid">
				{{-- Left Column: Core Identity --}}
				<div class="form-column">
					<div class="card @if($errors->has('first_name') || $errors->has('last_name') || $errors->has('job_title') || $errors->has('email') || $errors->has('phone')) has-errors @endif">
						<h3 class="card-title">{{ __('leads.personal_info') ?? 'Personal Information' }}</h3>

						<div class="form-group-row">
							<div class="form-group">
								<label for="first_name">{{ __('leads.first_name') }} *</label>
								<input type="text" name="first_name" id="first_name" class="form-input @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
								@error('first_name') <span class="error-text">{{ $message }}</span> @enderror
							</div>
							<div class="form-group">
								<label for="last_name">{{ __('leads.last_name') }} *</label>
								<input type="text" name="last_name" id="last_name" class="form-input @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
								@error('last_name') <span class="error-text">{{ $message }}</span> @enderror
							</div>
						</div>

						<div class="form-group">
							<label for="job_title">{{ __('leads.job_title') }}</label>
							<input type="text" name="job_title" id="job_title" class="form-input @error('job_title') is-invalid @enderror" value="{{ old('job_title') }}">
							@error('job_title') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group-row">
							<div class="form-group">
								<label for="email">{{ __('leads.email') }}</label>
								<input type="email" name="email" id="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}">
								@error('email') <span class="error-text">{{ $message }}</span> @enderror
							</div>
							<div class="form-group">
								<label for="phone">{{ __('leads.phone') }}</label>
								<input type="text" name="phone" id="phone" class="form-input @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
								@error('phone') <span class="error-text">{{ $message }}</span> @enderror
							</div>
						</div>
					</div>

					<div class="card @if($errors->has('company_name') || $errors->has('website')) has-errors @enderror">
						<h3 class="card-title">{{ __('leads.company_details') ?? 'Company Details' }}</h3>
						<div class="form-group">
							<label for="company_name">{{ __('leads.company') }}</label>
							<input type="text" name="company_name" id="company_name" class="form-input @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}">
							@error('company_name') <span class="error-text">{{ $message }}</span> @enderror
						</div>
						<div class="form-group">
							<label for="website">{{ __('leads.website') }}</label>
							<input type="url" name="website" id="website" class="form-input @error('website') is-invalid @enderror" placeholder="https://" value="{{ old('website') }}">
							@error('website') <span class="error-text">{{ $message }}</span> @enderror
						</div>
					</div>
				</div>

				{{-- Right Column: Tracking & Value --}}
				<div class="form-column">
					<div class="card mb-4 @if($errors->has('status') || $errors->has('priority') || $errors->has('source') || $errors->has('estimated_value')) has-errors @endif">
						<h3 class="card-title">{{ __('leads.strategy') ?? 'Lead Strategy' }}</h3>

						<div class="form-group">
							<label for="status">{{ __('leads.status.label') }}</label>
							<select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
								@foreach(\App\Enums\Leads\LeadStatus::cases() as $status)
									<option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
										{{ $status->label() }}
									</option>
								@endforeach
							</select>
							@error('status') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group">
							<label for="priority">{{ __('leads.priority.label') }}</label>
							<select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
								@foreach(\App\Enums\Leads\LeadPriority::cases() as $priority)
									<option value="{{ $priority->value }}" {{ old('priority', 'medium') == $priority->value ? 'selected' : '' }}>
										{{ $priority->label() }}
									</option>
								@endforeach
							</select>
							@error('priority') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group">
							<label for="source">{{ __('leads.source') }}</label>
							<select name="source" id="source" class="form-select @error('source') is-invalid @enderror">
								@foreach(['manual', 'website', 'referral', 'linkedin'] as $source)
									<option value="{{ $source }}" {{ old('source') == $source ? 'selected' : '' }}>
										{{ __('leads.sources.' . $source) }}
									</option>
								@endforeach
							</select>
							@error('source') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group">
							<label for="estimated_value">{{ __('leads.estimated_value') }} (€)</label>
							<input type="number" step="0.01" name="estimated_value" id="estimated_value" class="form-input @error('estimated_value') is-invalid @enderror" placeholder="0.00" value="{{ old('estimated_value') }}">
							@error('estimated_value') <span class="error-text">{{ $message }}</span> @enderror
						</div>
					</div>

					<div class="card @error('notes') has-errors @enderror">
						<h3 class="card-title">{{ __('leads.notes') }}</h3>
						<div class="form-group">
							<textarea name="notes" id="notes" class="form-textarea @error('notes') is-invalid @enderror" rows="5" placeholder="Context about this lead...">{{ old('notes') }}</textarea>
							@error('notes') <span class="error-text">{{ $message }}</span> @enderror
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection