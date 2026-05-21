<?php

	namespace Database\Factories;

	use App\Models\Account;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;

	/**
	 * @extends Factory<Account>
	 */
	class AccountFactory extends Factory {
		protected $model = Account::class;

		public function definition(): array {
			return [
				'name'           => $this->faker->company(),
				'email'          => $this->faker->unique()->companyEmail(),
				'phone'          => $this->faker->phoneNumber(),
				'website'        => $this->faker->url(),
				'address'        => $this->faker->streetAddress(),
				'city'           => $this->faker->city(),
				'state'          => fake()->state(),
				'country'        => 'Greece',
				'postal_code'    => $this->faker->postcode(),
				'industry'       => $this->faker->randomElement(['Technology', 'Healthcare', 'Finance', 'Education', 'Retail', 'Manufacturing']),
				'employee_count' => $this->faker->numberBetween(10, 5000),
				'annual_revenue' => $this->faker->randomFloat(2, 50000, 100000000),
				'is_active'      => true,
				'owner_id'       => User::inRandomOrder()->first()?->id ?? User::factory(),
				'notes'          => $this->faker->sentence(),
			];
		}
	}
