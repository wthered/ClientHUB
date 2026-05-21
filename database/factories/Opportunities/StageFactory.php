<?php

    namespace Database\Factories\Opportunities;

    use App\Models\Opportunities\Stage;
    use App\Models\Pipeline;
    use Illuminate\Database\Eloquent\Factories\Factory;

    /**
     * @extends Factory<Stage>
     */
    class StageFactory extends Factory {
        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition(): array {
            return [
                'pipeline_id' => Pipeline::factory(),
                'name'        => $this->faker->word,
                'order'       => $this->faker->numberBetween(1, 5),
            ];
        }
    }
