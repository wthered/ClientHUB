@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/invoices/index.css') }}">
@endpush

@section('content')
	<div class="main-content">
		<div class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Invoices</h1>
				<p class="module-subtitle">
					<i class="fas fa-file-invoice-dollar"></i>
					Management of billing and financial records
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('invoices.create') }}" class="btn-primary">
					<i class="fas fa-plus"></i> New Invoice
				</a>
			</div>
		</div>

		<div class="card mb-4">
			<form action="{{ route('invoices.index') }}" method="GET" class="filter-grid">
				{{-- Search Field --}}
				<div class="filter-group">
					<label class="filter-label" for="search">Search</label>
					<input type="text"
					       name="search"
					       id="search"
					       class="form-control @error('search') is-invalid @enderror"
					       placeholder="INV-2026..."
					       value="{{ request('search') }}">

					@error('search')
					<span class="error-text">{{ $message }}</span>
					@enderror
				</div>

				{{-- Status Field --}}
				<div class="filter-group">
					<label class="filter-label" for="status">Status</label>
					<select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
						<option value="">All Statuses</option>
						@foreach(\App\Enums\InvoiceStatus::cases() as $status)
							<option value="{{ $status->value }}" {{ request('status') == $status ? 'selected' : '' }}>
								{{ $status->label() }}
							</option>
						@endforeach
					</select>

					@error('status')
					<span class="error-text">{{ $message }}</span>
					@enderror
				</div>

				{{-- From Date --}}
				<div class="filter-group">
					<label class="filter-label" for="date_from">From Date</label>
					<input type="date" name="date_from" id="date_from" class="form-control @error('date_from') is-invalid @enderror" value="{{ request('date_from') }}">

					@error('date_from')
					<span class="error-text">{{ $message }}</span>
					@enderror
				</div>

				{{-- To Date --}}
				<div class="filter-group">
					<label class="filter-label" for="date_to">To Date</label>
					<input type="date" name="date_to" id="date_to" class="form-control @error('date_to') is-invalid @enderror" value="{{ request('date_to') }}">

					@error('date_to')
					<span class="error-text">{{ $message }}</span>
					@enderror
				</div>

				{{-- Actions --}}
				<div class="filter-actions">
					<button type="submit" class="btn-primary-filter">
						<i class="fas fa-filter"></i> Filter
					</button>

					{{-- Το κουμπί Reset --}}
					<a href="#" class="btn-sync-reset" title="Reset Filters">
						<i class="fas fa-sync-alt"></i>
					</a>
				</div>
			</form>
		</div>

		<div class="card">
			<div class="table-responsive">
				<table class="table-custom">
					<thead>
					<tr>
						<th>Invoice #</th>
						<th>Account</th>
						<th>Issued Date</th>
						<th>Due Date</th>
						<th>Total Amount</th>
						<th>Status</th>
						<th class="text-end">Actions</th>
					</tr>
					</thead>
					<tbody>
					@foreach($invoices as $invoice)
						<tr class="invoice-row"
						    data-status="{{ $invoice->effective_status->value }}"
						    data-date="{{ $invoice->invoice_date->format('Y-m-d') }}">

							<td class="bold">{{ $invoice->invoice_number }}</td>

							<td>
								<div class="flex-column">
									<span class="bold">{{ $invoice->account->name }}</span>
									<span class="text-muted" style="font-size: 0.75rem;">ID: {{ $invoice->account_id }}</span>
								</div>
							</td>

							<td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>

							<td>{{ $invoice->due_date->format('d/m/Y') }}</td>

							<td>
								<div class="flex-column">
									<span class="bold">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span>
									<small class="text-muted">Paid: {{ number_format($invoice->paid_amount, 2) }}</small>
								</div>
							</td>

							<td>
								<span class="status-badge" style="background-color: {{ $invoice->effective_status->bgColor() }}; color: {{ $invoice->effective_status->color() }}; border: 1px solid {{ $invoice->effective_status->color() }}40;">
									{{ $invoice->effective_status->label() }}
								</span>
							</td>

							<td class="text-end">
								<div class="header-actions" style="justify-content: flex-end;">
									<a href="{{ route('invoices.show', $invoice->id) }}" class="btn-view-details" title="View Invoice">
										<i class="fas fa-eye"></i>
									</a>

									<a href="{{ route('invoices.edit', $invoice->id) }}" class="btn-view-details" title="Edit Invoice">
										<i class="fas fa-edit"></i>
									</a>

									<form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure?')">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn-view-details btn-delete" title="Delete">
											<i class="fas fa-trash"></i>
										</button>
									</form>
								</div>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>

			<div class="card-footer">
				{{ $invoices->links('partials.pagination') }}
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script src="{{ asset('js/invoices/search.js') }}"></script>
@endpush