@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/leads/show.css') }}">
@endpush

@section('content')
	<div class="main-content">
		<header class="module-header">
			<div class="header-title-group">
				<div class="lead-profile-summary">
					<div class="avatar-circle large">
						{{-- Logic remains same, but using the full_name accessor --}}
						{{ strtoupper(substr($lead->first_name, 0, 1) . substr($lead->last_name, 0, 1)) }}
					</div>
					<div class="lead-identity">
						<h1 class="module-title">{{ $lead->full_name }}</h1>
						<p class="module-subtitle">
							{{ $lead->job_title ?? __('leads.job_title') }} @ {{ $lead->company_name ?? __('leads.individual') ?? 'Individual' }}
						</p>
					</div>
				</div>
			</div>
			<div class="header-actions">
				<a href="{{ route('leads.index') }}" class="btn-action">{{ __('leads.back') ?? 'Back' }}</a>
				<a href="{{ route('leads.edit', $lead->id) }}" class="btn-action edit">{{ __('leads.edit_title') ?? 'Edit Lead' }}</a>

				@if(!$lead->isConverted())
					<button class="btn-action success" onclick="openConversionModal()">{{ __('leads.convert_action') ?? 'Convert to Contact' }}</button>
				@endif
			</div>
		</header>

		<div class="show-grid">
			{{-- Sidebar --}}
			<aside class="show-sidebar">
				<div class="card mb-4">
					<h3 class="card-title">{{ __('leads.contact_details') ?? 'Contact Details' }}</h3>
					<div class="info-item">
						<span class="label">{{ __('leads.email') }}</span>
						<a href="mailto:{{ $lead->email }}" class="value link">{{ $lead->email ?? 'N/A' }}</a>
					</div>
					<div class="info-item">
						<span class="label">{{ __('leads.phone') }}</span>
						<span class="value">{{ $lead->phone ?? 'N/A' }}</span>
					</div>
					<div class="info-item">
						<span class="label">{{ __('leads.website') }}</span>
						<a href="{{ $lead->website }}" target="_blank" class="value link">{{ $lead->website ?? 'N/A' }}</a>
					</div>
				</div>

				<div class="card">
					<h3 class="card-title">{{ __('leads.metadata') ?? 'Metadata' }}</h3>
					<div class="info-item">
						<span class="label">{{ __('leads.source') }}</span>
						{{-- Check if source has a translation, otherwise fallback to ucfirst --}}
						<span class="value">{{ __('leads.sources.' . strtolower($lead->source)) ?? ucfirst($lead->source) }}</span>
					</div>
					<div class="info-item">
						<span class="label">{{ __('leads.owner') }}</span>
						<span class="value">{{ $lead->owner->profile->full_name ?? 'System' }}</span>
					</div>
				</div>
			</aside>

			{{-- Main Content --}}
			<main class="show-main">
				<div class="card mb-4">
					<h3 class="card-title">{{ __('leads.notes') }}</h3>
					<div class="notes-content">
						{!! nl2br(e($lead->notes ?? __('leads.no_notes') ?? 'No notes available.')) !!}
					</div>
				</div>

				<div class="card">
					<h3 class="card-title">{{ __('leads.company_details') }}</h3>
					<div class="company-details-grid">
						<div class="info-item">
							<span class="label">{{ __('leads.organization') ?? 'Organization' }}</span>
							<span class="value bold">{{ $lead->company_name ?? __('leads.individual') ?? 'Individual' }}</span>
						</div>
					</div>
				</div>
			</main>

			{{-- Status Panel --}}
			<aside class="show-status">
				<div class="card status-card">
					<h3 class="card-title">{{ __('leads.pipeline') ?? 'Pipeline' }}</h3>

					{{-- Fixed the strtoupper error by using the Enum label() and dynamic colors --}}
					<div class="status-badge-large"
					     style="background-color: {{ $lead->status->bgColor() }}; color: {{ $lead->status->color() }}; border: 1px solid {{ $lead->status->color() }}44;">
						{{ strtoupper($lead->status->label()) }}
					</div>

					<div class="priority-tag" style="color: {{ $lead->priority->color() }}; font-weight: bold; margin-top: 1rem; display: block;">
						● {{ $lead->priority->label() }}
					</div>
				</div>

				<div class="card value-card">
					<h3 class="card-title">{{ __('leads.estimated_value') }}</h3>
					<div class="currency-value">
						€{{ number_format($lead->estimated_value, 2) }}
					</div>
				</div>
			</aside>
		</div>
	</div>

	{{-- Conversion Modal --}}
	<div id="conversionModal" class="modal-overlay" style="display: none;">
		<div class="modal-card card">
			<div class="modal-header">
				<h2 class="module-title">{{ __('leads.convert_lead') ?? 'Convert Lead' }}</h2>
				<p class="module-subtitle">Convert {{ $lead->full_name }} into a permanent Contact and Account.</p>
			</div>

			<form action="{{ route('leads.convert', $lead->id) }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="info-item">
						<span class="label">{{ __('leads.organization_name') ?? 'Organization Name' }}</span>
						<input type="text" name="account_name" class="form-input"
						       value="{{ $lead->company_name ?? $lead->last_name . ' Household' }}" required>
					</div>

					<div class="checkbox-wrapper">
						<input type="checkbox" id="create_opp" name="create_opportunity" class="custom-checkbox" checked>
						<label for="create_opp" class="checkbox-label">
							<span class="checkbox-box"></span>
							<span class="checkbox-text">Create a new Opportunity for this contact</span>
						</label>
					</div>
				</div>

				<div class="modal-footer header-actions">
					<button type="button" class="btn-action" onclick="closeConversionModal()">Cancel</button>
					<button type="submit" class="btn-action success">Finalize Conversion</button>
				</div>
			</form>
		</div>
	</div>
@endsection