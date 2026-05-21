<?php

	namespace Database\Factories;

	use App\Models\Lead;
	use App\Models\Task;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;
	use Illuminate\Support\Carbon;

	/** @extends Factory<Task> */
	class TaskFactory extends Factory {
		protected $model = Task::class;

		public function definition(): array {
			return [
				'subject'       => $this->faker->sentence(3),
				'description'   => $this->faker->paragraph(),
				'status'        => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'deferred']),
				'priority'      => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),

				'due_date'      => $this->faker->dateTimeBetween('now', '+1 month'),
				'completed_at'  => null, // Συνήθως ένα νέο task δεν είναι ολοκληρωμένο

				// ΣΥΝΔΕΣΗ ΜΕ ΠΡΑΓΜΑΤΙΚΟ ΧΡΗΣΤΗ
				'user_id'       => User::inRandomOrder()->first()?->id ?? User::factory(),

				// POLYMORPHIC LINK (Default σε ένα Lead, αλλά αλλάζει στον Seeder)
				'taskable_id'   => Lead::inRandomOrder()->first()?->id ?? Lead::factory(),
				'taskable_type' => Lead::class,

				'created_at'    => Carbon::now(),
				'updated_at'    => Carbon::now(),
			];
		}
	}
