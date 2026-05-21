<?php

	namespace Database\Factories;

	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Users\User;
	use App\Support\GreekNameGenerator;
	use Illuminate\Database\Eloquent\Factories\Factory;

	/**
	 * @extends Factory<Contact>
	 */
	class ContactFactory extends Factory {
		protected $model = Contact::class;

		public function definition(): array {
			$user = GreekNameGenerator::generate();

			return [
				'account_id' => Account::factory(),
				'first_name' => $user['first_name'],
				'last_name'  => $user['last_name'],
				'email'      => $user['email'],

				'phone'     => $this->faker->optional()->regexify('^(2\d{9}|69\d{8})$'),
				'job_title' => $user['job_title'],

				'address' => $this->faker->streetAddress,
				'city'    => $this->faker->city,
				'country' => $this->faker->country,

				'notes'      => $this->faker->optional()->paragraph(2),
				'is_primary' => false,
				'owner_id'   => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
			];
		}


		/**
		 * Αυτή είναι η μέθοδος που έλειπε!
		 * Ορίζει την κατάσταση "primary" για το contact.
		 */
		public function primary(): static {
			return $this->state(fn(array $attributes) => [
				'is_primary' => true,
			]);
		}
	}
