@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/contacts/index.css') }}">
@endpush

@section('content')
	<div class="main-content-container">
		{{-- Header Section --}}
		<div class="contacts-header">
			<div class="header-title-group">
				<h2 class="card-title">Contacts</h2>
				<p class="text-muted">Διαχείριση προσώπων επικοινωνίας & συνεργατών</p>
			</div>
			<a href="{{ route('contacts.create') }}" class="btn-add-contact">
				<span>+</span> Νέα Επαφή
			</a>
		</div>

		{{-- Table Card --}}
		<div class="card">
			<div class="table-container">
				<table class="table-custom">
					<thead>
					<tr>
						<th>Ονοματεπώνυμο</th>
						<th>Λογαριασμός / Εταιρεία</th>
						<th>Θέση & Ρόλος</th>
						<th>Επικοινωνία</th>
						<th>Owner</th>
						<th class="text-end">Ενέργειες</th>
					</tr>
					</thead>
					<tbody>
					@forelse($contacts as $contact)
						<tr>
							{{-- Ονοματεπώνυμο --}}
							<td>
								<div class="contact-info-cell">
									<div class="contact-avatar">
										{{-- Εδώ μπαίνει το νέο smart avatar logic --}}
										<div class="contact-avatar-wrapper">
											@if($contact->avatar_url)
												<img src="{{ $contact->avatar_url }}"
												     alt="{{ $contact->full_name }}"
												     class="contact-img">
											@endif

											<div class="contact-initials-placeholder"
											     style="display: {{ $contact->avatar_url ? 'none' : 'flex' }}; background-color: #eef2ff; color: #6366f1;">
												{{ $contact->initials }}
											</div>
										</div>
									</div>
									<div class="contact-details">
							            <span class="contact-name">
							                {{ $contact->full_name }}
								            @if($contact->is_primary)
									            <span class="badge-primary-dot" title="Primary Contact"></span>
								            @endif
							            </span>
										<span class="contact-subtext">{{ $contact->city ?? 'No Location' }}</span>
									</div>
								</div>
							</td>

							{{-- Λογαριασμός / Εταιρεία --}}
							<td>
								@if($contact->account)
									<div class="account-link-group">
										<a href="{{ route('accounts.show', $contact->account_id) }}" class="account-link">
											{{ $contact->account->name }}
										</a>
										<span class="industry-tag">{{ $contact->account->industry }}</span>
									</div>
								@else
									<span class="text-muted">—</span>
								@endif
							</td>

							{{-- Θέση & Ρόλος --}}
							<td>
								<div class="role-group">
									<div class="role-text">{{ $contact->job_title ?? 'Not Specified' }}</div>
									@if($contact->is_primary)
										<span class="status-pill status-active">Primary</span>
									@endif
								</div>
							</td>

							{{-- Επικοινωνία --}}
							<td>
								<div class="comms-group">
									<a href="mailto:{{ $contact->email }}" class="contact-email">{{ $contact->email }}</a>
									<span class="contact-phone">{{ $contact->phone ?? 'No Phone' }}</span>
								</div>
							</td>

							{{-- Owner --}}
							<td>
								<div class="assigned-user">
									<div class="avatar-mini">
										{{ strtoupper(substr($contact->owner->name ?? 'U', 0, 1)) }}
									</div>
									<span class="user-handle">{{ $contact->owner->profile ? $contact->owner->profile->full_name : 'Unassigned' }}</span>
								</div>
							</td>

							{{-- Ενέργειες --}}
							<td class="text-end">
								<div class="actions-group">
									{{-- View Button --}}
									<a href="{{ route('contacts.show', $contact->id) }}" class="btn-action view" title="Προβολή">👁️</a>

									{{-- Edit Button --}}
									<a href="{{ route('contacts.edit', $contact->id) }}" class="btn-action edit" title="Επεξεργασία">✏️</a>

									{{-- Delete Button --}}
									<form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" style="display:inline;">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn-action delete" title="Διαγραφή" onclick="return confirm('Διαγραφή επαφής;')">🗑️</button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6">
								<div class="empty-state">
									<p>Δεν βρέθηκαν επαφές.</p>
								</div>
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if($contacts instanceof \Illuminate\Pagination\AbstractPaginator)
				<div class="pagination-container">
					{{ $contacts->links('partials.pagination') }}
				</div>
			@endif
		</div>
	</div>
@endsection