<?php

	namespace Database\Seeders;

	use Database\Seeders\Activities\ActivitiesTableSeeder;
	use Database\Seeders\Activities\ActivityTypesTableSeeder;
	use Database\Seeders\Tags\TaggableTableSeeder;
	use Database\Seeders\Tags\TagsTableSeeder;
	use Illuminate\Database\Console\Seeds\WithoutModelEvents;
	use Illuminate\Database\Seeder;

	class DatabaseSeeder extends Seeder {
		use WithoutModelEvents;

		/**
		 * Seed the application's database.
		 */
		public function run(): void {
			$this->command->info('🚀 Starting Full CRM Database Seeding...');

			$this->call([
				/*
				|--------------------------------------------------------------------------
				| 1. INFRASTRUCTURE & SETTINGS
				|--------------------------------------------------------------------------
				| Αυτά δεν εξαρτώνται από τίποτα. Είναι το "Setup" του συστήματος.
				*/
				RoleAndPermissionSeeder::class,
				SettingsTableSeeder::class,
				TagsTableSeeder::class,         // Προηγείται για να μπει σε Accounts/Leads
				CustomFieldsTableSeeder::class, // Προηγείται για να γεμίσουν τα metadata

				/*
				|--------------------------------------------------------------------------
				| 2. CORE USERS & TEAMS
				|--------------------------------------------------------------------------
				| Οι "ιδιοκτήτες" των δεδομένων.
				*/
				UsersTableSeeder::class,
				TeamsTableSeeder::class,

				/*
				|--------------------------------------------------------------------------
				| 3. CRM CATALOGS (The Backbone)
				|--------------------------------------------------------------------------
				| Η "ραχοκοκαλιά" των πωλήσεων.
				*/
				PipelinesTableSeeder::class,
				StagesTableSeeder::class,       // Εξαρτάται από Pipelines
				ProductsTableSeeder::class,     // Απαραίτητο για Opportunity Items
				ActivityTypesTableSeeder::class,

				/*
				|--------------------------------------------------------------------------
				| 4. PRIMARY ENTITIES
				|--------------------------------------------------------------------------
				| Οι βασικοί μας "κάδοι" δεδομένων.
				*/
				CompaniesTableSeeder::class,
				AccountsTableSeeder::class,     // Η καρδιά του CRM
				ContactsTableSeeder::class,     // Εξαρτάται από Accounts
				LeadsTableSeeder::class,        // Μπορεί να συνδεθεί με Users/Sources
				DealsTableSeeder::class,

				/*
				|--------------------------------------------------------------------------
				| 5. SUPPORTING ENTITIES (Polymorphic & Metadata)
				|--------------------------------------------------------------------------
				| "Κολλάνε" πάνω στους παραπάνω πίνακες.
				*/
				AddressesTableSeeder::class,
				DocumentsTableSeeder::class,
				FilesTableSeeder::class,
				CommunicationsTableSeeder::class,

				/*
				|--------------------------------------------------------------------------
				| 6. THE SALES ENGINE
				|--------------------------------------------------------------------------
				| Εξαρτάται από: Stages, Products, Accounts, Contacts.
				*/
				OpportunitySeeder::class,

				/*
				|--------------------------------------------------------------------------
				| 7. FINANCIAL MODULE
				|--------------------------------------------------------------------------
				| Το Cash Flow. Εξαρτάται από Accounts & Opportunities.
				*/
				InvoicesTableSeeder::class,
				InvoiceItemsTableSeeder::class,
				PaymentsTableSeeder::class,     // Εξαρτάται από Invoices

				/*
				|--------------------------------------------------------------------------
				| 8. ENGAGEMENT, HISTORY & LOGS
				|--------------------------------------------------------------------------
				| Το καθημερινό workflow και το "μαύρο κουτί".
				*/
				TasksTableSeeder::class,
				ActivitiesTableSeeder::class,
				NotesTableSeeder::class,
				RemindersTableSeeder::class,
				ActivityLogsTableSeeder::class,
				AuditLogsTableSeeder::class,

				/*
				|--------------------------------------------------------------------------
				| 9. FINAL POLYMORPHIC LINKING
				|--------------------------------------------------------------------------
				| Συνδέει τα Tags με Leads, Accounts & Opportunities.
				| Πρέπει να τρέξει τελευταίο για να υπάρχουν ήδη όλες οι εγγραφές.
				*/
				TaggableTableSeeder::class,
			]);

			$this->command->info('✨ CRM Database Seeded Successfully! Ready to fly, baby! ✈️');
		}
	}
