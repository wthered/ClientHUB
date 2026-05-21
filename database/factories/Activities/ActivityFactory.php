<?php

	namespace Database\Factories\Activities;

	use App\Models\Activities\Activity;
	use App\Models\Activities\ActivityType;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\Factory;

	/**
	 * @extends Factory<Activity>
	 */
	class ActivityFactory extends Factory {
		/**
		 * Define the model's default state.
		 *
		 * @return array<string, mixed>
		 */
		public function definition(): array {
			$type        = $this->faker->randomElement([
				'call',
				'meeting',
				'email',
				'task',
				'demo'
			]);
			$isCompleted = $this->faker->boolean(40);

			return [
				'type'             => $type,
				'subject'          => ucfirst($type) . ": " . $this->faker->sentence(3),
				'description'      => $this->faker->sentence(10),
				'content'          => $type === 'email' ? $this->faker->paragraphs(2, true) : $this->faker->sentence(),
				'direction'        => in_array($type, [
					'call',
					'email'
				]) ? $this->faker->randomElement([
					'inbound',
					'outbound'
				]) : 'outbound',
				'priority'         => $this->faker->randomElement([
					'low',
					'medium',
					'high'
				]),
				'due_at'           => $this->faker->dateTimeBetween('-1 month', '+1 month'),
				'completed_at'     => $isCompleted ? now() : null,
				'status'           => $isCompleted ? 'completed' : 'pending',
				'is_completed'     => $isCompleted,
				'owner_id'         => User::inRandomOrder()->first()?->id ?? User::factory(),
				'activity_type_id' => ActivityType::query()->where('slug', $type)->first()?->id ?? 1,
				'is_active'        => true,
			];
		}
	}
