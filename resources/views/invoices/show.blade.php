@extends('layouts.app')

@push('scripts')
	<link rel="stylesheet" href="{{ asset('css/invoices/show.css') }}">
@endpush

@section('content')
	<div class="container-fluid">

		<header class="invoice-page-header">
			<div class="header-content">
				<h2 class="invoice-title">Τιμολόγιο #{{ $invoice->invoice_number }}</h2>
				<nav class="breadcrumb-nav">
					<ol class="breadcrumb-list">
						<li class="breadcrumb-item">
							<a href="{{ route('invoices.index') }}">Τιμολόγια</a>
						</li>
						<li class="breadcrumb-item active">
							{{ $invoice->invoice_number }}
						</li>
					</ol>
				</nav>
			</div>

			<div class="header-actions">
				<button class="btn-secondary" onclick="window.print()">
					<i class="fas fa-print"></i> Εκτύπωση
				</button>
				<a href="{{ route('invoices.edit', $invoice->id) }}" class="btn-primary">
					<i class="fas fa-edit"></i> Επεξεργασία
				</a>
			</div>
		</header>

		<main class="invoice-container">
			<section class="invoice-main-content">
				<div class="invoice-card">
					<div class="invoice-body">

						<div class="invoice-top-bar">
							<div class="company-info">
								<h4 class="brand-name">PLIASSAS CRM</h4>
								<address class="address-block">
									Λεωφόρος Παπάγου 123<br>
									Ζωγράφου, 15772<br>
									info@pliassas.gr
								</address>
							</div>
							<div class="invoice-status-block">
							    <span class="status-badge" style="background-color: {{ $invoice->effective_status->bgColor() }}; color: {{ $invoice->effective_status->color() }}; border-color: {{ $invoice->effective_status->color() }}40;">
								    {{ $invoice->effective_status->label() }}
							    </span>
								<h5 class="total-label">
									{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
								</h5>
							</div>
						</div>

						<hr class="invoice-divider">

						<div class="billing-details">
							<div class="client-info">
								<h6 class="section-subtitle">Χρέωση προς:</h6>
								<h5 class="client-name">{{ $invoice->account->name }}</h5>
								<p class="meta-text">
									Account ID: #{{ $invoice->account->id }}<br>
									@if($invoice->opportunity)
										Σχετική Ευκαιρία: {{ $invoice->opportunity->title }}
									@endif
								</p>
							</div>
							<div class="date-info">
								<div class="date-row">
									<span class="text-muted">Ημερομηνία Έκδοσης:</span>
									<span class="bold">{{ $invoice->invoice_date->format('d/m/Y') }}</span>
								</div>
								<div class="date-row">
									<span class="text-muted">Ημερομηνία Λήξης:</span>
									<span class="bold text-danger">{{ $invoice->due_date->format('d/m/Y') }}</span>
								</div>
							</div>
						</div>

						<div class="table-container">
							<table class="table-financial">
								<thead>
								<tr>
									<th>Περιγραφή</th>
									<th class="text-end">Ποσό</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>Καθαρή Αξία (Net Amount)</td>
									<td class="text-end">{{ number_format($invoice->net_amount, 2) }} €</td>
								</tr>
								<tr>
									<td>ΦΠΑ (24%)</td>
									<td class="text-end">{{ number_format($invoice->tax_amount, 2) }} €</td>
								</tr>
								<tr class="grand-total-row">
									<td class="bold">Γενικό Σύνολο</td>
									<td class="text-end bold">{{ number_format($invoice->total_amount, 2) }} €</td>
								</tr>
								<tr class="paid-row">
									<td>Έχει Πληρωθεί</td>
									<td class="text-end">- {{ number_format($invoice->paid_amount, 2) }} €</td>
								</tr>
								<tr class="balance-due-row">
									<td class="bold text-primary">Υπόλοιπο προς Εξόφληση</td>
									<td class="text-end bold text-primary">
										{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }} €
									</td>
								</tr>
								</tbody>
							</table>
						</div>

						@if($invoice->notes)
							<div class="notes-section">
								<h6 class="section-subtitle">Σημειώσεις:</h6>
								<p class="notes-content">{{ $invoice->notes }}</p>
							</div>
						@endif
					</div>
				</div>
			</section>

			<aside class="invoice-widgets">
				<div class="invoice-widget">
					<div class="widget-header">Εσωτερικές Σημειώσεις</div>
					<div class="widget-body">
						<p class="meta-text">{{ $invoice->internal_notes ?? 'Δεν υπάρχουν εσωτερικές σημειώσεις.' }}</p>
					</div>
				</div>

				<div class="invoice-widget">
					<div class="widget-header">Ιστορικό</div>
					<div class="widget-body p-0">
						<ul class="history-list">
							<li>
								<span>Δημιουργήθηκε:</span>
								<span class="bold">{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
							</li>
							<li>
								<span>Τελευταία αλλαγή:</span>
								<span class="bold">{{ $invoice->updated_at->format('d/m/Y H:i') }}</span>
							</li>
						</ul>
					</div>
				</div>
			</aside>
		</main>
	</div>
@endsection