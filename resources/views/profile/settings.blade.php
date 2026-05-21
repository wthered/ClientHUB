@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/profile/forms.css') }}">
@endpush

@section('content')
	<div class="profile-container">
		<div class="profile-card">
			<div class="profile-header">
				<h1 class="user-fullname">Ρυθμίσεις Λογαριασμού</h1>
				<p class="user-role">Διαχείριση ασφάλειας και προτιμήσεων συστήματος</p>
			</div>

			<div class="profile-tabs">
				<a href="{{ route('profile.edit') }}" class="tab-item">
					<i class="fas fa-user"></i> Προφίλ
				</a>
				<a href="{{ route('profile.settings.index') }}" class="tab-item active">
					<i class="fas fa-shield-alt"></i> Ασφάλεια & Ειδοποιήσεις
				</a>
			</div>

			<div class="profile-body">

				{{-- ΕΝΟΤΗΤΑ 1: ΑΛΛΑΓΗ ΚΩΔΙΚΟΥ --}}
				<section class="settings-section">
					<h3 class="settings-title">
						<i class="fas fa-shield-alt"></i> Ασφάλεια Πρόσβασης
					</h3>

					<form action="{{ route('profile.settings.security') }}" method="POST">
						@csrf
						@method('PATCH')

						<div class="form-group">
							<label for="current_password">Τρέχων Κωδικός</label>
							<input type="password" name="current_password" id="current_password"
							       class="form-control @error('current_password') is-invalid @enderror" required>
							@error('current_password') <span class="error-text">{{ $message }}</span> @enderror
						</div>

						<div class="form-row">
							<div class="form-group">
								<label for="password">Νέος Κωδικός</label>
								<input type="password" name="password" id="password"
								       class="form-control @error('password') is-invalid @enderror" required>
								@error('password') <span class="error-text">{{ $message }}</span> @enderror
							</div>

							<div class="form-group">
								<label for="password_confirmation">Επιβεβαίωση Νέου Κωδικού</label>
								<input type="password" name="password_confirmation" id="password_confirmation"
								       class="form-control" required>
							</div>
						</div>

						<div class="text-end" style="margin-top: 10px;">
							<button type="submit" class="btn-submit">
								<i class="fas fa-key"></i> Ενημέρωση Κωδικού
							</button>
						</div>
					</form>
				</section>

				{{-- ΕΝΟΤΗΤΑ 2: ΕΙΔΟΠΟΙΗΣΕΙΣ (PREFERENCES) --}}
				<section class="settings-section">
					<h3 class="settings-title">
						<i class="fas fa-bell"></i> Ειδοποιήσεις
					</h3>
					<p class="help-text">Επιλέξτε πώς θέλετε να ενημερώνεστε για τις δραστηριότητες στο CRM.</p>

					<div class="settings-list">
						{{-- Toggle 1 --}}
						<div class="setting-item">
							<div class="setting-info">
								<span class="setting-label">Email αναθέσεων</span>
								<p class="setting-description">Λήψη email κάθε φορά που σας ανατίθεται μια νέα πώληση.</p>
							</div>
							<label class="switch">
								<input type="checkbox" name="notif_sales" id="checkbox_sales" {{ $profile->notify_on_sales ? 'checked' : '' }}>
								<span class="slider round"></span>
							</label>
						</div>

						{{-- Toggle 2 --}}
						<div class="setting-item">
							<div class="setting-info">
								<span class="setting-label">Εβδομαδιαίο Report</span>
								<p class="setting-description">Σύνοψη της απόδοσής σας κάθε Δευτέρα πρωί.</p>
							</div>
							<label class="switch">
								<input type="checkbox" name="notif_report" id="checkbox_report" {{ $profile->notify_on_report ? 'checked' : '' }}>
								<span class="slider round"></span>
							</label>
						</div>
					</div>
				</section>

				<hr class="form-divider">

				{{-- ΕΝΟΤΗΤΑ 3: ΠΕΡΙΟΧΗ ΚΙΝΔΥΝΟΥ --}}
				<section class="danger-zone">
					<h3 class="settings-title" style="color: var(--danger); border-color: rgba(231, 76, 60, 0.1);">
						<i class="fas fa-exclamation-triangle"></i> Περιοχή Κινδύνου
					</h3>
					<p class="help-text">
						Η διαγραφή του λογαριασμού είναι μόνιμη. Όλα τα προσωπικά σας δεδομένα θα αφαιρεθούν, αλλά οι ιστορικές πωλήσεις θα παραμείνουν στο σύστημα για στατιστικούς λόγους.
					</p>

					<form action="{{ route('profile.settings.destroy') }}" method="POST" onsubmit="return confirm('Είστε απόλυτα σίγουροι; Αυτή η ενέργεια δεν αναιρείται.')">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn-danger" style="width: 100%; border: 1px solid var(--danger); background: transparent; color: var(--danger); padding: 12px; border-radius: var(--radius); cursor: pointer; font-weight: 700; transition: var(--transition);">
							Διαγραφή Λογαριασμού
						</button>
					</form>
				</section>

			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script src="{{ asset('js/profile/settings.js') }}"></script>
@endpush