@extends('layouts.app')

@push('scripts')
	<link rel="stylesheet" href="{{ asset('css/invoices/edit.css') }}">
@endpush

@section('content')
	<div class="container">
		<form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="invoice-form" data-id="{{ $invoice->id }}">
			@csrf
			@method('PUT')

			<div class="edit-grid">

				{{-- Κύριο Μέρος: Στοιχεία & Items --}}
				<div class="edit-main">
					<div class="edit-card mb-4">
						<div class="card-header-simple">
							<h5><i class="fas fa-file-invoice"></i> Στοιχεία Τιμολογίου #{{ $invoice->invoice_number }}</h5>
						</div>

						<div class="form-row mb-3">
							<div class="form-group">
								<label for="account_id">Πελάτης</label>
								<select name="account_id" id="account_id" class="form-select @error('account_id') is-invalid @enderror">
									@foreach($accounts as $account)
										<option value="{{ $account->id }}" {{ old('account_id', $invoice->account_id) == $account->id ? 'selected' : '' }}>
											{{ $account->name }}
										</option>
									@endforeach
								</select>
								<input type="hidden" name="opportunity_id" value="{{ old('opportunity_id', $invoice->opportunity_id) }}">
							</div>
							<div class="form-group">
								<label for="invoice_date">Ημερομηνία Έκδοσης</label>
								<input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}">
							</div>
						</div>

						<div class="form-row">
							<div class="form-group">
								<label for="status">Κατάσταση</label>
								<select name="status" id="status" class="form-select">
									@foreach($statuses as $status)
										<option value="{{ $status->value }}"{{ old('status', $invoice->status->value ?? '') == $status->value ? 'selected' : '' }}>
											{{ $status->label() }}
										</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label for="due_date">Ημερομηνία Λήξης</label>
								<input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}">
							</div>
						</div>
					</div>

					{{-- Πίνακας Γραμμών --}}
					<div class="edit-card">
						<div class="card-header-simple flex-between">
							<h5><i class="fas fa-list"></i> Γραμμές Χρέωσης</h5>
							<button type="button" class="btn-add-item" id="add-item">
								<i class="fas fa-plus"></i> Προσθήκη Γραμμής
							</button>
						</div>

						<table class="table-edit-items" id="items-table">
							<thead>
							<tr>
								<th class="col-product">Προϊόν</th>
								<th class="col-description">Περιγραφή</th>
								<th class="col-qty text-center">Ποσ.</th>
								<th class="col-unit-price text-end">Τιμή Μον.</th>
								<th class="col-amount text-end">Σύνολο</th>
								<th class="col-actions"></th>
							</tr>
							</thead>
							<tbody>
							@foreach(collect(old('items', $invoice->items ?? [])) as $index => $item)
								<tr>
									<td class="col-product">
										<select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
											<option value="">-- Επιλέξτε Προϊόν --</option>
											@foreach($products as $product)
												<option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ (is_array($item) ? $item['product_id'] : $item->product_id) == $product->id ? 'selected' : '' }}>
													{{ $product->name }} ({{ number_format($product->price, 2) }} €)
												</option>
											@endforeach
										</select>
									</td>
									<td class="col-description">
										<input type="text" name="items[{{ $index }}][description]"
										       class="form-control"
										       value="{{ is_array($item) ? $item['description'] : $item->description }}" required>
									</td>
									<td class="col-qty">
										<input type="number" name="items[{{ $index }}][quantity]"
										       class="form-control qty-input text-center"
										       value="{{ is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1) }}" required>
									</td>
									<td class="col-unit-price">
										<input type="number" name="items[{{ $index }}][unit_price]"
										       class="form-control unit-price-input text-end"
										       value="{{ is_array($item) ? ($item['unit_price'] ?? 0) : ($item->unit_price ?? 0) }}" step="0.01" required>
									</td>
									<td class="col-amount">
										<input type="number" name="items[{{ $index }}][amount]"
										       class="form-control amount-input text-end"
										       value="{{ is_array($item) ? ($item['amount'] ?? 0) : $item->amount }}" step="0.01" readonly>
									</td>
									<td class="col-actions">
										<button type="button" class="btn-remove-row remove-item">
											<i class="fas fa-times"></i>
										</button>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>

				{{-- Sidebar: Σύνολα & Ενέργειες --}}
				<div class="edit-sidebar">
					<div class="sticky-sidebar">
						<div class="edit-card">
							<div class="card-header-simple">
								<h5><i class="fas fa-calculator"></i> Σύνοψη</h5>
							</div>

							<div class="totals-preview">
								<div class="total-row">
									<span>Καθαρή Αξία:</span>
									<span id="preview-net">0.00 €</span>
								</div>
								<div class="total-row">
									<span>ΦΠΑ (24%):</span>
									<span id="preview-tax">0.00 €</span>
								</div>
								<div class="total-row grand-total">
									<span>Σύνολο:</span>
									<span id="preview-total">0.00 €</span>
								</div>

								<div class="total-row text-success">
									<span>Ήδη Πληρωμένα:</span>
									<span id="already-paid-display" data-paid="{{ $already_paid }}">{{ number_format($already_paid, 2, ',', '.') }} €</span>
								</div>

								<div class="total-row grand-total">
									<span>Υπόλοιπο:</span>
									<span id="preview-balance">0.00 €</span>
								</div>
							</div>

							<hr class="form-divider">

							<button type="submit" class="btn-primary-lg">
								<i class="fas fa-save"></i> Αποθήκευση
							</button>

							<p class="small-text text-center mt-4 text-muted">
								* Τα σύνολα υπολογίζονται αυτόματα κατά την αποθήκευση.
							</p>
						</div>

						<div class="edit-card mt-4">
							<div class="card-header-simple">
								<h5><i class="fas fa-hand-holding-usd"></i> Γρήγορη Πληρωμή</h5>
							</div>
							<div class="payment-quick-form p-3">
								{{-- Ποσό --}}
								<div class="form-group mb-2">
									<label class="small fw-bold">Ποσό Πληρωμής (€)</label>
									<input type="number" id="quick-payment-amount" class="form-control form-control-sm" step="0.01" placeholder="0.00">
								</div>

								{{-- Μέθοδος Πληρωμής --}}
								<div class="form-group mb-3">
									<label class="small fw-bold">Τρόπος Πληρωμής</label>
									<select id="quick-payment-method" class="form-select form-select-sm">
										<option value="bank_transfer">Τραπεζική Κατάθεση</option>
										<option value="card">Πιστωτική/Χρεωστική Κάρτα</option>
										<option value="check">Επιταγή</option>
										<option value="cash">Μετρητά (Μικροποσά)</option>
										<option value="stripe">Stripe / Online</option>
									</select>
								</div>

								<button type="button" id="btn-record-payment" class="btn-secondary-sm w-100 mt-2">
									Καταχώρηση
								</button>

								<div class="history-container">
									<a href="{{ route('invoices.payments.show', ['invoice' => $invoice->id]) }}" class="view-history-link">
										<i class="fas fa-clock-rotate-left"></i> Ιστορικό Πληρωμών
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</form>
	</div>
@endsection

@push('scripts')
	<script type="application/javascript" src="{{ asset('js/invoices/edit.js') }}"></script>
	<script type="application/javascript" src="{{ asset('js/invoices/payment.js') }}"></script>
@endpush