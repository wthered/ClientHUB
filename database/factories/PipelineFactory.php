<?php

    namespace Database\Factories;

    use App\Models\Pipeline;
    use Illuminate\Database\Eloquent\Factories\Factory;

    /**
     * @extends Factory<Pipeline>
     */
    class PipelineFactory extends Factory {
	    protected $model = Pipeline::class;

	    public function definition(): array {
		    return [
			    'name'        => $this->faker->unique()->words(2, true),
			    'description' => $this->faker->sentence(),
		    ];
	    }

	    // Προκαθορισμένα Pipelines για το Demo
	    public function software(): static {
		    return $this->state(fn () => [
			    'name' => 'Software Sales',
			    'description' => 'Standard workflow for SaaS and licenses.',
		    ]);
	    }
    }
