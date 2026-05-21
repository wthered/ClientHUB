<?php

    namespace Database\Factories;

    use App\Models\Activity;
    use App\Models\Deal;
    use App\Models\User;
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
            return [
                'deal_id'      => Deal::factory(),
                'user_id'      => User::factory(),
                'type'         => $this->faker->randomElement([
                    'call',
                    'meeting',
                    'email',
                    'task'
                ]),
                'subject'      => $this->faker->sentence(4),
                'details'      => $this->faker->paragraph,
                'due_date'     => $this->faker->dateTimeBetween('now', '+15 days'),
                'is_completed' => $this->faker->boolean(50),
            ];
        }
    }
