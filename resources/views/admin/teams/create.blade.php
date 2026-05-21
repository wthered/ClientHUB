@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/admin/teams/create.css') }}">
@endpush

@section('content')
	<div class="main-content">
		<div class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Create New Team</h1>
				<p class="module-subtitle">
					<i class="fas fa-plus-circle"></i>
					Define a new organizational unit and assign roles.
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('teams.index') }}" class="btn-action">
					<i class="fas fa-arrow-left"></i> Cancel & Return
				</a>
			</div>
		</div>

		<div class="card creation-card">
			<form action="{{ route('teams.store') }}" method="POST" class="team-creation-form">
				@csrf

				<div class="form-grid">
					<div class="field-group">
						<label for="name" class="field-label">Team Name</label>
						<input type="text" name="name" id="name" class="field-input @error('name') has-error @enderror" placeholder="e.g. Sales Division" value="{{ old('name') }}" required>
						@error('name') <span class="field-error">{{ $message }}</span> @enderror
					</div>

					<div class="field-group">
						<label for="company_id" class="field-label">Organization / Company</label>
						<select name="company_id" id="company_id" class="field-select @error('company_id') has-error @enderror">
							<option value="">-- Choose Organization --</option>
							@foreach($companies as $company)
								<option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
									{{ $company->name }}
								</option>
							@endforeach
						</select>
						@error('company_id')
							<span class="field-error">{{ $message }}</span>
						@enderror
					</div>
				</div>

				<div class="field-group full-width">
					<label for="description" class="field-label">Description (Optional)</label>
					<textarea name="description" id="description" rows="3"
					          class="field-input textarea" placeholder="What is the purpose of this team?">{{ old('description') }}</textarea>
				</div>

				<div class="form-divider"><span>Leadership Hierarchy</span></div>

				<div class="form-grid">
					<div class="field-group">
						<label for="manager_id" class="field-label">Reporting Manager</label>
						<select name="manager_id" id="manager_id" class="field-select">
							<option value="">-- Select Manager --</option>
							@foreach($users as $user)
								<option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>
									{{ $user->profile->full_name }}
								</option>
							@endforeach
						</select>
						<small class="field-hint"><i class="fas fa-shield-alt"></i> Supervises team operations.</small>
					</div>

					<div class="field-group">
						<label for="leader_id" class="field-label">Operational Leader</label>
						<select name="leader_id" id="leader_id" class="field-select">
							<option value="">-- Select Team Leader --</option>
							@foreach($users as $user)
								<option value="{{ $user->id }}" {{ old('leader_id') == $user->id ? 'selected' : '' }}>
									{{ $user->profile->full_name }}
								</option>
							@endforeach
						</select>
						<small class="field-hint"><i class="fas fa-user-tie"></i> Direct management of members.</small>
					</div>
				</div>

				<div class="checkbox-wrapper mt-4">
					<input type="hidden" name="is_active" value="0">
					<input type="checkbox" name="is_active" id="is_active" value="1"
					       class="custom-checkbox" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
					<label for="is_active" class="checkbox-label">
						<span class="checkbox-box"></span>
						<span class="checkbox-text">Set as Active Team</span>
					</label>
				</div>

				<div class="form-footer">
					<button type="submit" class="btn-primary btn-large">
						<i class="fas fa-plus-circle"></i> Create Team Instance
					</button>
				</div>
			</form>
		</div>
	</div>
@endsection