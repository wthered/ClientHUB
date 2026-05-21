<div class="top-bar-right">

	<header class="top-bar">
		<div class="top-bar-left">
			<button id="sidebar-toggle" class="btn-icon">☰</button>
			<span class="page-title">Dashboard</span>
		</div>

		<div class="top-bar-right">
			<div class="top-bar-actions">
				@auth

					<div class="notifications-container">
						<button id="notifications-toggle-btn" class="notifications-toggle-btn" title="Ειδοποιήσεις">
							<span class="icon">🔔</span>
							@if(auth()->user()->unreadNotifications->count() > 0)
								<span class="notification-badge" id="noti-badge-count">
									{{ auth()->user()->unreadNotifications->count() }}
								</span>
							@endif
						</button>

						<div id="notifications-dropdown" class="notifications-dropdown-menu">
							<div class="notifications-header">
								<h3>Ειδοποιήσεις</h3>
								<button id="mark-all-read-btn"
								        class="mark-all-read-btn"
								        data-url="{{ route('notifications.markAllRead') }}"
								        style="background: none; border: none; cursor: pointer;">
									Καθαρισμός
								</button>
							</div>

							<div class="notifications-list" id="notifications-list">
								@forelse(auth()->user()->unreadNotifications as $notification)
									<a href="{{ $notification->data['action_url'] ?? '#' }}" class="notification-item">
										<div class="noti-content">
											<p class="noti-title">{{ $notification->data['title'] }}</p>
											<p class="noti-text">{{ $notification->data['message'] }}</p>
											<span class="noti-time">{{ $notification->created_at->diffForHumans() }}</span>
										</div>
									</a>
								@empty
									<div class="notifications-empty" style="padding: 20px; text-align: center; color: var(--text-muted);">
										<p>Δεν υπάρχουν νέες ειδοποιήσεις</p>
									</div>
								@endforelse
							</div>
						</div>
					</div>

					<div class="language-selector">
						<div class="lang-dropdown-container">
							<button id="lang-menu-toggle" class="btn-icon lang-btn" title="Αλλαγή Γλώσσας">
								{{-- Εμφάνιση της τρέχουσας γλώσσας (π.χ. σημαία ή κείμενο) --}}
								<span class="current-lang-icon">
									@if(app()->getLocale() == 'el')
										🇬🇷
									@else
										🇬🇧
									@endif
								</span>
							</button>

							<div id="lang-dropdown" class="lang-dropdown-menu">
								<a href="{{ route('language.switch', 'el') }}" class="dropdown-item {{ app()->getLocale() == 'el' ? 'active' : '' }}">
									<span class="lang-flag">🇬🇷</span> Ελληνικά
								</a>
								<a href="{{ route('language.switch', 'en') }}" class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
									<span class="lang-flag">🇬🇧</span> English
								</a>
							</div>
						</div>
					</div>
					<div class="user-dropdown-container">
						<button id="user-menu-toggle" class="user-menu-btn" aria-haspopup="true" aria-expanded="false">
							<div class="user-avatar-wrapper">
								@if(auth()->user()->profile->avatar_url)
									<img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }} Avatar" class="user-avatar-img">
								@else
									<div class="user-avatar-placeholder bg-primary-light">
										{{ auth()->user()->initials }}
									</div>
								@endif
								<span class="status-indicator online"></span>
							</div>

							<div class="user-info-text logout-text">
								<span class="user-name">{{ auth()->user()->profile->first_name . ' ' . auth()->user()->profile->last_name }}</span>
								<span class="user-role">{{ auth()->user()->roles->first()->name ?? 'User' }}</span>
							</div>

							<span class="dropdown-caret logout-text">▼</span>
						</button>

						<div id="user-dropdown" class="user-dropdown-menu">
							<div class="dropdown-header logout-text">
								<p>Λογαριασμός</p>
							</div>

							<a href="{{ route('profile.show') }}" class="dropdown-item">
								<span class="dropdown-icon">👤</span> Προφίλ
							</a>
							<a href="{{ route('profile.settings.index') }}" class="dropdown-item">
								<span class="dropdown-icon">⚙️</span> Ρυθμίσεις
							</a>

							<div class="dropdown-divider"></div>

							<form action="{{ route('logout') }}" method="POST" class="inline-form">
								@csrf
								<button type="submit" class="dropdown-item dropdown-item-logout" title="Αποσύνδεση">
									<span class="dropdown-icon">🚪</span> Αποσύνδεση
								</button>
							</form>
						</div>
					</div>
				@endauth
			</div>
		</div>
	</header>
</div>