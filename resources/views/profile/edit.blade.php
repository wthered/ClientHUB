@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/profile/forms.css') }}">
@endpush

@section('content')
	<div class="profile-container">
		<div class="profile-card">
			<div class="profile-header">
				<h1 class="user-fullname">Ρυθμίσεις Προφίλ</h1>
				<p class="user-role">Διαχείριση προσωπικών στοιχείων και ταυτότητας</p>
			</div>

			<div class="profile-tabs">
				<a href="{{ route('profile.edit') }}" class="tab-item active">
					<i class="fas fa-user"></i> Προφίλ
				</a>
				<a href="{{ route('profile.settings.index') }}" class="tab-item">
					<i class="fas fa-shield-alt"></i> Ασφάλεια & Ειδοποιήσεις
				</a>
			</div>

			{{-- Προσθήκη enctype για το avatar upload --}}
			<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
				@csrf
				@method('PATCH')

				<div class="profile-body">

					{{-- SECTION: Avatar Upload --}}
					<div class="avatar-upload-section">
						<div class="current-avatar">
							@if($user->profile->avatar_url)
								<img src="{{ $user->profile->avatar_url }}" alt="Avatar" class="avatar-preview">
							@else
								<div class="avatar-placeholder">{{ $user->initials }}</div>
							@endif
						</div>
						<div class="upload-controls">
							<label for="avatar" class="form-label">Φωτογραφία Προφίλ</label>
							<input type="file" name="avatar" id="avatar" class="form-control @error('avatar') is-invalid @enderror">
							<small class="text-muted">Προτεινόμενο: Τετράγωνη εικόνα (JPG, PNG)</small>
							@error('avatar') <span class="error-text">{{ $message }}</span> @enderror
						</div>
					</div>

					<hr class="form-divider">

					{{-- SECTION: Personal Data (First & Last Name) --}}
					<div class="form-row">
						<div class="form-group">
							<label for="first_name">Όνομα</label>
							<input type="text" name="first_name" id="first_name"
							       class="form-control @error('first_name') is-invalid @enderror"
							       value="{{ old('first_name', $user->profile->first_name) }}" required>
							@error('first_name') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-group">
							<label for="last_name">Επώνυμο</label>
							<input type="text" name="last_name" id="last_name"
							       class="form-control @error('last_name') is-invalid @enderror"
							       value="{{ old('last_name', $user->profile->last_name) }}" required>
							@error('last_name') <span class="error-text">{{ $message }}</span> @enderror
						</div>
					</div>

					{{-- SECTION: Contact & Work --}}
					<div class="form-row">
						<div class="form-group">
							<label for="email">Email (Account)</label>
							<input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" readonly title="Το email αλλάζει μόνο από τις ρυθμίσεις ασφαλείας">
						</div>

						<div class="form-group">
							<label for="phone">Τηλέφωνο Επικοινωνίας</label>
							<input type="text" name="phone" id="phone"
							       class="form-control @error('phone') is-invalid @enderror"
							       value="{{ old('phone', $user->profile->phone) }}" placeholder="π.χ. 2101234567">
							@error('phone') <span class="error-text">{{ $message }}</span> @enderror
						</div>
					</div>

					<div class="form-group">
						<label for="position">Θέση / Τίτλος Εργασίας</label>
						<input type="text" name="position" id="position"
						       class="form-control @error('position') is-invalid @enderror"
						       value="{{ old('position', $user->profile->position) }}" placeholder="π.χ. Senior Sales Associate">
						@error('position') <span class="error-text">{{ $message }}</span> @enderror
					</div>

					<div class="form-group">
						<label for="bio">Βιογραφικό / Σημειώσεις</label>
						<textarea name="bio" id="bio" rows="4"
						          class="form-control @error('bio') is-invalid @enderror"
						          placeholder="Λίγα λόγια για εσάς...">{{ old('bio', $user->profile->bio) }}</textarea>
						@error('bio') <span class="error-text">{{ $message }}</span> @enderror
					</div>
				</div>

				<div class="profile-footer">
					<a href="{{ route('profile.show') }}" class="btn-cancel">Ακύρωση</a>
					<button type="submit" class="btn-submit">
						<i class="fas fa-save"></i> Ενημέρωση Στοιχείων
					</button>
				</div>
			</form>
		</div>
	</div>
@endsection