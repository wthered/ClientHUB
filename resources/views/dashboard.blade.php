@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('page_title', 'Dashboard')

@section('content')
	<header class="dashboard-header">
		<h1 class="h1">Πίνακας Ελέγχου</h1>
		<p class="text-muted">
			Καλώς ήρθες, <strong>{{ auth()->user()->name }}</strong>.
			Η τελευταία σου είσοδος ήταν {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'τώρα' }}.
		</p>
	</header>

	<div class="stats-grid">
		<div class="stat-card">
			<div class="stat-icon bg-primary-light">👥</div>
			<div class="stat-content">
				<span class="stat-label">Ενεργοί Χρήστες</span>
				<span class="stat-value">{{ $onlineUsers->count() }}</span>
			</div>
		</div>
		<div class="stat-card">
			<div class="stat-icon bg-success-light">💰</div>
			<div class="stat-content">
				<span class="stat-label">Ανοιχτές Ευκαιρίες</span>
				<span class="stat-value">{{ $openOpportunities }}</span> </div>
		</div>
		<div class="stat-card">
			<div class="stat-icon bg-warning-light">📅</div>
			<div class="stat-content">
				<span class="stat-label">Σημερινές Δραστηριότητες</span>
				<span class="stat-value">{{ $todayActivitiesCount }}</span>
			</div>
		</div>
	</div>

	<div class="dashboard-grid">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">👥 Συνδεδεμένοι Χρήστες</h3>
				<span class="badge badge-success">Live</span>
			</div>
			<div class="user-list">
				@forelse($onlineUsers as $user)
					<div class="user-item">
						<div class="user-info">
							{{-- 1. Το Avatar Wrapper με το status indicator μέσα --}}
							<div class="user-avatar-wrapper" style="width: 32px; height: 32px;">
								@if($user->avatar_url)
									<img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="user-avatar-img">
								@else
									<div class="user-avatar-placeholder bg-primary-light" style="font-size: 0.75rem;">
										{{ $user->initials }}
									</div>
								@endif
								<span class="status-indicator online"></span>
							</div>

							<span class="user-name">{{ $user->name }}</span>
						</div>

						<time class="user-meta">{{ $user->last_login_at->diffForHumans() }}</time>
					</div>
				@empty
					<p class="empty-state">Δεν υπάρχουν άλλοι ενεργοί χρήστες.</p>
				@endforelse
			</div>
		</div>

		<div class="card">
			<div class="card-header">
				<h3 class="card-title">🛡️ Πρόσφατα Audit Logs</h3>
				<a href="{{ route('audit-logs.index') }}" class="btn-link">Προβολή όλων</a>
			</div>
			<div class="logs-container">
				@foreach($recentAuditLogs as $log)
					<div class="log-item">
						<div class="log-main">
							<span class="log-event event-{{ strtolower($log->event) }}">{{ strtoupper($log->event) }}</span>
							<p class="log-desc">{{ $log->description }}</p>
						</div>
						<div class="log-meta">
							<span>{{ $log->created_at->format('H:i:s') }}</span>
							<span class="divider">|</span>
							<span>IP: {{ $log->ip_address }}</span>
							<span class="divider">|</span>
							<span class="log-type">{{ $log->log_type }}</span>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endsection