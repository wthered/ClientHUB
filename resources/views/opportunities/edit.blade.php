@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/opportunity/edit.css') }}">
@endpush

@section('content')
	<div class="container">
		<div class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">
					{{ __('opportunities.edit') }}: {{ $opportunity->name }}
					<span class="status-badge" style="--status-color: {{ $opportunity->status->color() }}; --status-bg: {{ $opportunity->status->background() }};">
						{{ $opportunity->status->label() }}
					</span>
				</h1>
			</div>
			<div class="header-actions">
				<a href="{{ route('opportunities.show', $opportunity->id) }}" class="btn-action cancel">
					{{ __('globals.cancel') }}
				</a>
			</div>
		</div>

		<form action="{{ route('opportunities.update', $opportunity->id) }}" method="POST" class="grid-layout">
			@csrf
			@method('PUT')

			<div class="card info-card">
				<div class="card-header">
					<h3>{{ __('opportunities.basic_info') }}</h3>
				</div>
				<div class="details-grid">
					<div class="detail-item">
						<label for="name">{{ __('opportunities.deal_name') }}</label>
						<input type="text" name="name" id="name" class="form-control" value="{{ old('name', $opportunity->name) }}" required>
					</div>

					<div class="grid-2-col">
						<div class="detail-item">
							<label for="account_id">{{ __('opportunities.account') }}</label>
							<select name="account_id" id="account_id" class="form-select">
								@foreach($accounts as $account)
									<option value="{{ $account->id }}" @selected($opportunity->account_id == $account->id)>
										{{ $account->name }}
									</option>
								@endforeach
							</select>
						</div>
						<div class="detail-item">
							<label for="contact_id">{{ __('opportunities.contact') }}</label>
							<select name="contact_id" id="contact_id" class="form-select">
								<option value="">Επιλογή Επαφής...</option>
								@foreach($contacts as $contact)
									<option value="{{ $contact->id }}" @selected($opportunity->contact_id == $contact->id)>
										{{ $contact->full_name }}
									</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="grid-2-col">
						<div class="detail-item">
							<label for="stage_id">{{ __('opportunities.stage.label') }}</label>
							<select name="stage_id" id="stage_id" class="form-select" data-lost-ids="{{ $lostStageIds->toJson() }}">
								@foreach($pipelines as $pipeline)
									<optgroup label="Pipeline: {{ $pipeline->name }}">
										@foreach($pipeline->stages as $stage)
											@php
												// Προσπαθούμε να βρούμε το Enum instance από το όνομα στη βάση
												// Αν το name είναι π.χ. 'discovery', το $stageEnum θα έχει το label 'Ανακάλυψη'
												$stageEnum = \App\Enums\Opportunities\OpportunityStage::tryFrom(strtolower($stage->name));
											@endphp
											<option value="{{ $stage->id }}" @selected($opportunity->stage_id == $stage->id)>
												{{ $stageEnum ? $stageEnum->label() : $stage->name }}
											</option>
										@endforeach
									</optgroup>
								@endforeach
							</select>
						</div>
						<div class="detail-item">
							<label for="owner_id">Ανάθεση σε</label>
							<select name="owner_id" id="owner_id" class="form-select">
								@foreach($users as $user)
									<option value="{{ $user->id }}" @selected($opportunity->owner_id == $user->id)>
										{{ $user->profile->full_name }}
									</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="detail-item">
						<label for="notes">Σημειώσεις</label>
						<textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $opportunity->notes) }}</textarea>
					</div>

					<div class="detail-item mt-4">
						<label class="mb-2 d-block font-weight-bold">{{ __('opportunities.tags') }}</label>
						<div class="tags-selector-grid">
							@foreach($allTags as $tag)
								<label class="tag-checkbox-label" style="--tag-color: {{ $tag->color }};">
									<input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked($opportunity->tags->contains($tag->id))>
									<span class="tag-pill">
										<i class="fas fa-tag"></i> {{ $tag->name }}
									</span>
								</label>
							@endforeach
						</div>
					</div>
				</div>
			</div>

			<div class="card info-card">
				<div class="card-header">
					<h3>{{ __('opportunities.financials') }}</h3>
				</div>
				<div class="details-grid">
					<div class="grid-financials">
						<div class="detail-item">
							<label for="amount">{{ __('opportunities.amount') }}</label>
							<input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount', $opportunity->amount) }}">
						</div>
						<div class="detail-item">
							<label for="currency">{{ __('opportunities.currency') }}</label>
							<input type="text" name="currency" id="currency" class="form-control" value="{{ $opportunity->currency }}" readonly>
						</div>
					</div>

					<div class="detail-item">
						<label for="probability">{{ __('opportunities.probability') }} (<span id="prob-val">{{ $opportunity->probability }}</span>%)</label>
						<input type="range" name="probability" id="probability" min="0" max="100" step="5" class="form-range" value="{{ old('probability', $opportunity->probability) }}" oninput="document.getElementById('prob-val').innerText = this.value">
					</div>

					<div class="detail-item">
						<label for="close_date">{{ __('opportunities.closing_date') }}</label>
						<input type="date" name="close_date" id="close_date" class="form-control" value="{{ $opportunity->close_date ? $opportunity->close_date->format('Y-m-d') : '' }}">
					</div>

					<div class="detail-item" id="loss-reason-group">
						<label for="loss_reason">Αιτία Απώλειας</label>
						<input type="text" name="loss_reason" id="loss_reason" class="form-control" value="{{ old('loss_reason', $opportunity->loss_reason) }}">
					</div>

					<div class="form-actions mt-4">
						<button type="submit" class="btn-action success btn-full">
							{{ __('globals.save_changes') ?? 'Αποθήκευση Αλλαγών' }}
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
@endsection

@push('scripts')
	<script src="{{ asset('js/opportunities/edit.js') }}"></script>
@endpush