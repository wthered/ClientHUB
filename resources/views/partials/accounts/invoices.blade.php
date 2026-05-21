<div class="section-header">
	<h3>Invoices ({{ $invoices->count() }})</h3>
	<div class="header-actions">
		{{-- Υπολογισμός Unpaid: total_amount - paid_amount --}}
		@php
			$unpaidSum = $invoices->where('status', '!=', 'paid')->sum('total_amount');
		@endphp
		<span class="total-amount-label">
            Total Unpaid: <strong>{{ number_format($unpaidSum, 2, ',', '.') }} €</strong>
        </span>
	</div>
</div>

<table class="data-table">
	<thead>
	<tr>
		<th>Invoice #</th>
		<th>Date</th>
		<th>Due Date</th>
		<th>Amount</th>
		<th>Status</th>
		<th class="text-right">Actions</th>
	</tr>
	</thead>
	<tbody>
	@forelse($invoices as $invoice)
		<tr>
			<td><strong>{{ $invoice->invoice_number }}</strong></td>
			{{-- Προσοχή: Βεβαιώσου ότι έχεις ορίσει τα dates στο Model Invoice (casting) --}}
			<td>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y') : '-' }}</td>
			<td>{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</td>
			<td>{{ number_format($invoice->total_amount, 2, ',', '.') }} {{ $invoice->currency }}</td>
			<td>
                    <span class="status-pill status-{{ strtolower($invoice->status) }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
			</td>
			<td class="text-right">
				<a href="#" class="btn-icon">
					<i class="fas fa-eye"></i>
				</a>
			</td>
		</tr>
	@empty
		<tr>
			<td colspan="6" class="text-center" style="padding: 3rem; color: var(--text-muted);">
				No invoices found.
			</td>
		</tr>
	@endforelse
	</tbody>
</table>