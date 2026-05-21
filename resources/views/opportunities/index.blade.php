@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/opportunity/index.css') }}">
@endpush

@section('content')
	<div class="main-content">
		<header class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">{{ __('opportunities.title') }}</h1>
				<p class="module-subtitle">Tracking {{ $opportunities->total() }} active deals in the pipeline.</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('opportunities.create') }}" class="btn-action success">+ {{ __('opportunities.create_title') }}</a>
			</div>
		</header>

		{{-- Filter Bar --}}
		<div class="filter-section card">
			<form action="{{ route('opportunities.index') }}" method="GET" class="filter-form">

				<div class="filter-group">
					<input type="text" name="search" class="form-input" placeholder="{{ __('opportunities.search_placeholder') }}" value="{{ request('search') }}">
				</div>

				<div class="filter-group">
					<select name="stage_id" class="form-select" onchange="this.form.submit()">
						<option value="">{{ __('opportunities.all_stages') }}</option>

						@foreach($stages->groupBy('pipeline_id') as $pipelineId => $pipelineStages)
							<optgroup label="{{ $pipelineStages->first()->pipeline->name }}">
								@foreach($pipelineStages as $stage)
									<option value="{{ $stage->id }}" {{ request('stage_id') == $stage->id ? 'selected' : '' }}>
										{{ \App\Enums\Opportunities\OpportunityStage::tryFrom($stage->name)->label() }}
									</option>
								@endforeach
							</optgroup>
						@endforeach
					</select>
				</div>

				<div class="filter-group">
					<select name="status" class="form-select" onchange="this.form.submit()">
						<option value="">{{ __('opportunities.status.all') }}</option>
						@foreach(['open', 'won', 'lost'] as $status)
							<option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
								{{ __('opportunities.status.' . $status) }}
							</option>
						@endforeach
					</select>
				</div>
			</form>
		</div>

		<div class="table-container card">
			<table class="data-table">
				<thead>
				<tr>
					<th>{{ __('opportunities.deal_name') }}</th>
					<th>{{ __('opportunities.account') }}</th>
					<th>{{ __('opportunities.amount') }}</th>
					<th>{{ __('opportunities.stage.label') }}</th>
					<th>{{ __('opportunities.probability') }}</th>
					<th>{{ __('opportunities.closing_date') }}</th>
					<th class="text-right">{{ __('opportunities.actions') }}</th>
				</tr>
				</thead>
				<tbody>
				@forelse($opportunities as $opportunity)
					<tr>
						<td>
							<div class="item-main">
								<a href="{{ route('opportunities.show', $opportunity->id) }}" class="item-name">{{ $opportunity->name }}</a>
								<span class="item-sub">{{ $opportunity->contact->full_name ?? 'No Contact Assigned' }}</span>

								{{-- Προσθήκη Tags --}}
								@if($opportunity->tags->count() > 0)
									<div class="tag-container" style="margin-top: 6px; display: flex; flex-wrap: wrap; gap: 4px;">
										@foreach($opportunity->tags as $tag)
											<span class="tag-badge" style="background-color: {{ $tag->color }}22; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}44;padding: 1px 6px;border-radius: 4px;font-size: 0.65rem;font-weight: 700;text-transform: uppercase;letter-spacing: 0.02em;white-space: nowrap;">
												{{ $tag->name }}
											</span>
										@endforeach
									</div>
								@endif
							</div>
						</td>
						<td>
							<span class="company-tag">{{ $opportunity->account->name ?? 'Individual' }}</span>
						</td>
						<td class="bold">
							<span class="text-{{ $opportunity->status }}">
								{{ $opportunity->currency }} {{ number_format($opportunity->amount, 2) }}
							</span>
						</td>
						<td>
							@php
								// Μετατρέπουμε το stage name σε Enum instance
								$stageEnum = \App\Enums\Opportunities\OpportunityStage::tryFrom($opportunity->stage->name);
							@endphp
							<span class="status-badge" style="background-color: {{ $stageEnum?->background() ?? '#e2e8f0' }}; color: {{ $stageEnum?->color() ?? '#64748b' }}; border: 1px solid {{ $stageEnum?->border() ?? '#cbd5e1' }};">
								{{ $stageEnum?->label() ?? $opportunity->stage->name }}
							</span>
						</td>
						<td>
							<span class="probability-pill">{{ $opportunity->probability }}%</span>
						</td>
						<td>
							<span class="{{ $opportunity->close_date && $opportunity->close_date->isPast() && $opportunity->status === 'open' ? 'text-danger bold' : '' }}">
								{{ $opportunity->close_date ? $opportunity->close_date->format('d/m/Y') : 'N/A' }}
							</span>
						</td>
						<td class="text-right">
							<div class="action-buttons">
								<a href="{{ route('opportunities.edit', $opportunity->id) }}" class="btn-icon edit" title="Edit">✎</a>
								@if($opportunity->status === 'open')
									<form action="{{ route('opportunities.mark-won', $opportunity->id) }}" method="POST" style="display:inline;">
										@csrf
										<button type="submit" class="btn-icon success" title="Mark Won">✓</button>
									</form>
								@endif
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="7" class="text-center">{{ __('opportunities.no_records') ?? 'No opportunities found.' }}</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>

		<div class="pagination-wrapper">
			{{ $opportunities->links('vendor.pagination.custom') }}
		</div>
	</div>
@endsection