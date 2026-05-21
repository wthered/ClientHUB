<?php

	namespace Database\Seeders\Tags;

	use App\Models\Account;
	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Scopes\AccountScope;
	use App\Models\Scopes\LeadScope;
	use App\Models\Scopes\OpportunityScope;
	use App\Models\Tag;
	use Illuminate\Database\Seeder;

	class TaggableTableSeeder extends Seeder {
		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$tags = Tag::all();

			if ($tags->isEmpty()) {
				$this->command->warn('⚠️ No tags found. Please run TagsTableSeeder first.');
				return;
			}

			// Λίστα με τα μοντέλα που δέχονται tags
			// Χρησιμοποιούμε withoutGlobalScope για να "πιάσουμε" και τα converted/inactive records
			$targets = [
				'Leads'         => Lead::query()->withoutGlobalScope(LeadScope::class)->get(),
				'Accounts'      => Account::query()->withoutGlobalScope(AccountScope::class)->get(),
				'Opportunities' => Opportunity::query()->withoutGlobalScope(OpportunityScope::class)->get(),
			];

			foreach ($targets as $label => $records) {
				if ($records->isEmpty()) {
					$this->command->info("ℹ️ No $label found to tag.");
					continue;
				}

				$this->command->info("🏷️  Attaching tags to " . $records->count() . " $label...");

				$records->each(function ($record) use ($tags) {
					// Επιλογή 1 έως 3 τυχαίων tags
					$randomTags = $tags->random(mt_rand(1, 4))->pluck('id');

					// Χρήση syncWithoutDetaching για αποφυγή duplicates
					$record->tags()->syncWithoutDetaching($randomTags);
				});
			}
		}
	}