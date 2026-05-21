<?php

	namespace Database\Factories;

	use App\Models\Company;
	use App\Models\Team;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;

	/**
	 * @extends Factory<Team>
	 */
	class TeamFactory extends Factory {
		/**
		 * Ορισμός της πρότυπης κατάστασης του Model.
		 *
		 * @return array<string, mixed>
		 */
		public function definition(): array {
			return [
				'name'        => fake()->unique()->jobTitle() . ' Group',
				'description' => fake()->realTextBetween(128, 256),

				// Συνδέουμε την ομάδα με μια τυχαία εταιρεία
				'company_id'  => Company::query()->inRandomOrder()->first()?->id ?? Company::factory(),

				// Ορίζουμε Manager και Leader από τη δεξαμενή των χρηστών
				'manager_id'  => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
				'leader_id'   => User::query()->inRandomOrder()->first()?->id ?? User::factory(),

				'is_active'  => fake()->boolean(90),
				'created_at' => fake()->dateTimeBetween('-6 months'),
				'updated_at' => function (array $attributes) {
					return fake()->dateTimeBetween($attributes['created_at']);
				},
			];
		}

		/**
		 * State για ανενεργές ομάδες.
		 */
		public function inactive(): static {
			return $this->state(fn(array $attributes) => [
				'is_active' => false,
			]);
		}
	}
