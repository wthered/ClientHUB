<?php

    namespace Database\Seeders;

    use App\Enums\Opportunities\OpportunityStage;
    use App\Enums\Opportunities\OpportunityStageStatus;
    use App\Models\Opportunities\Stage;
    use App\Models\Pipeline;
    use Illuminate\Database\Seeder;

    class StagesTableSeeder extends Seeder {
	    public function run(): void {
		    $pipelines = Pipeline::all();

		    if ($pipelines->isEmpty()) {
			    $this->command->warn('⚠️ No pipelines found. Run PipelinesTableSeeder first.');
			    return;
		    }

		    // Αυτά τα ονόματα πρέπει να κάνουν match με τα helper methods στην OpportunityFactory
		    $stageConfigs = [
			    OpportunityStage::DISCOVERY->value    => ['order' => 1, 'status' => OpportunityStageStatus::OPEN],
			    OpportunityStage::PROPOSAL->value     => ['order' => 2, 'status' => OpportunityStageStatus::OPEN],
			    OpportunityStage::NEGOTIATION->value  => ['order' => 3, 'status' => OpportunityStageStatus::OPEN],
			    OpportunityStage::AWAITING_SIG->value => ['order' => 4, 'status' => OpportunityStageStatus::OPEN],
			    OpportunityStage::WON->value          => ['order' => 5, 'status' => OpportunityStageStatus::WON],
			    OpportunityStage::LOST->value         => ['order' => 6, 'status' => OpportunityStageStatus::LOST],
		    ];

		    foreach ($pipelines as $pipeline) {
			    foreach ($stageConfigs as $stageValue => $config) {
				    Stage::factory()->create([
					    'pipeline_id' => $pipeline->id,
					    'name'        => $stageValue,
					    'order'       => $config['order'],
					    'status'      => $config['status']->value,
				    ]);
			    }
		    }

		    $this->command->info('✅ Stages created for all Pipelines.');
	    }
    }
