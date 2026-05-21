@foreach($activities as $log)
	<tr>
		{{-- 1. Χρήστης --}}
		<td class="log-user">
			<div class="flex-align-center" style="gap: 12px;">
				<div class="user-avatar-wrapper" style="width: 32px; height: 32px;">
					@if($log->user)
						@if($log->user->profile->avatar_url)
							<img src="{{ $log->user->profile->avatar_url }}"
							     alt="{{ $log->user->profile->full_name }}"
							     title="{{ $log->user->profile->full_name }}"
							     class="user-avatar-img">
						@else
							<div class="user-avatar-placeholder">
								{{ $log->user->initials }}
							</div>
						@endif
					@else
						<div class="user-avatar-placeholder" style="background: var(--gray-200); color: var(--gray-600);">
							<i class="fas fa-robot"></i>
						</div>
					@endif
				</div>

				<div class="flex-column">
                    <span class="bold" style="font-size: 0.9rem;">
                        {{ $log->user->profile->full_name ?? 'Σύστημα' }}
                    </span>
					@if($log->user)
						<small class="text-muted" style="font-size: 0.7rem;">
							{{ $log->user->getRoleNames()->first() ?? '' }}
						</small>
					@endif
				</div>
			</div>
		</td>

		{{-- 2. Ενέργεια --}}
		<td class="log-action">
			<div class="flex-align-center" style="gap: 8px;">
				{{-- Εικονίδιο Τύπου (System/Audit/Activity) --}}
				<span title="{{ $log->log_type->label() }}" style="font-size: 1.1rem; cursor: help;">
                    {{ $log->log_type->icon() }}
                </span>

				{{-- Badge Ενέργειας (Login, Create, κλπ) --}}
				<span class="status-badge {{ $log->event->colorClass() }}" title="{{ $log->event->label() }}">
                    {{ $log->event->label() }}
                </span>
			</div>
		</td>

		{{-- 3. Περιγραφή --}}
		<td class="log-description">
			<span class="text-muted" style="font-size: 0.85rem;">{{ $log->description }}</span>
		</td>

		{{-- 4. Σχετίζεται με (Model Type) --}}
		<td class="log-entity">
			@if($log->model_type)
				<span class="collab-badge" style="{{ $log->model_type->colors() }}">
                    <span style="margin-right: 4px;">{{ $log->model_type->icon() }}</span>
                    {{ $log->model_type->label() }}
                </span>
			@else
				<span class="collab-badge">{{ class_basename($log->loggable_type) }}</span>
			@endif
		</td>

		{{-- 5. Ημερομηνία --}}
		<td class="log-date">
			<small class="text-muted" title="{{ $log->created_at->format('d/m/Y H:i') }}">
				{{ $log->created_at->diffForHumans() }}
			</small>
		</td>

		{{-- 6. Ενέργειες --}}
		<td class="log-actions text-right">
			<div style="display: flex; gap: 5px; justify-content: flex-end;">
				@if($log->model_type && Route::has($log->model_type->route()))
					<a href="{{ route($log->model_type->route(), $log->loggable_id) }}" class="btn-action" title="Προβολή">
						<i class="fas fa-eye"></i>
					</a>
				@endif

				@if($log->properties && count((array)$log->properties) > 0)
					<button type="button"
					        class="btn-view-details"
					        data-props="{{ json_encode($log->properties) }}"
					        title="Ιστορικό">
						<i class="fas fa-history"></i>
					</button>
				@endif
			</div>
		</td>
	</tr>
@endforeach