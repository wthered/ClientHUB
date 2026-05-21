<?php

	namespace Database\Factories;

	use App\Enums\Leads\LeadPriority;
	use App\Enums\Leads\LeadStatus;
	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;

	/**
	 * @extends Factory<Lead>
	 */
	class LeadFactory extends Factory {
		protected $model = Lead::class;

		public function definition(): array {
			// ΜΗΝ βάζεις logic για $converted εδώ.
			// Το definition πρέπει να αντιπροσωπεύει ένα απλό, "φρέσκο" Lead.

			return [
				'first_name'   => $this->faker->firstName,
				'last_name'    => $this->faker->lastName,
				'company_name' => $this->faker->company,
				'email'        => $this->faker->unique()->safeEmail,
				'phone'        => $this->faker->phoneNumber,
				'source'       => $this->faker->randomElement(['Website', 'Referral', 'LinkedIn', 'Facebook', 'Google', 'Conference']),
				'status'       => $this->faker->randomElement(LeadStatus::cases()),
				'priority'     => $this->faker->randomElement(LeadPriority::cases()),

				// Χρησιμοποιούμε Factories για να λειτουργήσει το recycle($users), recycle($accounts) κλπ.
				'owner_id'                    => User::factory(),
				'converted_by'                => null,
				'converted_at'                => null,
				'converted_to_contact_id'     => null,
				'converted_to_account_id'     => null,
				'converted_to_opportunity_id' => null,

				'last_contacted_at' => null,
				'estimated_value'   => $this->faker->randomFloat(2, 1000, 50000),
				'notes'             => $this->faker->optional()->sentence(),
				'is_active'         => true,
				'created_at'        => $this->faker->dateTimeBetween('-6 months'),
			];
		}

		/**
		 * State για Lead που έχει ήδη μετατραπεί σε Πελάτη.
		 */
		public function converted(): static {
			return $this->state(function (array $attributes) {
				return [
					'status'       => LeadStatus::QUALIFIED->value,
					'is_active'    => false,
					'converted_at' => now()->subDays(mt_rand(1, 30))->setHours(mt_rand(0, 23))->setMinutes(mt_rand(0, 59))->setSeconds(mt_rand(0, 59)),
					// Εδώ ζητάμε Factory. Το recycle($users) θα βάλει έναν από τους υπάρχοντες.
					'converted_by' => User::factory(),
				];
			})->afterCreating(function (Lead $lead) {
				// Εδώ δημιουργούμε/συνδέουμε τα ρεαλιστικά δεδομένα
				$account = Account::factory()->create([
					'name'     => $lead->company_name ?? ($lead->last_name . ' Ltd'),
					'owner_id' => $lead->owner_id,
				]);

				$contact = Contact::factory()->create([
					'account_id' => $account->id,
					'first_name' => $lead->first_name,
					'last_name'  => $lead->last_name,
					'email'      => $lead->email,
					'owner_id'   => $lead->owner_id,
				]);

				$lead->updateQuietly([
					'converted_to_account_id' => $account->id,
					'converted_to_contact_id' => $contact->id,
				]);
			});
		}
	}
