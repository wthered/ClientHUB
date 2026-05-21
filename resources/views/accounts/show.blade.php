@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/accounts/show.css') }}">
@endpush

@section('content')
	<div class="accounts-container">
		<header class="module-header">
			<div class="header-title-group">
				<h2 class="module-title">{{ $account->name }}</h2>
				<p class="module-subtitle">
					<i class="fas fa-map-marker-alt"></i> {{ $account->city ?? 'No City Set' }} |
					<i class="fas fa-industry"></i> {{ $account->industry ?? 'Other' }}
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('accounts.edit', $account) }}" class="btn-action edit">
					<i class="fas fa-edit"></i> Edit
				</a>
				<form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline-form">
					@csrf @method('DELETE')
					<button type="submit" class="btn-action delete" onclick="return confirm('Are you sure?')">
						<i class="fas fa-trash"></i> Delete
					</button>
				</form>
			</div>
		</header>

		<section class="stats-grid">
			<div class="stat-card">
				<span class="stat-label">Total Value</span>
				<span class="stat-value">{{ number_format($account->total_opportunity_value, 0, ',', '.') }} €</span>
			</div>
			<div class="stat-card">
				<span class="stat-label">Open Opportunities</span>
				<span class="stat-value text-primary">{{ $account->open_opportunities_count }}</span>
			</div>
			<div class="stat-card">
				<span class="stat-label">Employees</span>
				<span class="stat-value">{{ number_format($account->employee_count, 0, ',', '.') }}</span>
			</div>
			<div class="stat-card">
				<span class="stat-label">Status</span>
				<span class="status-pill {{ $account->is_active ? 'status-active' : 'status-inactive' }}">
                {{ $account->is_active ? 'Active' : 'Inactive' }}
            </span>
			</div>
		</section>

		<div class="account-content-grid" data-account-id="{{ $account->id }}">
			<main class="account-main">
				<div class="card tabs-card">
					<nav class="tabs-header">
						<button class="tab-link active" data-tab="overview">Overview</button>
						<button class="tab-link" data-tab="contacts">Contacts</button>
						<button class="tab-link" data-tab="invoices">Invoices</button>
						<button class="tab-link" data-tab="notes">Notes</button>
					</nav>
					<div class="tab-content">
						<div id="overview" class="tab-pane active">
							<div class="overview-section">
								<h3>General Information</h3>
								<div class="details-grid">
									<div class="detail-item">
										<strong>Website:</strong>
										<a href="{{ $account->website }}" target="_blank" class="text-primary">
											{{ $account->website }} <i class="fas fa-external-link-alt fa-xs"></i>
										</a>
									</div>
									<div class="detail-item">
										<strong>Address:</strong>
										<a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($account->full_address) }}"
										   target="_blank"
										   class="address-link">
											{{ $account->full_address }} <i class="fas fa-map-marker-alt fa-xs"></i>
										</a>
									</div>
								</div>
							</div>
						</div>

						<div id="contacts" class="tab-pane">
							<div class="section-header">
								<h3>Contacts</h3>
								<button type="button" class="btn-primary-sm" id="addContactBtn">
									New Contact
								</button>
							</div>
							<p class="text-muted">Εδώ θα μπει η λίστα των επαφών (πίνακας)...</p>
						</div>

						<div id="invoices" class="tab-pane">
							<h3>Invoices</h3>
							<p class="text-muted">Εδώ θα μπουν τα τιμολόγια...</p>
						</div>

						<div id="notes" class="tab-pane">
							<h3>Notes</h3>
							<p class="text-muted">Εδώ θα μπουν οι σημειώσεις...</p>
						</div>
					</div>
				</div>
			</main>

			<aside class="account-sidebar">
				<div class="card owner-card">
					<h4>Account Owner</h4>
					<div class="assigned-user">
						<div class="avatar-mini">
							<img src="https://robohash.org/{{ md5($account->owner->email ?? 'default') }}.png?set=set3" alt="Owner">
						</div>
						<span class="user-handle">{{ $account->owner->username ?? 'Unassigned' }}</span>
					</div>
				</div>
			</aside>
		</div>
	</div>

	<div id="contactModal" class="modal-overlay">
		<div class="modal-card">
			<div class="modal-header">
				<h3>Add New Contact</h3>
				<button type="button" class="close-modal" onclick="closeContactModal()">&times;</button>
			</div>

			<form action="{{ route('contacts.store') }}" method="POST">
				@csrf
				<input type="hidden" name="account_id" value="{{ $account->id }}">

				<div class="modal-body">
					<div class="form-group">
						<label for="first_name">First Name</label>
						<input type="text" id="first_name" name="first_name" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="last_name">Last Name</label>
						<input type="text" id="last_name" name="last_name" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" id="email" name="email" class="form-control">
					</div>
					<div class="form-group">
						<label for="position">Position / Role</label>
						<input type="text" id="position" name="position" class="form-control" placeholder="e.g. Manager">
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn-secondary-sm" onclick="closeContactModal()">Cancel</button>
					<button type="submit" class="btn-primary-sm">Save Contact</button>
				</div>
			</form>
		</div>
	</div>
	@endsection

@push('scripts')
	<script src="{{ asset('js/accounts/show.js') }}"></script>
@endpush