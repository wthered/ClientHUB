<?php

    namespace Database\Seeders;

    use App\Models\Pipeline;
    use Illuminate\Database\Seeder;

    class PipelinesTableSeeder extends Seeder {
	    public function run(): void {
		    // Δημιουργία των 2 βασικών Pipelines
		    Pipeline::factory()->software()->create();

		    Pipeline::factory()->create([
			    'name' => 'Consulting Sales',
			    'description' => 'Workflow for service-based deals.',
		    ]);

		    $this->command->info('✅ Pipelines (Software & Consulting) created.');
	    }
    }
