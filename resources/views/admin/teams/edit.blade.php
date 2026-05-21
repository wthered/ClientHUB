@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/admin/teams/edit.css') }}">
@endpush

@section('content')
	<div class="main-content">
		<div class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Edit Team: {{ $team->name }}</h1>
				<div class="module-subtitle">
					<span><i class="fas fa-layer-group"></i>Configure team structure, leadership, and status.</span>
					@if($team->company)
						<span class="header-divider">|</span>
						<span class="company-context">
                    <i class="fas fa-building"></i>
                    <strong>{{ $team->company->name }}</strong>
                </span>
					@endif
				</div>
			</div>
			<div class="header-actions">
				<a href="{{ route('teams.index') }}" class="btn-action">
					<i class="fas fa-arrow-left"></i> Back to List
				</a>
			</div>
		</div>

		<div class="card">
			<form action="{{ route('teams.update', $team) }}" method="POST" class="form-standard">
				@csrf
				@method('PUT')

				<div class="form-section">
					<h3 class="section-title"><i class="fas fa-info-circle"></i> Basic Details</h3>
					<div class="form-row">
						<div class="form-group">
							<label for="name" class="form-label">Team Name</label>
							<input type="text" name="name" id="name"
							       class="form-control @error('name') is-invalid @enderror"
							       value="{{ old('name', $team->name) }}" required>
							@error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
						</div>

						<div class="form-group">
							<label for="company_id" class="form-label">Associated Company</label>
							<select name="company_id" id="company_id" class="form-select">
								<option value="">-- Select Company --</option>
								@foreach($companies as $company)
									<option value="{{ $company->id }}" {{ old('company_id', $team->company_id) == $company->id ? 'selected' : '' }}>
										{{ $company->name }}
									</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-group mt-4">
						<label for="description" class="form-label">Description</label>
						<textarea name="description" id="description" rows="3"
						          class="form-control">{{ old('description', $team->description) }}</textarea>
					</div>
				</div>

				<hr class="nav-divider">

				<div class="form-section">
					<h3 class="section-title"><i class="fas fa-sitemap"></i> Leadership Hierarchy</h3>
					<div class="form-row">
						<div class="form-group">
							<label for="manager_id" class="form-label">Team Manager (Supervisor)</label>
							<select name="manager_id" id="manager_id" class="form-select">
								<option value="">-- No Manager Assigned --</option>
								@foreach($users as $user)
									<option value="{{ $user->id }}" {{ old('manager_id', $team->manager_id) == $user->id ? 'selected' : '' }}>
										{{ $user->profile->full_name }}
									</option>
								@endforeach
							</select>
							<span class="form-text"><i class="fas fa-user-shield"></i> High-level oversight.</span>
						</div>

						<div class="form-group">
							<label for="leader_id" class="form-label">Team Leader (Operational)</label>
							<select name="leader_id" id="leader_id" class="form-select">
								<option value="">-- No Leader Assigned --</option>
								@foreach($users as $user)
									<option value="{{ $user->id }}" {{ old('leader_id', $team->leader_id) == $user->id ? 'selected' : '' }}>
										{{ $user->profile->full_name }}
									</option>
								@endforeach
							</select>
							<span class="form-text"><i class="fas fa-user-tie"></i> Daily operations head.</span>
						</div>
					</div>
				</div>

				<hr class="nav-divider">

				<div class="form-section">
					<div class="checkbox-wrapper">
						<input type="hidden" name="is_active" value="0">
						<input type="checkbox" name="is_active" id="is_active" value="1"
						       class="custom-checkbox" {{ old('is_active', $team->is_active) ? 'checked' : '' }}>
						<label for="is_active" class="checkbox-label">
							<span class="checkbox-box"></span>
							<span class="checkbox-text">Team is currently Active</span>
						</label>
					</div>
				</div>

				<div class="form-actions flex-end gap-3">
					<a href="{{ route('teams.index') }}" class="btn-action">Cancel Changes</a>
					<button type="submit" class="btn-primary">
						<i class="fas fa-check-circle"></i> Update Team
					</button>
				</div>
			</form>
		</div>
	</div>
@endsection