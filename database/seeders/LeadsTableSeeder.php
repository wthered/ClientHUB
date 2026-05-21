<?php

	namespace Database\Seeders;

	use App\Enums\Leads\LeadPriority;
	use App\Enums\Leads\LeadStatus;
	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Lead;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Users\User;
	use Illuminate\Database\Seeder;

	class LeadsTableSeeder extends Seeder {
		public function run(): void {
			$users = User::all();
			$accounts = Account::all();
			$opportunities = Opportunity::all();
			$contacts = Contact::all();

			if ($users->isEmpty()) {
				$this->command->error('❌ No users found. Run UserSeeder first.');
				return;
			}

			// 1. Δημιουργία 30 "ανοιχτών" Leads (New/Contacted)
			Lead::factory()->count(30)->recycle($users)->recycle($accounts)->recycle($contacts)->recycle($opportunities)->create();

			// 2. Δημιουργία 10 Leads που έγιναν "Qualified" (Υψηλή προτεραιότητα)
			Lead::factory()->count(10)->recycle($users)->recycle($accounts)->recycle($contacts)->recycle($opportunities)->create([
				'status'   => LeadStatus::QUALIFIED->value,
				'priority' => LeadPriority::HIGH->value,
			]);

			// 3. Δημιουργία 5 Leads που μετατράπηκαν σε Accounts/Contacts (The Magic!)
			Lead::factory()->converted()->count(5)->recycle($users)->create();

			// 4. Δημιουργία 5 Leads που χάθηκαν (Lost)
			Lead::factory()->count(5)->recycle($users)->create([
				'status'    => LeadStatus::LOST->value,
				'is_active' => false
			]);

			$this->command->info('✅ Leads database is now full and realistic!');
		}
	}