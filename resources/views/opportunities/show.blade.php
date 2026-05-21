@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/opportunity/show.css') }}">
@endpush

@section('content')
	<div class="container">
		{{-- Header Section --}}
		<div class="module-header">
			<div class="header-title-group">
				<div class="flex-align-center" style="gap: 10px;">
					<h1 class="module-title">{{ $opportunity->name }}</h1>
					<span class="status-badge" style="background-color: {{ $opportunity->status->value === 'won' ? '#10b98126' : '#3b82f626' }}; color: {{ $opportunity->status->value === 'won' ? '#059669' : '#2563eb' }};">
                {{ $opportunity->status->label() }}
            </span>
				</div>
				<p class="module-subtitle">
					<strong>{{ $opportunity->account->name }}</strong>
					@if($opportunity->contact)
						&bull; {{ $opportunity->contact->name }}
					@endif
				</p>
			</div>

			<div class="header-actions">
				<a href="{{ route('opportunities.index') }}" class="btn-icon" title="Back" style="margin-right: 8px;">←</a>
				<a href="{{ route('opportunities.edit', $opportunity->id) }}" class="btn-action edit">
					✎ {{ __('opportunities.edit') }}
				</a>
			</div>
		</div>

		<div class="grid-layout">
			{{-- Αριστερή Στήλη: Κύρια Πληροφορία --}}
			<div class="main-content">

				{{-- Info Card --}}
				<div class="card info-card" style="margin-bottom: 1.5rem;">
					<div class="card-header">
						<h3>{{ __('opportunities.title') }}</h3>
					</div>
					<div class="details-grid-horizontal">
						<div class="detail-item">
							<label>{{ __('opportunities.stage.label') }}</label>
							<span class="bold" style="color: {{ $opportunity->stage_type->color() }};">
								{{ $opportunity->stage_type->label() }}
							</span>
							<span class="pipeline-info-tag">
								<i class="fas fa-layer-group"></i> {{ $opportunity->pipeline->name }}
							</span>
						</div>

						<div class="detail-item">
							<label>{{ __('opportunities.probability') }}</label>
							<div class="flex-align-center" style="gap: 10px;">
								<span class="probability-pill" style="background: hsl({{ $opportunity->probability * 1.2 }}, 70%, 40%);">{{ $opportunity->probability }}%</span>
								<div class="probability-bar-container" style="width: 80px;">
									<div class="probability-bar-fill" style="width: {{ $opportunity->probability }}%; background: hsl({{ $opportunity->probability * 1.2 }}, 70%, 50%);"></div>
								</div>
							</div>
						</div>

						<div class="detail-item">
							<label>{{ __('opportunities.amount') }}</label>
							<span class="bold">{{ $opportunity->currency }} {{ number_format($opportunity->amount, 2, ',', '.') }}</span>
						</div>

						<div class="detail-item">
							<label>Ημερομηνία Κλεισίματος</label>
							<span class="bold {{ $opportunity->close_date < now() && $opportunity->status->value === 'open' ? 'text-danger' : '' }}">
								{{ $opportunity->close_date->format('d/m/Y') }}
							</span>
							@if($opportunity->close_date < now() && $opportunity->status->value === 'open')
								<small class="text-danger" style="font-weight: 700;">(Καθυστερημένη)</small>
							@endif
						</div>
					</div>
				</div>

				{{-- Items Card --}}
				<div class="card items-card">
					<div class="card-header">
						<h3>{{ __('opportunities.items.product') }}</h3>
					</div>
					<div class="table-container">
						<table class="data-table">
							<thead>
							<tr>
								<th>{{ __('opportunities.items.product') }}</th>
								<th class="text-right">{{ __('opportunities.items.quantity') }}</th>
								<th class="text-right">{{ __('opportunities.items.total') }}</th>
							</tr>
							</thead>
							<tbody>
							@foreach($opportunity->items as $item)
								<tr>
									<td><span class="item-name">{{ $item->product->name }}</span></td>
									<td class="text-right">{{ number_format($item->quantity) }}</td>
									<td class="text-right bold">{{ $opportunity->currency }} {{ number_format($item->total, 2, ',', '.') }}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

			{{-- Δεξιά Στήλη: Sidebar --}}
			<div class="module-sidebar">

				{{-- Κάρτα Συνδέσμων & Tags --}}
				<div class="card side-card">
					<div class="card-header"><h3>Στοιχεία Επαφής</h3></div>
					<div class="side-details">
						<div class="user-item">
							<label>Λογαριασμός</label>
							<a href="{{ route('accounts.show', $opportunity->account_id) }}" class="link-styled">
								<strong><i class="fas fa-building"></i> {{ $opportunity->account->name }}</strong>
							</a>
						</div>

						@if($opportunity->contact)
							<div class="user-item mt-3" style="margin-top: 1rem;">
								<label>Κύρια Επαφή</label>
								<a href="{{ route('contacts.show', $opportunity->contact_id) }}" class="link-styled">
									<strong><i class="fas fa-user-tie"></i> {{ $opportunity->contact->name }}</strong>
								</a>
							</div>
						@endif

						@if($opportunity->tags->count() > 0)
							<div class="user-item mt-3" style="margin-top: 1rem;">
								<label>Ετικέτες</label>
								<div class="tag-container mt-1">
									@foreach($opportunity->tags as $tag)
										<span class="tag-badge" style="background-color: {{ $tag->color ?? 'var(--gray-500)' }}">
                                  {{ $tag->name }}
                               </span>
									@endforeach
								</div>
							</div>
						@endif
					</div>
				</div>

				{{-- Κάρτα Συμμετεχόντων --}}
				<div class="card side-card" style="margin-top: 1.5rem;">
					<div class="card-header"><h3>Συμμετέχοντες</h3></div>
					<div class="side-details">
						<div class="user-item">
							<label>Υπεύθυνος (Owner)</label>
							<strong>{{ $opportunity->owner->profile->full_name }}</strong>
						</div>
						@if($opportunity->users->count() > 1)
							<div class="user-item mt-3" style="margin-top: 1rem;">
								<label>Ομάδα Πώλησης</label>
								<div class="tag-container mt-1">
									@foreach($opportunity->users as $collaborator)
										@if($collaborator->id !== $opportunity->owner_id)
											<span class="collab-badge">{{ $collaborator->profile->full_name }}</span>
										@endif
									@endforeach
								</div>
							</div>
						@endif
					</div>
				</div>

				{{-- Κάρτα Ιστορικού --}}
				<div class="card side-card" style="margin-top: 1.5rem;">
					<div class="card-header"><h3>Ιστορικό</h3></div>
					<div class="timeline-compact" style="max-height: 400px; overflow-y: auto;">
						@foreach($opportunity->activities as $activity)
							<div class="timeline-event">
								<div style="font-size: 0.75rem; color: var(--text-muted);">
									<strong>{{ $activity->owner?->profile?->full_name ?? 'System' }}</strong>&bull; {{ $activity->created_at->diffForHumans() }}
								</div>
								<p style="font-size: 0.85rem; margin: 4px 0;">{{ $activity->description }}</p>
							</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection