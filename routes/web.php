<?php

	use App\Enums\Language;
	use App\Http\Controllers\AccountController;
	use App\Http\Controllers\ActivityController;
	use App\Http\Controllers\AuditLogController;
	use App\Http\Controllers\Auth\LoginController;
	use App\Http\Controllers\ContactController;
	use App\Http\Controllers\DashboardController;
	use App\Http\Controllers\DealController;
	use App\Http\Controllers\Finance\InvoiceController;
	use App\Http\Controllers\Finance\PaymentController;
	use App\Http\Controllers\LeadController;
	use App\Http\Controllers\OpportunityController;
	use App\Http\Controllers\Profile\NotificationController;
	use App\Http\Controllers\Profile\ProfileController;
	use App\Http\Controllers\Profile\SettingsController;
	use App\Http\Controllers\TaskController;
	use App\Http\Controllers\TeamController;
	use Illuminate\Support\Facades\Route;

	/*
	|--------------------------------------------------------------------------
	| Public & Guest Routes
	|--------------------------------------------------------------------------
	| Διαδρομές προσβάσιμες χωρίς σύνδεση (Login, Password Reset κλπ).
	*/
	Route::middleware('guest')->group(function () {
		Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
		Route::post('login', [LoginController::class, 'login']);
	});

	/*
	|--------------------------------------------------------------------------
	| Localization Route
	|--------------------------------------------------------------------------
	*/
	Route::get('lang/{locale}', function ($locale) {
		session()->put('locale', $locale);
		return redirect()->back();
	})->name('language.switch')->whereIn('locale', Language::values());

	/*
	|--------------------------------------------------------------------------
	| Authenticated Routes
	|--------------------------------------------------------------------------
	| Κεντρικό group για συνδεδεμένους χρήστες.
	| Περιλαμβάνει έλεγχο status χρήστη.
	*/
	Route::middleware(['auth', 'update.user.status'])->group(function () {

		/**
		 * Dashboard & Core Actions
		 */
		Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
		Route::post('logout', [LoginController::class, 'logout'])->name('logout');

		/**
		 * Accounts Management
		 * Διαχείριση πελατών και συνδεδεμένων οντοτήτων (Contacts, Invoices, Notes).
		 */
		Route::resource('accounts', AccountController::class);
		Route::prefix('accounts/{account}')->name('accounts.')->group(function () {
			Route::get('contacts', [AccountController::class, 'getContacts'])->name('contacts');
			Route::get('invoices', [AccountController::class, 'getInvoices'])->name('invoices');

			// Internal Notes logic
			Route::get('notes', [AccountController::class, 'getNotes'])->name('notes');
			Route::post('notes', [AccountController::class, 'storeNote'])->name('notes.store');
		});
		Route::delete('/notes/{note}', [AccountController::class, 'destroyNote'])->name('notes.destroy');

		/**
		 * Contacts & Leads
		 * Διαχείριση επαφών και υποψήφιων πελατών.
		 */
		Route::resource('contacts', ContactController::class);
		Route::resource('leads', LeadController::class);
		Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

		/**
		 * Opportunities
		 * Διαχείριση ευκαιριών πώλησης και κλεισίματος συμφωνιών.
		 */
		Route::resource('opportunities', OpportunityController::class);
		Route::post('opportunities/{opportunity}/mark-won', [OpportunityController::class, 'markWon'])->name('opportunities.mark-won');

		/**
		 * Deals
		 * Η τελική φάση του Sales Pipeline (Won Opportunities).
		 */
		Route::resource('deals', DealController::class);

		/**
		 * Tasks Management
		 */
		Route::resource('tasks', TaskController::class);

		/**
		 * Invoices
		 * Οικονομικά στοιχεία και τιμολόγηση.
		 */
		Route::get('/invoices/add-row', [InvoiceController::class, 'addNewRow'])->name('invoices.add-row');
		Route::resource('invoices', InvoiceController::class);

		Route::prefix('invoices')->name('invoices.')->group(function () {
			// Custom routes για ειδικές λειτουργίες πριν το resource
			Route::get('{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
			Route::patch('{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
		});

		Route::get('/invoices/{invoice}/payments', [PaymentController::class, 'show'])->name('invoices.payments.show');
		Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');

		/******
		 * Payments Section
		 */
		Route::resource('payments', PaymentController::class);
		Route::post('/payments/filter', [PaymentController::class, 'filter'])->name('payments.filter');

		/**
		 * Activities & Audit Logs (AJAX Enabled)
		 * Καταγραφή ενεργειών συστήματος, φιλτράρισμα και εξαγωγές.
		 */
		Route::prefix('activities')->name('activities.')->group(function () {
			Route::get('/', [ActivityController::class, 'index'])->name('index');

			// ΔΙΟΡΘΩΣΗ: Το path είναι σχετικό με το prefix 'activities'
			Route::get('export', [ActivityController::class, 'export'])->name('export');
			Route::post('clear', [ActivityController::class, 'clearOldLogs'])->name('clear')->middleware('role:admin|super-admin');

			Route::post('/', [ActivityController::class, 'store'])->name('store');
			Route::patch('/{activity}/toggle', [ActivityController::class, 'toggleComplete'])->name('toggle');
		});

		Route::middleware(['auth', 'role:admin|super-admin'])->prefix('admin')->group(function () {
			Route::resource('teams', TeamController::class);

			Route::patch('teams/{team}/leader/{user}', [TeamController::class, 'setLeader'])->name('teams.set-leader');

			Route::post('teams/{team}/assign', [TeamController::class, 'assignUser'])->name('teams.assign');
			Route::delete('teams/{team}/users/{user}', [TeamController::class, 'removeUser'])->name('teams.remove-user');

			/**
			 * Security Guardian: Audit Logs
			 * Προβολή ιστορικού αλλαγών στο σύστημα.
			 */
			Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
			Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
		});

		/******************
		 * Notifications Section
		 *****************/
		Route::get('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

		/**
		 * User Profile & System Settings
		 * Προσωπικές ρυθμίσεις, ασφάλεια και προτιμήσεις χρήστη.
		 */
		Route::prefix('profile')->name('profile.')->group(function () {
			Route::get('/', [ProfileController::class, 'show'])->name('show');
			Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
			Route::patch('/', [ProfileController::class, 'update'])->name('update');

			// Settings Sub-group
			Route::prefix('settings')->name('settings.')->group(function () {
				Route::get('/', [SettingsController::class, 'index'])->name('index');
				Route::patch('/security', [SettingsController::class, 'updateSecurity'])->name('security');
				Route::patch('/preferences', [SettingsController::class, 'updatePreferences'])->name('preferences');
				Route::delete('/account', [SettingsController::class, 'destroy'])->name('destroy');
			});
		});
	});