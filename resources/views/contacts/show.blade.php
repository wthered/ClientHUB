@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/contacts/show.css') }}">
@endpush

@section('content')
	<div class="main-content-container">
		{{-- Header --}}
		<header class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">{{ $contact->full_name }}</h1>
				<p class="module-subtitle">
					<i class="nav-icon">🏢</i> {{ $contact->account->name ?? 'No Account' }}
					• {{ $contact->job_title ?? $contact->position ?? 'No Title' }}
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('contacts.edit', $contact->id) }}" class="btn-action edit">✏️ Edit Contact</a>
				<a href="{{ route('contacts.index') }}" class="btn-action">⬅️ Back to List</a>
			</div>
		</header>

		<div class="show-grid">
			{{-- LEFT COLUMN: Profile & Info --}}
			<aside class="show-sidebar">
				<div class="profile-card">
					<div class="profile-avatar-large">
						@if($contact->avatar_url)
							<img src="{{ $contact->avatar_url }}" alt="{{ $contact->full_name }}" class="avatar-img">
						@else
							<span class="avatar-initials">
								{{ strtoupper(substr($contact->first_name, 0, 1) . substr($contact->last_name, 0, 1)) }}
							</span>
						@endif
					</div>
					<h3 class="profile-name">{{ $contact->full_name }}</h3>
					<span class="status-pill {{ $contact->is_primary ? 'status-active' : 'status-inactive' }}">
						{{ $contact->is_primary ? 'Primary Contact' : 'Secondary Contact' }}
					</span>

					{{-- ΕΔΩ ΜΠΑΙΝΟΥΝ ΤΑ STATS --}}
					<div class="profile-stats">
						<div class="stat-box">
							<span class="stat-value">€{{ number_format($contact->total_sales ?? 0) }}</span>
							<span class="stat-label">Sales</span>
						</div>
						<div class="stat-divider"></div>
						<div class="stat-box">
							<span class="stat-value">{{ $contact->last_contacted_at ? $contact->last_contacted_at->diffForHumans() : 'Never' }}</span>
							<span class="stat-label">Last Call</span>
						</div>
					</div>

					<div class="info-list">
						<div class="info-item">
							<label>Email</label>
							<a href="mailto:{{ $contact->email }}">{{ $contact->email ?? 'N/A' }}</a>
						</div>
						<div class="info-item">
							<label>Phone</label>
							<span>{{ $contact->phone ?? 'N/A' }}</span>
						</div>
						<div class="info-item">
							<label>Owner</label>
							<span>👤 {{ $contact->owner->name ?? 'Unassigned' }}</span>
						</div>
					</div>
				</div>
			</aside>

			{{-- RIGHT COLUMN: Content & Details --}}
			<main class="show-content">
				{{-- Location Card --}}
				<div class="card mb-4">
					<h4 class="card-section-title">📍 Location Details</h4>
					<div class="location-grid">
						<div>
							<label class="d-block text-muted small">Address</label>
							<p>{{ $contact->address ?? 'No address provided' }}</p>
						</div>
						<div>
							<label class="d-block text-muted small">City / Country</label>
							<p>{{ $contact->city }} {{ $contact->city && $contact->country ? ',' : '' }} {{ $contact->country }}</p>
						</div>
					</div>
				</div>

				{{-- Notes Card --}}
				<div class="card">
					<h4 class="card-section-title">📝 Internal Notes</h4>
					<div class="notes-body">
						{!! nl2br(e($contact->notes)) ?: '<span class="text-muted italic">No notes available for this contact.</span>' !!}
					</div>
				</div>
			</main>
		</div>
	</div>
@endsection