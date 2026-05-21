<?php

    namespace Database\Seeders;

    use App\Models\Activity;
    use App\Models\Deal;
    use App\Models\User;
    use Illuminate\Database\Seeder;

    class ActivitiesTableSeeder extends Seeder {
        /**
         * Run the database seeds.
         */
        public function run(): void {
            $deals = Deal::all();
            $users = User::all();

            foreach ($deals as $deal) {
                Activity::query()->create([
                    'deal_id'      => $deal->id,
                    'user_id'      => $users->random()->id,
                    'type'         => 'call',
                    'subject'      => 'Follow-up Call',
                    'details'      => 'Call client to discuss next steps',
                    'due_date'     => now()->addDays(2),
                    'is_completed' => false,
                ]);
            }
        }
    }
