@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/admin/teams/index.css') }}">
@endpush

@section('content')
	<div class="main-content">
		<div class="module-header">
			<div class="header-title-group">
				<h1 class="module-title">Teams Management</h1>
				<p class="module-subtitle">
					<i class="fas fa-users-cog"></i>
					Organize users into teams and assign leaders
				</p>
			</div>
			<div class="header-actions">
				<a href="{{ route('teams.create') }}" class="btn-primary">
					<i class="fas fa-plus"></i> Create New Team
				</a>
			</div>
		</div>

		<div class="card">
			<div class="table-responsive">
				<table class="table-custom">
					<thead>
					<tr>
						<th>Team Name</th>
						<th>Team Leader</th>
						<th>Members Count</th>
						<th>Members</th>
						<th class="text-end">Actions</th>
					</tr>
					</thead>
					<tbody>
					@forelse($teams as $team)
						<tr>
							<td>
								<div class="flex-column">
									<div class="d-flex align-items-center gap-2">
										<span class="bold">{{ $team->name }}</span>
										<span class="status-indicator-inline {{ $team->is_active ? 'online' : 'busy' }}" title="{{ $team->is_active ? 'Active' : 'Inactive' }}"></span>
									</div>
									@if($team->company)
										<span class="company-tag">
											<i class="fas fa-building"></i> {{ $team->company->name }}
										</span>
									@endif
								</div>
							</td>
							<td>
								<div class="flex-column gap-2">
									@if($team->leader)
										<div class="user-info" title="Team Leader">
											<div class="avatar-sm">{{ $team->leader->initials }}</div>
											<span class="bold small">{{ $team->leader->profile->full_name }}</span>
										</div>
									@else
										<span class="text-muted italic small">No Leader</span>
									@endif

									@if($team->manager)
										<div class="user-info opacity-75" title="Team Manager">
											<div class="avatar-sm manager-variant"><i class="fas fa-user-shield"></i></div>
											<span class="text-muted small">{{ $team->manager->profile->full_name }}</span>
										</div>
									@endif
								</div>
							</td>
							<td>
								<span class="badge-count">{{ $team->members->count() }} Users</span>
							</td>
							<td>
								<div class="avatar-group">
									@foreach($team->members->take(5) as $member)
										<div class="avatar-stack" title="{{ $member->profile->full_name }}">
											{{ strtoupper($member->initials) }}
										</div>
									@endforeach
									@if($team->members->count() > 5)
										<div class="avatar-stack more">
											+{{ $team->members->count() - 5 }}
										</div>
									@endif
								</div>
							</td>
							<td class="text-end">
								<div class="table-actions">
									<a href="{{ route('teams.show', $team) }}" class="btn-view-details" title="View Members">
										<i class="fas fa-eye"></i>
									</a>
									<a href="{{ route('teams.edit', $team) }}" class="btn-view-details" title="Edit Identity">
										<i class="fas fa-edit"></i>
									</a>
									<form action="{{ route('teams.destroy', $team) }}" method="POST" class="inline-form">
										@csrf
										@method('DELETE')
										<button type="submit"
										        class="btn-view-details btn-delete"
										        title="Delete Team"
										        onclick="return confirm('Are you sure? This will remove all member associations.')">
											<i class="fas fa-trash"></i>
										</button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="5" class="text-center text-muted py-4">No teams found.</td>
						</tr>
					@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection