@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/payments/index.css') }}">
@endpush

@section('content')
	<div class="main-content">
		<div class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Payments</h1>
				<p class="module-subtitle">
					<i class="fas fa-money-check-alt"></i>
					History of financial transactions and payments
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('invoices.index') }}" class="btn-primary">
					<i class="fas fa-plus"></i> Select Invoice to Pay
				</a>
			</div>
		</div>

		<div class="card mb-4">
			<form action="{{ route('payments.index') }}" method="GET" class="filter-grid">
				{{-- Invoice ID Field --}}
				<div class="filter-group">
					<label class="filter-label" for="invoice_id">Invoice ID</label>
					<input type="text"
					       name="invoice_id"
					       id="invoice_id"
					       class="form-control"
					       placeholder="Search Invoice ID..."
					       value="{{ request('invoice_id') }}">
				</div>

				{{-- Method Field --}}
				<div class="filter-group">
					<label class="filter-label" for="method">Method</label>
					<select name="method" id="method" class="form-select">
						<option value="">All Methods</option>
						<option value="bank transfer" {{ request('method') == 'bank transfer' ? 'selected' : '' }}>Bank Transfer</option>
						<option value="credit card" {{ request('method') == 'credit card' ? 'selected' : '' }}>Credit Card</option>
						<option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
						<option value="stripe" {{ request('method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
					</select>
				</div>

				{{-- From Date --}}
				<div class="filter-group">
					<label class="filter-label" for="date_from">From Date</label>
					<input type="date" name="date_from" id="date_from" class="form-control" max="{{ today()->format('Y-m-d') }}" value="{{ request('date_from') }}">
				</div>

				{{-- To Date --}}
				<div class="filter-group">
					<label class="filter-label" for="date_to">To Date</label>
					<input type="date" name="date_to" id="date_to" class="form-control" max="{{ today()->format('Y-m-d') }}" min="{{ request('date_from') }}" value="{{ request('date_to') }}">
				</div>

				{{-- Actions --}}
				<div class="filter-actions">
					<button type="submit" class="btn-primary-filter">
						<i class="fas fa-filter"></i> Filter
					</button>

					<a href="{{ route('payments.index') }}" class="btn-sync-reset" title="Reset Filters">
						<i class="fas fa-sync-alt"></i>
					</a>
				</div>
			</form>
		</div>

		<div class="card" id="payments-table-container">
			@if(view()->exists('partials.payments.table'))
				@include('partials.payments.table', ['payments' => $payments])
			@else
				<p style="color: red;">Error: The table was not found!</p>
			@endif
		</div>
	</div>
@endsection

@push('scripts')
	<script src="{{ asset('js/payments/index.js') }}"></script>
@endpush
