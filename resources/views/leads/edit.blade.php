@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/leads/create.css') }}">
	<style>
        .input-error {
            border-color: #ef4444 !important;
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
				<h1 class="module-title">{{ __('leads.edit_title') }}: {{ $lead->full_name }}</h1>
				<p class="module-subtitle">
					<i class="nav-icon">📝</i> {{ __('leads.updated') ?? 'Last updated' }} {{ $lead->updated_at->diffForHumans() }}
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('leads.index') }}" class="btn-action">{{ __('leads.cancel') }}</a>
				<button type="submit" form="lead-edit-form" class="btn-action edit">{{ __('leads.save') }}</button>
			</div>
		</header>

		<form action="{{ route('leads.update', $lead->id) }}" method="POST" id="lead-edit-form">
			@csrf
			@method('PUT')

			<div class="form-grid">
				{{-- Left Column: Core Identity --}}
				<div class="form-column">
					<div class="card mb-4 @if($errors->hasAny(['first_name', 'last_name', 'job_title', 'email', 'phone'])) has-errors @endif">
						<h3 class="card-title">{{ __('leads.personal_info') }}</h3>

						<div class="form-group-row">
							<div class="form-group">
								<label for="first_name">{{ __('leads.first_name') }} *</label>
								<input type="text" name="first_name" id="first_name"
								       class="form-input @error('first_name') input-error @enderror"
								       value="{{ old('first_name', $lead->first_name) }}" required>
								@error('first_name') <span class="error-text">{{ $message }}</span> @enderror
							</div>

							<div class="form-group">
								<label for="last_name">{{ __('leads.last_name') }} *</label>
								<input type="text" name="last_name" id="last_name"
								       class="form-input @error('last_name') input-error @enderror"
								       value="{{ old('last_name', $lead->last_name) }}" required>
								@error('last_name') <span class="error-text">{{ $message }}</span> @enderror
							</div>
						</div>

						<div class="form-group">
							<label for="job_title">{{ __('leads.job_title') }}</label>
							<input type="text" name="job_title" id="job_title"
							       class="form-input @error('job_title') input-error @enderror"
							       value="{{ old('job_title', $lead->job_title) }}">
							@error('job_title') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group-row">
							<div class="form-group">
								<label for="email">{{ __('leads.email') }} *</label>
								<input type="email" name="email" id="email"
								       class="form-input @error('email') input-error @enderror"
								       value="{{ old('email', $lead->email) }}" required>
								@error('email') <span class="error-text">{{ $message }}</span> @enderror
							</div>

							<div class="form-group">
								<label for="phone">{{ __('leads.phone') }}</label>
								<input type="text" name="phone" id="phone"
								       class="form-input @error('phone') input-error @enderror"
								       value="{{ old('phone', $lead->phone) }}">
								@error('phone') <span class="error-text">{{ $message }}</span> @enderror
							</div>
						</div>
					</div>

					<div class="card @if($errors->hasAny(['company_name', 'website'])) has-errors @endif">
						<h3 class="card-title">{{ __('leads.company_details') }}</h3>
						<div class="form-group">
							<label for="company_name">{{ __('leads.company') }}</label>
							<input type="text" name="company_name" id="company_name"
							       class="form-input @error('company_name') input-error @enderror"
							       value="{{ old('company_name', $lead->company_name) }}">
							@error('company_name') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group">
							<label for="website">{{ __('leads.website') }}</label>
							<input type="url" name="website" id="website"
							       class="form-input @error('website') input-error @enderror"
							       value="{{ old('website', $lead->website) }}" placeholder="https://...">
							@error('website') <span class="error-text">{{ $message }}</span> @enderror
						</div>
					</div>
				</div>

				{{-- Right Column: Tracking & Status --}}
				<div class="form-column">
					<div class="card mb-4 @if($errors->hasAny(['status', 'priority', 'source', 'estimated_value', 'is_active'])) has-errors @endif">
						<h3 class="card-title">{{ __('leads.strategy') }}</h3>

						<div class="form-group">
							<label for="status">{{ __('leads.status.label') }}</label>
							<select name="status" id="status" class="form-select @error('status') input-error @enderror">
								@foreach(\App\Enums\Leads\LeadStatus::cases() as $status)
									@if($status !== \App\Enums\Leads\LeadStatus::CONVERTED || $lead->status === \App\Enums\Leads\LeadStatus::CONVERTED)
										<option value="{{ $status->value }}" {{ old('status', $lead->status->value) == $status->value ? 'selected' : '' }}>
											{{ $status->label() }}
										</option>
									@endif
								@endforeach
							</select>
							@error('status') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group-row">
							<div class="form-group">
								<label for="priority">{{ __('leads.priority.label') }}</label>
								<select name="priority" id="priority" class="form-select @error('priority') input-error @enderror">
									@foreach(\App\Enums\Leads\LeadPriority::cases() as $priority)
										<option value="{{ $priority->value }}" {{ old('priority', $lead->priority->value) == $priority->value ? 'selected' : '' }}>
											{{ $priority->label() }}
										</option>
									@endforeach
								</select>
								@error('priority') <span class="error-text">{{ $message }}</span> @enderror
							</div>

							<div class="form-group">
								<label for="source">{{ __('leads.source') }}</label>
								<select name="source" id="source" class="form-select @error('source') input-error @enderror">
									@foreach(['manual', 'website', 'referral', 'linkedin'] as $source)
										<option value="{{ $source }}" {{ old('source', $lead->source) == $source ? 'selected' : '' }}>
											{{ __('leads.sources.' . $source) }}
										</option>
									@endforeach
								</select>
								@error('source') <span class="error-text">{{ $message }}</span> @enderror
							</div>
						</div>

						<div class="form-group">
							<label for="estimated_value">{{ __('leads.estimated_value') }}</label>
							<div class="input-with-icon" style="position: relative;">
								<span class="icon" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);">€</span>
								<input type="number" name="estimated_value" id="estimated_value"
								       class="form-input @error('estimated_value') input-error @enderror"
								       style="padding-left: 25px;"
								       step="0.01" value="{{ old('estimated_value', $lead->estimated_value) }}" placeholder="0.00">
							</div>
							@error('estimated_value') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group">
							<label class="form-checkbox-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
								<input type="checkbox" name="is_active" value="1"{{ old('is_active', $lead->is_active) ? 'checked' : '' }}>
								{{ __('leads.active_status') }}
							</label>
							@error('is_active')
							<span class="error-text">{{ $message }}</span>
							@enderror
						</div>
					</div>

					<div class="card @error('notes') has-errors @enderror">
						<h3 class="card-title">{{ __('leads.notes') }}</h3>
						<div class="form-group">
                        <textarea name="notes" id="notes"
                                  class="form-textarea @error('notes') input-error @enderror"
                                  rows="6">{{ old('notes', $lead->notes) }}</textarea>
							@error('notes') <span class="error-text">{{ $message }}</span> @enderror
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection