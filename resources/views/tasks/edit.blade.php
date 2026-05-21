@extends('layouts.app')

@section('title', 'Επεξεργασία Εργασίας')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/tasks/edit.css') }}">
@endpush

@section('content')
	<div class="main-content">

		<header class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Επεξεργασία: {{ $task->subject }}</h1>
				<p class="module-subtitle">
					<i class="fas fa-fingerprint"></i> ID: #{{ $task->id }}
					<span class="nav-divider" style="display:inline-block; height:10px; margin:0 10px;"></span>
					<i class="fas fa-user-edit"></i> Δημιουργήθηκε από: {{ $task->creator->name ?? 'Σύστημα' }}
				</p>
			</div>

			<div class="header-actions">
				<a href="{{ route('tasks.index') }}" class="btn-secondary">
					<i class="fas fa-arrow-left"></i> Επιστροφή
				</a>
			</div>
		</header>

		<div class="form-container">
			<div class="card">
				<form action="{{ route('tasks.update', $task->id) }}" method="POST">
					@csrf
					@method('PUT')

					<div class="form-grid">
						<div class="form-group full-width">
							<label for="subject" class="form-label">@lang('tasks.labels.subject')</label>
							<input type="text" name="subject" id="subject"
							       class="form-control @error('subject') is-invalid @enderror"
							       value="{{ old('subject', $task->subject) }}" required>
							@error('subject')
							<span class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group">
							<label for="user_id" class="form-label">@lang('tasks.labels.assigned_to')</label>
							<select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
								@foreach($users as $user)
									<option value="{{ $user->id }}" {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>
										{{ $user->profile->last_name ?? '' }} {{ $user->profile->first_name ?? $user->name }}
									</option>
								@endforeach
							</select>
							@error('user_id')
							<span class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group">
							<label for="priority" class="form-label">@lang('tasks.labels.priority')</label>
							<select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror">
								@foreach($priorities as $priority)
									<option value="{{ $priority->value }}" {{ old('priority', $task->priority->value ?? $task->priority) == $priority->value ? 'selected' : '' }}>
										{{ $priority->label() }}
									</option>
								@endforeach
							</select>
							@error('priority')
							<span class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group">
							<label for="status" class="form-label">@lang('tasks.labels.status')</label>
							<select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
								@foreach($statuses as $status)
									<option value="{{ $status->value }}" {{ old('status', $task->status->value ?? $task->status) == $status->value ? 'selected' : '' }}>
										{{ $status->label() }}
									</option>
								@endforeach
							</select>
							@error('status')
							<span class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group">
							<label for="due_date" class="form-label">@lang('tasks.labels.due_date')</label>
							<input type="datetime-local" name="due_date" id="due_date"
							       class="form-control @error('due_date') is-invalid @enderror"
							       value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}">
							@error('due_date')
							<span class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group full-width">
							<label for="description" class="form-label">@lang('tasks.labels.description')</label>
							<textarea name="description" id="description"
							          class="form-control @error('description') is-invalid @enderror">{{ old('description', $task->description) }}</textarea>
							@error('description')
							<span class="invalid-feedback" style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span>
							@enderror
						</div>

						@if($task->taskable)
							<div class="form-group full-width">
								<div style="background: var(--gray-50); padding: 10px; border-radius: var(--radius); border: 1px dashed var(--gray-300); font-size: 0.85rem;">
									<i class="fas fa-link"></i> @lang('tasks.labels.related_to'):
									<strong>{{ class_basename($task->taskable_type) }}</strong>
									(ID: {{ $task->taskable_id }})
								</div>
							</div>
						@endif
					</div>

					<div class="form-footer">
						<button type="submit" class="btn-primary">
							<i class="fas fa-save"></i> @lang('tasks.labels.edit')
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection