@extends('layouts.app')

@push('scripts')
	<link rel="stylesheet" href="{{ asset('css/payments/show.css') }}">
@endpush

@section('content')
	<div class="main-content">
		{{-- 1. Module Header --}}
		<div class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Ιστορικό Πληρωμών</h1>
				<p class="module-subtitle">
					<i class="fas fa-file-invoice"></i> Τιμολόγιο: <strong>#{{ $invoice->invoice_number }}</strong>
					<span class="mx-2">|</span>
					<i class="fas fa-user"></i> {{ $invoice->account->name }}
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('invoices.edit', $invoice->id) }}" class="btn-action">
					<i class="fas fa-arrow-left"></i> Επιστροφή στο Τιμολόγιο
				</a>
			</div>
		</div>

		{{-- 2. Summary Cards --}}
		<div class="payment-summary-grid">
			<div class="summary-card total">
				<span class="label">Συνολική Αξία</span>
				<span class="value">{{ number_format($invoice->total_amount, 2, ',', '.') }} €</span>
			</div>
			<div class="summary-card paid">
				<span class="label">Πληρωμένα</span>
				<span class="value text-success">{{ number_format($alreadyPaid, 2, ',', '.') }} €</span>
			</div>
			<div class="summary-card balance {{ $isSettled ? 'settled' : '' }}">
				<span class="label">Υπόλοιπο</span>
				<span class="value {{ $isSettled ? 'text-success' : 'text-danger' }}">
					{{ number_format($balance, 2, ',', '.') }} €
				</span>
			</div>
		</div>

		{{-- 3. Payments Timeline/Table --}}
		<div class="card p-0 overflow-hidden">
			<table class="table-custom">
				<thead>
				<tr>
					<th>Ημερομηνία</th>
					<th>Τρόπος Πληρωμής</th>
					<th>Reference / Σημειώσεις</th>
					<th class="text-end">Ποσό</th>
					<th class="text-center">Ενέργειες</th>
				</tr>
				</thead>
				<tbody>
				@foreach($payments as $payment)
					<tr>
						<td style="font-weight: 600">{{ $payment->payment_date }}</td>
						<td>
	                        <span class="payment-method-badge">
	                            <i class="fas fa-wallet"></i> {{ ucfirst(str_replace('_', ' ', $payment->method)) }}
	                        </span>
						</td>
						<td class="text-muted small">{{ $payment->reference_id ?? '-' }}</td>
						<td class="text-end" style="font-weight: 800">{{ number_format($payment->amount, 2, ',', '.') }} €</td>
						<td class="text-center">
							{{-- Εδώ θα μπορούσε να μπει ένα delete αν επιτρέπεται --}}
							<button class="btn-icon text-danger" title="Διαγραφή">
								<i class="fas fa-trash-alt"></i>
							</button>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
@endsection