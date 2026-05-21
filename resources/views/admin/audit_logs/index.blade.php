@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/admin/audits/index.css') }}">
@endpush

@section('content')
	<div class="container-fluid">
		<div class="audit-header-actions">
			<div class="header-title-group">
				<h1 class="module-title">🛡️ Security Guardian</h1>
				<p class="module-subtitle">
					<i class="fas fa-history"></i> Πλήρες ιστορικό ενεργειών και μεταβολών συστήματος
				</p>
			</div>
			<div class="audit-stats-pill">
				<span class="label">Σύνολο εγγραφών</span>
				<span class="value">{{ $logs->total() }}</span>
			</div>
		</div>

		<div class="card shadow-sm">
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table-custom">
						<thead>
						<tr>
							<th>Timestamp</th>
							<th>User</th>
							<th>Action</th>
							<th>Entity</th>
							<th>Changes</th>
							<th>Metadata</th>
						</tr>
						</thead>
						<tbody>
						@foreach($logs as $log)
							<tr>
								<td>
									<div class="meta-info">
										<strong>{{ $log->created_at->format('d/m/Y') }}</strong>
										<span>{{ $log->created_at->format('H:i:s') }}</span>
									</div>
								</td>
								<td>
									<div class="user-info-text">
										<span class="user-name">{{ $log->user->profile->full_name ?? 'System' }}</span>
										<span class="user-role">{{ $log->user->getRoleNames()->implode(', ') ?? 'N/A' }}</span>
									</div>
								</td>
								<td>
                                    <span class="action-badge action-{{ $log->action }}">
                                        {{ $log->action }}
                                    </span>
								</td>
								<td>
									@if($log->auditable_type)
										<div class="entity-pill">
											<span class="entity-type">{{ class_basename($log->auditable_type) }}</span>
											<span class="entity-id">ID: #{{ $log->auditable_id }}</span>
										</div>
									@else
										<span class="text-muted small">—</span>
									@endif
								</td>
								<td>
									@if($log->action === 'updated')
										<div class="diff-container">
											<button class="diff-toggle" onclick="this.nextElementSibling.classList.toggle('show')">
												View Changes <i class="fas fa-chevron-down"></i>
											</button>
											<div class="diff-content">
												@foreach($log->new_values as $key => $value)
													<div class="change-row">
														<span class="change-key">{{ $key }}:</span>
														<span class="change-old">{{ $log->old_values[$key] ?? 'null' }}</span>
														→
														<span class="change-new">{{ $value }}</span>
													</div>
												@endforeach
											</div>
										</div>
									@else
										<span class="text-muted small">Initial data logged</span>
									@endif
								</td>
								<td>
									<div class="meta-info">
										<span><i class="fas fa-network-wired"></i> {{ $log->ip_address }}</span>
										<span title="{{ $log->user_agent }}"><i class="fas fa-laptop"></i> Agent</span>
									</div>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer bg-white">
				{{ $logs->links('vendor.pagination.custom') }}
			</div>
		</div>
	</div>
@endsection