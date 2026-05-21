@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/profile/show.css') }}">
@endpush

@section('content')
	<div class="profile-container">
		<div class="profile-card">
			<div class="profile-header">
				{{-- Λογική Avatar: Εικόνα ή Initials --}}
				@if(auth()->user()->profile && auth()->user()->profile->avatar_url)
					<div class="avatar-container">
						<img src="{{ auth()->user()->profile->avatar_url }}" alt="Avatar" class="avatar-img-large">
					</div>
				@else
					<div class="avatar-large">
						{{ $user->initials }}
					</div>
				@endif

				<h1 class="user-fullname">
					{{ $user->profile->first_name ?? 'N/A' }} {{ $user->profile->last_name ?? '' }}
				</h1>

				{{-- Παίρνουμε το position από το profile --}}
				<span class="user-role">{{ $user->profile->position ?? 'Member' }}</span>
			</div>

			<div class="profile-body">
				<div class="info-row">
					<div class="info-group">
						<label><i class="fas fa-envelope"></i> Email Διεύθυνση</label>
						<p>{{ $user->email }}</p>
					</div>

					<div class="info-group">
						<label><i class="fas fa-phone"></i> Τηλέφωνο</label>
						<p>{{ $user->profile->phone ?? '-' }}</p>
					</div>
				</div>

				<div class="info-row">
					<div class="info-group">
						<label><i class="fas fa-calendar-alt"></i> Ημερομηνία Εγγραφής</label>
						<p>{{ $user->created_at->format('d/m/Y') }}</p>
					</div>

					<div class="info-group">
						<label><i class="fas fa-id-badge"></i> Ρόλος Συστήματος</label>
						<p>{{ $user->getRoleNames()->first() ?? 'User' }}</p>
					</div>
				</div>

				@if($user->profile && $user->profile->bio)
					<div class="info-group full-width">
						<label><i class="fas fa-info-circle"></i> Βιογραφικό</label>
						<p class="bio-text">{{ $user->profile->bio }}</p>
					</div>
				@endif
			</div>

			<div class="profile-footer">
				<a href="{{ route('profile.edit') }}" class="btn-edit">
					<i class="fas fa-user-edit"></i> Επεξεργασία Προφίλ
				</a>
			</div>
		</div>
	</div>
@endsection