<div class="table-responsive">
	<table class="table-custom">
		<thead>
		<tr>
			<th>Date</th>
			<th>Invoice #</th>
			<th>Account</th>
			<th>Method</th>
			<th>Reference</th>
			<th class="text-end">Amount</th>
			<th class="text-end">Actions</th>
		</tr>
		</thead>
		<tbody>
		@forelse($payments as $payment)
			<tr>
				{{-- 1. Formatted Date --}}
				<td class="bold">
					{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
				</td>

				<td>
					<a href="{{ $payment->invoice_id ? route('invoices.show', $payment->invoice_id) : '#' }}" class="bold primary-link">
						{{ $payment->invoice->invoice_number ?? 'No Invoice' }}
					</a>
				</td>

				<td>
					<span class="bold">{{ $payment->invoice->account->name ?? 'Unknown Account' }}</span>
				</td>

				<td>
					{{-- 2. Dynamic Method Badges --}}
					<span class="payment-method-badge method-{{ Str::slug($payment->method) }}">
                    <i class="fas {{ $payment->method == 'bank transfer' ? 'fa-university' : ($payment->method == 'cash' ? 'fa-money-bill-wave' : 'fa-credit-card') }}"></i>
                    {{ ucfirst($payment->method) }}
                </span>
				</td>

				<td class="text-muted small">{{ $payment->reference_id ?? '-' }}</td>

				<td class="text-end bold">
					{{ number_format($payment->amount, 2) }} <span class="small text-muted">{{ $payment->invoice->currency }}</span>
				</td>

				<td class="text-end">
					{{-- 3. Action Buttons --}}
					<div class="table-actions">
						<a href="{{ route('invoices.show', $payment->invoice_id) }}" class="btn-view-details" title="View Invoice">
							<i class="fas fa-eye"></i>
						</a>

						<form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete this payment record?')">
							@csrf
							@method('DELETE')
							<button type="submit" class="btn-view-details btn-delete" title="Delete">
								<i class="fas fa-trash"></i>
							</button>
						</form>
					</div>
				</td>
			</tr>
		@empty
			<tr><td colspan="7" class="text-center py-5 text-muted">No payments found matching your criteria.</td></tr>
		@endforelse
		</tbody>
	</table>
</div>

<div class="card-footer" id="pagination-links">
	{{ $payments->appends(request()->query())->links('partials.pagination') }}
</div>