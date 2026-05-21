<?php

	namespace Database\Factories\Users;

	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;
	use Illuminate\Support\Collection;
	use Illuminate\Support\Str;

	/** @extends Factory<User> */
	class UserFactory extends Factory {
		/**
		 * Το μοντέλο που αντιστοιχεί στο Factory.
		 */
		protected $model = User::class;

		/**
		 * Define the model's default state.
		 *
		 * @return array<string, mixed>
		 */
		public function definition(): array {
			$name = fake()->unique()->userName();
			$last_action_time = now()->subDays(mt_rand(1, 7))->subSeconds(mt_rand(0, 24 * 3600));
			$is_locked = fake()->boolean(10);
			$hasEmail = fake()->boolean(90);
			return [
				'name'                  => $name,
				'email'                 => $hasEmail ? $name . '@' . fake()->freeEmailDomain() : null,
				'email_verified_at'     => $hasEmail && fake()->boolean(80) ? now()->subDays(mt_rand(1, 7))->subSeconds(mt_rand(0, 24 * 3600)) : null,
				'password'              => Str::password(mt_rand(8, 12), symbols: false),
				'remember_token'        => fake()->boolean() ? Str::random(32) : null,

				// Status & Security (σύμφωνα με το migration)
				'is_active'             => fake()->boolean(),
				'is_locked'             => $is_locked,
				'lock_reason'           => $is_locked ? $this->createReason() : null,
				'last_login_at'         => fake()->dateTimeBetween('-1 month'),
				'last_login_ip'         => fake()->ipv4(),
				'last_active_at'        => $last_action_time,
				'failed_login_attempts' => $is_locked ? mt_rand(5, 10) : 0,
				'created_at'            => $last_action_time->subHours(mt_rand(0, 23))->subMinutes(mt_rand(0, 59))->subSeconds(mt_rand(0, 59)),
				'updated_at'            => $last_action_time->addSeconds(mt_rand(0, 59)),
			];
		}

		/**
		 * State για κλειδωμένους χρήστες.
		 * Χρήση: User::factory()->locked()->create();
		 */
		public function locked(): static {
			return $this->state(fn(array $attributes) => [
				'is_locked'   => true,
				'lock_reason' => $this->createReason(),
			]);
		}

		/**
		 * State για ανενεργούς χρήστες.
		 */
		public function inactive(): static {
			return $this->state(fn(array $attributes) => [
				'is_active' => false,
			]);
		}

		private function createReason() {
			return Collection::make([
				// --- Security & Compliance ---
				'Suspicious activity detected from an unknown IP address.',
				'Account flagged for potential credential stuffing attack.',
				'Security credentials have expired and require manual reset.',
				'Violation of the organization’s Multi-Factor Authentication (MFA) policy.',

				// --- Administrative & Operational ---
				'Account manually disabled by an Administrator for maintenance.',
				'Scheduled account deactivation due to end of contract/employment.',
				'Pending identity verification (KYC) documentation.',
				'Account archived due to prolonged inactivity (exceeding 90 days).',

				// --- Financial & Billing (Ιδανικό για το CRM σου) ---
				'Subscription payment failed or credit card expired.',
				'Overdue balance on the organization’s account.',
				'Team member limit exceeded for the current subscription tier.',

				// --- Behavioral & Policy ---
				'Account locked due to reported breach of Terms of Service.',
				'Temporary suspension pending an internal investigation.',
				'Access restricted due to unauthorized data export attempt.',
				'Automated flagging for bulk-action abuse (API rate-limiting violation).'
			])->random();
		}

	}
