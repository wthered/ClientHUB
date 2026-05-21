<aside class="sidebar">
	<div class="sidebar-header">
		<span class="logo-icon">🚀</span> CRM_Pro
	</div>

	<nav class="sidebar-nav">
		<ul>
			<li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
				<a href="{{ route('dashboard') }}">
					<span class="nav-icon">📊</span>
					<span>Dashboard</span>
				</a>
			</li>

			<li class="nav-label">Relationship Management</li>
			<li class="{{ Request::is('accounts*') ? 'active' : '' }}">
				<a href="{{ route('accounts.index') }}">
					<span class="nav-icon">🏢</span>
					<span>Accounts</span>
				</a>
			</li>
			<li class="{{ Request::is('contacts*') ? 'active' : '' }}">
				<a href="{{ route('contacts.index') }}">
					<span class="nav-icon">👤</span>
					<span>Contacts</span>
				</a>
			</li>

			<li class="nav-label">Sales Pipeline</li>
			<li class="{{ Request::is('leads*') ? 'active' : '' }}">
				<a href="{{ route('leads.index') }}">
					<span class="nav-icon">🎯</span>
					<span>Leads</span>
				</a>
			</li>
			<li class="{{ Request::is('opportunities*') ? 'active' : '' }}">
				<a href="{{ route('opportunities.index') }}">
					<span class="nav-icon">💰</span>
					<span>Opportunities</span>
				</a>
			</li>

			<li class="{{ Request::is('deals*') ? 'active' : '' }}">
				<a href="{{ route('deals.index') }}">
					<span class="nav-icon">🤝</span>
					<span>Deals</span>
				</a>
			</li>

			<li class="nav-label">Operations</li>
			<li class="{{ Request::is('tasks*') ? 'active' : '' }}">
				<a href="{{ route('tasks.index') }}">
					<span class="nav-icon">✅</span>
					<span>Tasks</span>
				</a>
			</li>

			<li class="{{ Request::is('activities*') ? 'active' : '' }}">
				<a href="{{ route('activities.index') }}">
					<span class="nav-icon">📅</span>
					<span>Activities</span>
				</a>
			</li>
			<li class="{{ Request::is('invoices*') ? 'active' : '' }}">
				<a href="{{ route('invoices.index') }}">
					<span class="nav-icon">🧾</span>
					<span>Invoices</span>
				</a>
			</li>

			<li class="{{ Request::is('payments*') ? 'active' : '' }}">
				<a href="{{ route('payments.index') }}">
					<span class="nav-icon">💸</span>
					<span>Payments</span>
				</a>
			</li>

			<!-- ΝΕΟ: Audit Logs (Security Guardian) -->
			<li class="{{ Request::is('audit-logs*') ? 'active' : '' }}">
				<a href="{{ route('audit-logs.index') }}">
					<span class="nav-icon">🔍</span>
					<span>Audit Logs</span>
				</a>
			</li>

			<li class="nav-divider"></li>

			@if(request()->user()->hasAnyRole(['admin', 'super-admin']))
				<li class="{{ Route::is('teams.*') ? 'active' : '' }}">
					<a href="{{ route('teams.index') }}">
						<span class="nav-icon">🛡️</span>
						<span>Team Management</span>
					</a>
				</li>
			@endif

			<li class="{{ Request::is('settings*') || Request::is('profile*') ? 'active' : '' }}">
				<a href="{{ route('profile.settings.index') }}">
					<span class="nav-icon">⚙️</span>
					<span>Settings</span>
				</a>
			</li>
		</ul>
	</nav>

	<div class="sidebar-footer">
		@auth
			<a href="{{ route('profile.edit') }}" style="text-decoration: none; color: inherit;">
				<div class="user-pill">
					<div class="user-avatar-wrapper" style="width: 24px; height: 24px;">
						@if(auth()->user()->profile->avatar_url)
							<img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }} Avatar" class="user-avatar-img">
						@else
							<div class="user-avatar-placeholder" style="font-size: 0.6rem;">
								{{ auth()->user()->initials }}
							</div>
						@endif
						<span class="status-indicator online"></span>
					</div>
					<span class="user-name">{{ auth()->user()->name }}</span>
				</div>
			</a>
		@endauth
	</div>
</aside>