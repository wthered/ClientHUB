<?php

	namespace App\Providers;

	use App\Listeners\Authentication\UserAuthHistoryListener;
	use App\Models\Contact;
	use App\Models\Invoices\Invoice;
	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Payment;
	use App\Models\Scopes\LeadScope;
	use App\Models\Tag;
	use App\Observers\ContactObserver;
	use App\Observers\InvoiceObserver;
	use App\Observers\OpportunityObserver;
	use App\Observers\PaymentObserver;
	use App\Observers\TagObserver;
	use Event;
	use Illuminate\Auth\Events\Failed;
	use Illuminate\Auth\Events\Login;
	use Illuminate\Auth\Events\Logout;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Relations\Relation;
	use Illuminate\Support\Facades\Gate;
	use Illuminate\Support\ServiceProvider;

	class AppServiceProvider extends ServiceProvider {
		/**
		 * Register any application services.
		 */
		public function register(): void {
			//
		}

		/**
		 * Bootstrap any application services.
		 */
		public function boot(): void {
			// 1. Model Observers
			Tag::observe(TagObserver::class);
			Contact::observe(ContactObserver::class);
			Invoice::observe(InvoiceObserver::class);
			Payment::observe(PaymentObserver::class);
			Opportunity::observe(OpportunityObserver::class);

			// 2. The "Master Key": Grant Super Admins all permissions
			// Αφαιρέσαμε το dd($user) για να παίζει η εφαρμογή
			Gate::before(function ($user, $ability) {
				return $user->hasAnyRole([
					'admin',
					'super-admin'
				]);
			});

			// 3. Register Lead scope macros
			Builder::macro('lead', function () {
				return new LeadScope();
			});

			// 4. Event Listeners
			Event::listen([
				Login::class,
				Failed::class,
				Logout::class,
			], UserAuthHistoryListener::class);

			// Relationships Builder
			Relation::morphMap([
				'lead'        => Lead::class,
				'opportunity' => Opportunity::class,
			]);
		}
	}
