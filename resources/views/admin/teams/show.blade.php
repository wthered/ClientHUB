@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/admin/teams/show.css') }}">
@endpush

@section('content')
	<div class="main-content">
		<div class="module-header">
			<div class="header-title-group">
				<div class="breadcrumb-nav">
					<a href="{{ route('teams.index') }}">Teams</a> / <span>Team Details</span>
				</div>
				<h1 class="module-title">
					{{ $team->name }} {{ request()->route()->getName() }}
					<span class="status-indicator-inline {{ $team->is_active ? 'online' : 'busy' }}"
					      title="{{ $team->is_active ? 'Active' : 'Inactive' }}"
					      style="width: 12px; height: 12px; margin-left: 10px;"></span>
				</h1>

				<div class="module-subtitle mt-1">
					@if($team->company)
						<span class="company-context">
                            <i class="fas fa-building"></i>
                            <strong>{{ $team->company->name }}</strong>
                        </span>
					@else
						<span><i class="fas fa-eye"></i> Team Overview & Member List</span>
					@endif
				</div>
			</div>
			<div class="header-actions">
				<a href="{{ route('teams.edit', $team) }}" class="btn-primary btn-outline">
					<i class="fas fa-edit"></i> Edit Team
				</a>
			</div>
		</div>

		<div class="team-grid">
			<div class="team-sidebar">

				{{-- Team Overview Card --}}
				<div class="card info-card {{ !$team->leader_id ? 'unassigned-border' : '' }}">
					<div class="card-header-simple">Team Hierarchy</div>

					<div class="info-list">
						{{-- SECTION: MANAGER --}}
						<div class="info-item">
							<span class="info-label">Reporting Manager</span>
							<div class="user-pill manager-style">
								@if($team->manager)
									<div class="avatar-sm manager-variant">
										@if($team->manager->profile?->avatar_url)
											<img src="{{ $team->manager->profile->avatar_url }}" alt="Manager">
										@else
											<span class="avatar-text">{{ $team->manager->initials }}</span>
										@endif
									</div>
									<div class="user-meta">
										<span class="bold">{{ $team->manager->profile->full_name ?? $team->manager->name }}</span>
									</div>
								@else
									<span class="text-muted italic">No Manager assigned</span>
								@endif
							</div>
						</div>

						{{-- SECTION: LEADER --}}
						<div class="info-item">
							<span class="info-label">Current Leader</span>
							<div class="leader-display-box {{ !$team->leader_id ? 'is-empty' : '' }}">
								@if($team->leader)
									<div class="user-pill">
										<div class="avatar-md">
											@if($team->leader->profile && $team->leader->profile->avatar_url)
												<img src="{{ $team->leader->profile->avatar_url }}" alt="Leader Avatar">
											@else
												<span class="avatar-text">{{ $team->leader->initials }}</span>
											@endif
											<div class="leader-badge"><i class="fas fa-crown"></i></div>
										</div>
										<div class="user-meta">
											<span class="bold">{{ $team->leader->profile->full_name ?? $team->leader->name }}</span>
											<small class="text-muted">{{ $team->leader->email }}</small>
										</div>
									</div>
								@else
									<div class="unassigned-warning">
										<i class="fas fa-exclamation-triangle"></i>
										<span>No Leader Assigned</span>
										<small class="text-muted" style="display:block; font-size: 0.7rem; margin-top: 5px;">
											Promote a member using the star icon in the table.
										</small>
									</div>
								@endif
							</div>
						</div>

						<div class="info-item">
							<span class="info-label">Created At</span>
							<span class="bold">{{ $team->created_at->format('d M Y') }}</span>
						</div>
					</div>
				</div>

				{{-- Add Member Card --}}
				<div class="card add-member-card mt-3">
					<div class="card-header-simple">Add New Member</div>
					<form action="{{ route('teams.assign', $team) }}" method="POST" class="p-3">
						@csrf
						<div class="form-group mb-3">
							<label class="form-label">Search User</label>
							<select name="user_id" class="form-select" required>
								<option value="">Select a User...</option>
								@foreach($users as $user)
									<option value="{{ $user->id }}">
										{{ $user->profile->full_name }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group mb-4">
							<label class="form-label">Assign Role</label>
							<select name="role" class="form-select" required>
								@foreach($roles as $role)
									<option value="{{ $role->value }}">
										{{ $role->label() }}
									</option>
								@endforeach
							</select>
						</div>

						<button type="submit" class="btn-primary w-100">
							<i class="fas fa-plus"></i> Add to Team
						</button>
					</form>
				</div>
			</div>

			<div class="team-main">
				<div class="card">
					<div class="card-header-between">
						<h3 class="card-title">Team Members ({{ $team->members->count() }})</h3>
					</div>
					<div class="table-responsive">
						<table class="table-custom">
							<thead>
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Role</th>
								<th class="text-end">Action</th>
							</tr>
							</thead>
							<tbody>
							@forelse($team->members as $member)
								<tr>
									<td>
										<div class="user-info">
											{{-- Leader Star Logic --}}
											<form action="{{ route('teams.set-leader', [$team, $member]) }}" method="POST" class="leader-form">
												@csrf
												@method('PATCH')
												<button type="submit" class="btn-star {{ $team->leader_id == $member->id ? 'is-leader' : '' }}" title="Set as Leader">
													<i class="fa{{ $team->leader_id == $member->id ? 's' : 'r' }} fa-star"></i>
												</button>
											</form>

											<div class="avatar-sm">
												@if($member->profile->avatar_url)
													<img src="{{ $member->profile->avatar_url }}" alt="Member">
												@else
													{{ strtoupper(substr($member->name, 0, 1)) }}
												@endif
											</div>
											<span class="bold">{{ $member->profile->full_name }}</span>
										</div>
									</td>
									<td>{{ $member->email }}</td>
									<td>
										@php $teamRole = \App\Enums\TeamRole::tryFrom($member->pivot->role); @endphp
										<span class="badge-role {{ $teamRole?->badgeClass() }}">
                                            {{ $teamRole ? $teamRole->label() : 'No Role' }}
                                        </span>
									</td>
									<td class="text-end">
										<form action="{{ route('teams.remove-user', [$team, $member]) }}" method="POST" class="delete-form">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn-view-details btn-delete" title="Remove Member">
												<i class="fas fa-user-minus"></i>
											</button>
										</form>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="4" class="text-center py-5">
										<p class="text-muted italic">No members in this team yet.</p>
									</td>
								</tr>
							@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
    <script type="application/javascript" src="{{ asset('js/teams/management.js') }}"></script>

    @if(session('success'))
	    <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.showToast === 'function') {
                    window.showToast("{{ session('success') }}");
                }
            });
	    </script>
    @endif
@endpush