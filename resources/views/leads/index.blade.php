@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/leads/index.css') }}">
@endpush

@section('content')
	<div class="main-content"> {{-- Changed to match Section 3.4 of your global CSS --}}
		<header class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Leads Pipeline</h1>
				<p class="module-subtitle">
					<i class="nav-icon">🎯</i> Manage potential opportunities and conversions
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('leads.create') }}" class="btn-action edit">
					<span class="nav-icon">+</span> New Lead
				</a>
			</div>
		</header>

		{{-- Search & Filter Section (Cleaned & Single) --}}
		<div class="card mb-4 shadow-sm">
			<form action="{{ route('leads.index') }}" method="GET" class="filter-form">
				<div style="display:flex; gap:12px; align-items:center; width:100%;">
					{{-- Search Input --}}
					<input type="text" name="search" placeholder="Search name or company..."
					       value="{{ request('search') }}"
					       class="form-input" style="flex:1;">

					{{-- Status Filter --}}
					<select name="status" class="form-select" onchange="this.form.submit()">
						{{-- Use the static method from our Enum --}}
						<option value="">{{ \App\Enums\Leads\LeadStatus::allLabel() }}</option>

						@foreach(\App\Enums\Leads\LeadStatus::cases() as $status)
							<option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
								{{ $status->label() }}
							</option>
						@endforeach
					</select>

					{{-- Priority Filter --}}
					<select name="priority" class="form-select" onchange="this.form.submit()">
						<option value="">{{ \App\Enums\Leads\LeadPriority::allLabel() }}</option>
						@foreach(\App\Enums\Leads\LeadPriority::cases() as $priority)
							<option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
								{{ $priority->label() }}
							</option>
						@endforeach
					</select>

					<button type="submit" class="btn-action">Filter</button>

					{{-- Updated Clear Check --}}
					@if(request()->anyFilled(['search', 'status', 'priority']))
						<a href="{{ route('leads.index') }}" class="btn-action delete">Clear</a>
					@endif
				</div>
			</form>
		</div>

		{{-- The Leads Table --}}
		<div class="card leads-table-container">
			<table class="table-custom">
				<thead>
				<tr>
					<th>{{ __('leads.name') ?? 'Lead Name' }}</th>
					<th>{{ __('leads.company') ?? 'Company' }}</th>
					<th>{{ __('leads.status.label') ?? 'Status' }}</th>
					<th>{{ __('leads.priority.label') ?? 'Priority' }}</th>
					<th>{{ __('leads.estimated_value') ?? 'Est. Value' }}</th>
					<th>{{ __('leads.owner') ?? 'Owner' }}</th>
					<th class="text-right">{{ __('leads.actions') ?? 'Actions' }}</th>
				</tr>
				</thead>
				<tbody>
				@foreach($leads as $lead)
					<tr>
						<td>
							<a href="{{ route('leads.show', $lead) }}" class="lead-name-link" style="font-weight: 600; color: #1e293b;">
								{{ $lead->full_name }}
							</a>
						</td>
						<td><span class="text-muted">{{ $lead->company_name ?? '—' }}</span></td>

						{{-- Dynamic Status Badge --}}
						<td>
				             <span class="status-pill" style="background-color: {{ $lead->status->bgColor() }}; color: {{ $lead->status->color() }}; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600;border: 1px solid {{ $lead->status->color() }}33;"> {{-- 33 adds slight transparency to border --}}
					             {{ $lead->status->label() }}
				             </span>
						</td>

						{{-- Dynamic Priority Indicator --}}
						<td>
			             <span class="priority-indicator" style="color: {{ $lead->priority->color() }}; font-weight: 600; font-size: 0.85rem;display: flex;align-items: center;gap: 6px;">
			                 <span style="font-size: 1.1rem;">●</span> {{ $lead->priority->label() }}
			             </span>
						</td>

						<td class="font-bold" style="color: #0f172a;">
							€{{ number_format($lead->estimated_value, 2) }}
						</td>

						<td>
							<div style="display:flex; align-items:center; gap:8px;">
								<div class="user-avatar-wrapper" style="width:28px; height:28px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
				                    <span class="user-avatar-placeholder" style="font-size:0.65rem; color: #475569; font-weight: bold;">
				                        {{ strtoupper(substr($lead->owner->name, 0, 1)) }}
				                    </span>
								</div>
								<span class="text-sm" style="font-weight: 500;">{{ $lead->owner->profile->full_name }}</span>
							</div>
						</td>
						<td class="text-right">
							<a href="{{ route('leads.edit', $lead) }}" class="btn-icon" title="Edit">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="color: #64748b;">
									<path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
								</svg>
							</a>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>

			{{-- Pagination --}}
			@if($leads->hasPages())
				<div style="margin-top: 1.5rem;">
					{{ $leads->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection