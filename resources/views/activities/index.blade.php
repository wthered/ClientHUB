@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/activities/index.css') }}" />
@endpush

@section('content')
	<div class="container">
		@if(session('success'))
			<div class="alert alert-success" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 8px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
				<i class="fas fa-check-circle"></i> {{ session('success') }}
			</div>
		@endif
		<div class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Δραστηριότητες Συστήματος</h1>
				<p class="module-subtitle">Πλήρες ιστορικό ενεργειών και μεταβολών</p>
			</div>
		</div>

		<div class="card" style="margin-top: 1rem; padding: 1.5rem;">
			<form action="{{ route('activities.index') }}" method="GET" class="filter-grid">

				{{-- Αναζήτηση --}}
				<div class="filter-group">
					<span class="filter-label">Αναζήτηση</span>
					<input type="text" name="search" value="{{ request('search') }}"
					       placeholder="Περιγραφή δραστηριότητας..."
					       class="form-control @error('search') is-invalid @enderror">
					@error('search')
					<span class="error-text">{{ $message }}</span>
					@enderror
				</div>

				{{-- Χρήστης --}}
				<div class="filter-group">
					<span class="filter-label">Χρήστης</span>
					<select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
						<option value="">Όλοι οι χρήστες</option>
						@foreach($users as $user)
							<option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
								{{ $user->profile->full_name ?? $user->name }}
							</option>
						@endforeach
					</select>
					@error('user_id')
					<span class="error-text">{{ $message }}</span>
					@enderror
				</div>

				{{-- Οντότητα --}}
				<div class="filter-group">
					<span class="filter-label">Οντότητα</span>
					<select name="model" class="form-select @error('model') is-invalid @enderror">
						<option value="">Όλες οι οντότητες</option>
						@foreach($models as $model)
							<option value="{{ $model->value }}" {{ request('model') == $model->value ? 'selected' : '' }}>
								{{ $model->label() }}
							</option>
						@endforeach
					</select>
					@error('model')
					<span class="error-text">{{ $message }}</span>
					@enderror
				</div>

				{{-- Ημερομηνία Από - Έως --}}
				<div class="filter-group">
					<span class="filter-label">Ημερομηνία Από - Έως</span>
					<div class="flex-align-center" style="position: relative;">
						<input type="date" name="date_from"
						       value="{{ request('date_from') }}"
						       class="form-control @error('date_from') is-invalid @enderror"
						       max="{{ today()->format('Y-m-d') }}">

						<input type="date" name="date_to"
						       value="{{ request('date_to') }}"
						       class="form-control @error('date_to') is-invalid @enderror"
						       max="{{ today()->format('Y-m-d') }}"
						       min="{{ request('date_from') ?? '' }}">

						{{-- Εμφάνιση πρώτου λάθους που θα βρεθεί στις ημερομηνίες --}}
						@error('date_from')
							<span class="error-text">{{ $message }}</span>
						@enderror

						@error('date_to')
						<span class="error-text" style="{{ $errors->has('date_from') ? 'margin-left: 20px;' : '' }}">
                            {{ $message }}
                        </span>
						@enderror
					</div>
				</div>

				<div class="filter-actions">
					<button type="submit" class="btn-primary">
						<i class="fas fa-filter"></i> Φίλτρο
					</button>

					<button type="button" id="export_button"
					        class="btn btn-secondary"
					        data-url="{{ route('activities.export') }}">
						<i class="fas fa-file-export"></i> Εξαγωγή (CSV)
					</button>

					@hasanyrole('admin|super-admin')
						<button type="button" class="btn btn-danger" title="Διαγραφή logs παλαιότερων των 6 μηνών" style="background-color: #dc3545; color: white;" onclick="if(confirm('Προσοχή: Θα διαγραφούν οριστικά logs παλαιότερα των 6 μηνών. Συνέχεια;')) document.getElementById('clear-logs-form').submit();">
							<i class="fas fa-trash-alt"></i> Εκκαθάριση
						</button>
					@endhasanyrole

					<a href="{{ route('activities.index') }}" class="btn-secondary" title="Καθαρισμός">
						<i class="fas fa-times"></i>
					</a>
				</div>
			</form>

			@if(auth()->user()->hasAnyRole(['admin', 'super-admin']))
				<form id="clear-logs-form" action="{{ route('activities.clear') }}" method="POST" class="hidden">
					@csrf
				</form>
			@endif
		</div>

		{{-- Υπόλοιπο Template (Table & Modal) --}}
		<div class="card hidden" id="activity_table_container">
			<div id="table_loader" class="table-loading-overlay hidden">
				<div class="loader-content">
					<i class="fas fa-spinner fa-spin"></i> Φόρτωση δεδομένων...
				</div>
			</div>

			<div class="table-container">
				<table class="data-table">
					<thead>
						<tr>
							<th class="log-user">Χρήστης</th>
							<th class="log-action">Ενέργεια</th>
							<th class="log-description">Περιγραφή</th>
							<th class="log-entity">Οντότητα</th>
							<th class="log-date">Ημερομηνία</th>
							<th class="log-actions text-right">Ενέργειες</th>
						</tr>
					</thead>
					<tbody id="activity_rows"></tbody>
				</table>
			</div>
			<div id="pagination_container" class="card-footer"></div>
		</div>
	</div>

	{{-- Modal --}}
	<div id="details_modal" class="modal hidden">
		<div class="modal-content">
			<div class="modal-header">
				<h3>Λεπτομέρειες Μεταβολής</h3>
				<button type="button" class="close-modal">&times;</button>
			</div>
			<div class="modal-body">
				<div id="properties_container">
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script src="{{ asset('js/activities/search.js') }}"></script>
	<script src="{{ asset('js/activities/export.js') }}"></script>
	<script src="{{ asset('js/activities/modal.js') }}"></script>
@endpush