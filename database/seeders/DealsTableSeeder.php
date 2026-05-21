<?php

	namespace Database\Seeders;

	use App\Enums\DealStatus;
	use App\Models\Account;
	use App\Models\Deal;
	use App\Models\Lead;
	use App\Models\Opportunities\Stage;
	use App\Models\Pipeline;
	use App\Models\Users\User;
	use Carbon\Carbon;
	use Illuminate\Database\Seeder;

	class DealsTableSeeder extends Seeder {
		public function run(): void {
			$accounts  = Account::with('contacts')->get();
			$leads     = Lead::query()->whereNull('converted_at')->get(); // Μόνο τα ενεργά leads
			$stages    = Stage::all();
			$users     = User::all();
			$pipelines = Pipeline::all();

			// 1. Δημιουργία Deals για Accounts (Existing Customers)
			foreach ($accounts as $account) {
				$contact = $account->contacts->random() ?? null;

				Deal::query()->create([
					'title'               => 'Expansion Deal: ' . $account->name,
					'account_id'          => $account->id,
					'contact_id'          => $contact?->id,
					'lead_id'             => null,
					'pipeline_id'         => $pipelines->random()->id,
					'stage_id'            => $stages->random()->id,
					'user_id'             => $users->random()->id,
					'value'               => rand(5000, 20000),
					'currency'            => 'EUR',
					'status'              => fake()->randomElement(DealStatus::cases())->value,
					'expected_close_date' => Carbon::now()->addDays(mt_rand(10, 60)),
				]);
			}

			// 2. Δημιουργία Deals για Leads (New Business Pipeline)
			// Παίρνουμε μερικά leads για οικονομία
			foreach ($leads->take(mt_rand(12, 16)) as $lead) {
				Deal::query()->create([
					'title'               => 'New Biz: ' . ($lead->company_name ?? $lead->last_name),
					'lead_id'             => $lead->id,
					'account_id'          => null,
					'contact_id'          => null,
					'pipeline_id'         => $pipelines->random()->id,
					'stage_id'            => $stages->random()->id,
					'user_id'             => $lead->owner_id ?? $users->random()->id,
					'value'               => $lead->estimated_value ?? mt_rand(1000, 5000),
					'currency'            => 'EUR',
					'status'              => DealStatus::OPEN->value,
					'expected_close_date' => Carbon::now()->addDays(mt_rand(15, 45)),
				]);
			}
		}
	}
