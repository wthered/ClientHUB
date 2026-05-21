<?php

	namespace Database\Factories;

	use App\Models\Company;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;

	/**
	 * @extends Factory<Company>
	 */
	class CompanyFactory extends Factory {
		/**
		 * Define the model's default state.
		 *
		 * @return array<string, mixed>
		 */
		public function definition(): array {
			return [
				'name' => fake()->company(),
				'legal_name' => fake()->company() . ' ' . fake()->companySuffix(),
				'vat_number' => 'EL' . fake()->unique()->numerify('#########'),
				'industry' => fake()->randomElement([
					'Technology',
					'Manufacturing',
					'Shipping',
					'Retail',
					'Energy'
				]),
				'website' => fake()->url(),
				'email' => fake()->companyEmail(),
				'phone' => fake()->phoneNumber(),
				'owner_id' => User::query()->inRandomOrder()->first()->id ?? 1,
				'created_at' => fake()->dateTimeBetween('-1 year'),
				'updated_at' => function (array $attributes) {
					return fake()->dateTimeBetween($attributes['created_at']);
				},
			];
		}
	}
