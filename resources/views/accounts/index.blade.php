@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/accounts/index.css') }}">
@endpush

@section('content')
	<div class="main-content-container">
		{{-- Header Section --}}
		<div class="accounts-header">
			<div class="header-title-group">
				<h2 class="card-title">Accounts</h2>
				<p class="text-muted">Εταιρική βάση δεδομένων & διαχείριση πελατών</p>
			</div>
			<a href="{{ route('accounts.create') }}" class="btn-add-account">
				<span>+</span> Νέος Λογαριασμός
			</a>
		</div>

		{{-- Table Card --}}
		<div class="card">
			<div class="table-container">
				<table class="table-custom">
					<thead>
					<tr>
						<th>Εταιρεία</th>
						<th>Κλάδος & Έσοδα</th>
						<th>Επικοινωνία</th>
						<th>Owner</th>
						<th>Status</th>
						<th class="text-end">Ενέργειες</th>
					</tr>
					</thead>
					<tbody>
					@forelse($accounts as $account)
						<tr>
							{{-- Εταιρεία --}}
							<td>
								<div class="account-info-cell">
									<div class="account-brand-icon">
										{{ strtoupper(substr($account->name, 0, 1)) }}
									</div>
									<div class="account-details">
										<span class="account-name">{{ $account->name }}</span>
										<span class="account-subtext">{{ $account->city }}</span>
									</div>
								</div>
							</td>

							{{-- Κλάδος & Έσοδα --}}
							<td class="text-right">
								<div class="revenue-group">
									<div class="revenue-text">
										{{ number_format($account->annual_revenue, 0, ',', '.') }} €
									</div>
									<div class="employee-label">
										<span>👥</span> {{ number_format($account->employee_count, 0, ',', '.') }} υπάλληλοι
									</div>
								</div>
							</td>

							{{-- Επικοινωνία (Primary Contact) --}}
							<td>
								@if($account->primary_contact)
									<div class="contact-info">
										<a href="mailto:{{ $account->primary_contact->email }}" class="contact-email">
											{{ $account->primary_contact->email }}
										</a>
										<span class="contact-phone">{{ $account->primary_contact->phone }}</span>
									</div>
								@else
									<span class="text-muted">Χωρίς Επαφή</span>
								@endif
							</td>

							{{-- Owner (Ο δικός σου υπάλληλος) --}}
							<td>
								<div class="assigned-user">
									<div class="avatar-mini">
										<img src="https://robohash.org/{{ md5($account->primary_contact->email) }}.png?size=64x64&set=set{{ $account->owner->profile?->user_id % 5 + 1 }}" alt="Avatar">
									</div>
									<span class="user-handle">{{ $account->owner->profile?->full_name ?? 'Unassigned' }}</span>
								</div>
							</td>

							{{-- Status --}}
							<td>
								<span class="status-pill {{ $account->is_active ? 'status-active' : 'status-inactive' }}">
									{{ $account->is_active ? 'Active' : 'Inactive' }}
								</span>
							</td>

							{{-- Ενέργειες --}}
							<td class="text-end">
								<div class="actions-group">
									<a href="{{ route('accounts.show', $account->id) }}" class="btn-action view" title="Προβολή">👁️</a>
									<a href="{{ route('accounts.edit', $account->id) }}" class="btn-action edit" title="Επεξεργασία">✏️</a>
									<form action="{{ route('accounts.destroy', $account->id) }}" method="POST" style="display:inline;">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn-action delete" title="Διαγραφή" onclick="return confirm('Διαγραφή λογαριασμού;')">🗑️</button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="5">
								<div class="empty-state">
									<p>Δεν βρέθηκαν λογαριασμοί στη βάση δεδομένων.</p>
								</div>
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			{{-- Custom Simple Pagination --}}
			{{-- Στο resources/views/accounts/index.blade.php --}}
			<div class="pagination-container">
				{{ $accounts->links('partials.pagination') }}
			</div>
		</div>
	</div>
@endsection