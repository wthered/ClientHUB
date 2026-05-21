@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/tasks/index.css') }}">
@endpush

@section('content')
	{{-- Χρήση του Global Module Header --}}
	<div class="module-header">
		<div class="header-title-group">
			<h1 class="module-title"><span class="nav-icon">✅</span> Διαχείριση Tasks</h1>
			<p class="module-subtitle">
				<i class="fas fa-info-circle"></i> Παρακολουθήστε και οργανώστε τις εκκρεμότητες της ομάδας σας.
			</p>
		</div>
		<div class="header-actions">
			<a href="{{ route('tasks.create') }}" class="btn-primary">
				<i class="fas fa-plus"></i> Νέο Task
			</a>
		</div>
	</div>

	<div class="main-content">

		{{-- Filtering Bar --}}
		<form action="{{ route('tasks.index') }}" method="GET" class="filters-bar">
			<div class="filter-group">
				<label>Status</label>
				<select name="status" class="filter-control">
					<option value="">Όλα</option>
					@foreach($statuses as $value => $label)
						<option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
					@endforeach
				</select>
			</div>

			<div class="filter-group">
				<label>Priority</label>
				<select name="priority" class="filter-control">
					<option value="">Όλες</option>
					@foreach($priorities as $value => $label)
						<option value="{{ $value }}" {{ request('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
					@endforeach
				</select>
			</div>

			<div class="filter-group">
				<label>Από (Λήξη)</label>
				<input type="date" name="date_from" class="filter-control" value="{{ request('date_from') }}">
			</div>

			<div class="filter-group">
				<label>Έως (Λήξη)</label>
				<input type="date" name="date_to" class="filter-control" value="{{ request('date_to') }}">
			</div>

			<div class="filter-group">
				<label>Αναζήτηση</label>
				<input type="text" name="search" class="filter-control" placeholder="Θέμα ή περιγραφή..." value="{{ request('search') }}">
			</div>

			<button type="submit" class="btn-primary" style="padding: 8px 16px;">
				<i class="fas fa-filter"></i>
			</button>

			<a href="{{ route('tasks.index') }}" class="btn-action" title="Καθαρισμός">
				<i class="fas fa-sync"></i>
			</a>
		</form>

		<div class="card p-0">
			<div class="card-body p-0">
				{{-- Χρήση της Global κλάσης table-custom --}}
				<table class="table-custom">
					<thead>
					<tr>
						<th class="text-start">{{ __('tasks.labels.subject') }} & {{ __('tasks.labels.description') }}</th>
						<th>{{ __('tasks.labels.priority') }}</th>
						<th>{{ __('tasks.labels.status') }}</th>
						<th>{{ __('tasks.labels.related_to') }}</th>
						<th>{{ __('tasks.labels.due_date') }}</th>
						<th>{{ __('tasks.labels.assigned_to') }}</th>
						<th class="text-end">{{ __('tasks.labels.actions') }}</th>
					</tr>
					</thead>
					<tbody>
					@forelse($tasks as $task)
						<tr>
							<td style="max-width: 300px;">
								<div class="fw-bold text-dark">{{ $task->subject }}</div>
								<div class="text-muted small text-truncate">{{ $task->description }}</div>
							</td>
							<td>
                                <span class="badge badge-priority-{{ $task->priority->value }}">
                                    {{ $task->priority->label() }}
                                </span>
							</td>
							<td>
                                <span class="badge badge-status-{{ $task->status->value }}">
                                    {{ $task->status->label() }}
                                </span>
							</td>
							<td>
								@if($task->taskable)
									<div class="d-flex align-items-center gap-1 small">
										<a href="{{ $task->taskable->taskable_url }}" class="text-decoration-none text-dark fw-medium">
											{{ $task->taskable->taskable_label }}
										</a>
									</div>
								@else
									<span class="text-muted small">—</span>
								@endif
							</td>
							<td>
								<div class="due-date-wrapper {{ $task->date_class }}">
									<div class="date-main">
										{{ $task->due_date->format('d M, Y') }}
									</div>
									<div class="date-sub">
										{{ $task->days_text }}
									</div>
								</div>
							</td>
							<td>
								<div class="user-pill-sm">
									<div style="width: 20px; height: 20px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; margin-right: 6px;">
										{{ substr($task->user->name ?? 'U', 0, 1) }}
									</div>
									<span class="user-name">{{ $task->user->profile->full_name ?? 'Unassigned' }}</span>
								</div>
							</td>
							<td class="text-end">
								<div class="btn-group">
									<a href="{{ route('tasks.edit', $task->id) }}" class="btn-icon" title="{{ __('tasks.labels.edit') }}">
										<span>✏️</span>
									</a>

									<form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Είσαι σίγουρος;');">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn-icon text-danger" title="{{ __('tasks.labels.delete') }}">
											<span>🗑️</span>
										</button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="7" class="text-center py-5 text-muted">
								{{ __('tasks.messages.not_found') }}
							</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>

			@if($tasks->hasPages())
				<div class="card-footer">
					{{ $tasks->links('vendor.pagination.custom') }}
				</div>
			@endif
		</div>
	</div>

	@if ($errors->any())
		<div class="toast-container">
			@foreach ($errors->all() as $error)
				<div class="toast error">
					<i class="fas fa-exclamation-circle"></i>
					<span>{{ $error }}</span>
				</div>
			@endforeach
		</div>

		<script>
            // Αυτόματο κλείσιμο των toasts μετά από 5 δευτερόλεπτα
            document.querySelectorAll('.toast').forEach(toast => {
                setTimeout(() => {
                    toast.classList.add('fade-out');
                    setTimeout(() => toast.remove(), 500);
                }, 5000);
            });
		</script>
	@endif
@endsection

@push('scripts')
	<script src="{{ asset('js/tasks/filtering.js') }}"></script>
@endpush