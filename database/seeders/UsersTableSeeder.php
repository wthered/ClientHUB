<?php

	namespace Database\Seeders;

	use App\Enums\Language;
	use App\Models\Users\User;
	use App\Models\Users\UserProfile;
	use Carbon\Carbon;
	use Exception;
	use Illuminate\Database\Eloquent\Collection;
	use Illuminate\Database\Seeder;
	use Illuminate\Http\Client\ConnectionException;
	use Illuminate\Support\Facades\Http;
	use Illuminate\Support\Str;
	use LaravelIdea\Helper\Spatie\Permission\Models\_IH_Role_C;
	use Spatie\Permission\Models\Role;

	class UsersTableSeeder extends Seeder {
		/**
		 * Configuration for the seeder
		 */
		protected array $config = [
			'administrators' => [
				[
					'name'     => 'William Wallace',
					'email'    => 'wthered@gmail.com',
					'password' => 'password',
					'role'     => 'super-admin',
				],
				[
					'name'     => 'Vasilis Souflakos',
					'email'    => 'vsouflakos@infobell.gr',
					'password' => 'password',
					'role'     => 'super-admin',
				],
			],
			'demo_users' => [
				[
					'name'  => 'Νίκος Παπαδόπουλος',
					'email' => 'manager@crm.pliassas.gr',
					'role'  => 'manager'
				],
				[
					'name'  => 'Γιώργος Αντωνίου',
					'email' => 'sales@crm.pliassas.gr',
					'role'  => 'sales'
				],
				[
					'name'  => 'Μαρία Λουκά',
					'email' => 'support@crm.pliassas.gr',
					'role'  => 'support'
				],
				[
					'name'  => 'Κώστας Δημητρίου',
					'email' => 'viewer@crm.pliassas.gr',
					'role'  => 'viewer'
				],
			],
			'mockaroo'               => [
				'enabled'     => true,
				'url'         => 'https://my.api.mockaroo.com/blog_users.json',
				'api_key_env' => 'mockaroo.key',
				'timeout'     => 10,
				'max_users'   => 512,
			],
			'fallback_users'         => 20,
			'random_password_length' => [
				8,
				12
			],
		];

		/**
		 * Run the database seeds.
		 */
		public function run(): void {
			$this->command->info('🚀 Starting UsersTableSeeder with Profiles...');

			// 1. Administrators
			$this->createAdministrators();

			// 2. Demo Users
			$this->createDemoUsers();

			// 3. API or Fallback
			if ($this->config['mockaroo']['enabled']) {
				$this->createUsersFromApi();
			} else {
				$this->createFallbackUsers();
			}

			// 4. Factories (Προσοχή: Χρειάζεται και το UserProfileFactory)
			// This ensures the profile is created
			User::factory()->count(32)->has(UserProfile::factory(), 'profile')->create();

			$this->config['admin']['password'] = Str::password(length: mt_rand(12, 16), symbols: false);
			$this->command->warn('⚠️  IMPORTANT: Initial Admin Password is: ' . $this->config['admin']['password']);

			User::query()->where('updated_at', '<', 'created_at')->get()->each(function ($user) {
				$user->update([
					'updated_at' => $user->created_at->addSeconds(mt_rand(0, 24 * 3600))
				]);
			});

			$this->displayStatistics();
		}

		/**
		 * Create the admin user
		 */
		/**
		 * Create the admin user and their profile
		 */
		protected function createAdministrators(): void {
			$administrators = $this->config['administrators'];

			foreach ($administrators as $adminConfig) {
				$adminConfig['last_login_at'] = now()->subSeconds(mt_rand(0, 24 * 3600));

				$admin = User::query()->firstOrCreate(['email' => $adminConfig['email']], [
					'name'              => $adminConfig['name'],
					'password'          => bcrypt($adminConfig['password']),
					'email_verified_at' => fake()->dateTimeBetween('-2 months', 'today'),
					'last_login_at'     => $adminConfig['last_login_at'],
					'last_login_ip'     => fake()->ipv4(),
					'is_active'         => true,
					'last_active_at'    => $adminConfig['last_login_at']->addSeconds(mt_rand(0, 5 * 60)),
					'created_at'        => $adminConfig['last_login_at']->copy()->subSeconds(mt_rand(0, 24 * 3600)),
					'updated_at'        => $this->randomPastTimestamp(),
				]);

				$nameParts = explode(' ', $adminConfig['name'], 2);

				$admin->profile()->updateOrCreate(['user_id' => $admin->id], [
					'first_name' => $nameParts[0],
					'last_name'  => $nameParts[1] ?? '',
					'avatar'     => "https://robohash.org/" . md5($adminConfig['email']) . ".png?size=256x256&set=set" . mt_rand(1, 5),
					'position'   => 'System Administrator',
					'phone'      => fake()->regexify('2[1-8][0-9]{8}'),
					'notify_on_report' => fake()->boolean(),
				]);

				$admin->settings()->updateOrCreate(['user_id' => $admin->id], [
					'stagnant_report_enabled' => fake()->boolean(),
					'daily_pulse_enabled'     => fake()->boolean(),
					'notify_on_sales'         => fake()->boolean(),
					'language'                => fake()->randomElement(Language::cases())->value,
					'timezone'                => fake()->timezone(),
					'theme'                   => fake()->randomElement(['light', 'dark']),
				]);

				$role = Role::where('name', $adminConfig['role'])->first();
				if ($role && !$admin->hasRole($role)) {
					$admin->assignRole($role);
				}

				$this->command->info("✅ Administrator created: {$adminConfig['email']} ({$adminConfig['role']})");
			}
		}

		/**
		 * Create demo users with different roles
		 */
		/**
		 * Create demo users with different roles and their associated profiles
		 */
		protected function createDemoUsers(): void {
			foreach ($this->config['demo_users'] as $demoUser) {
				$demo_time = today()->subDays(mt_rand(0, 30))->setHours(mt_rand(0, 23))->setMinutes(mt_rand(0, 59))->setSeconds(mt_rand(0, 59));

				// 1. Δημιουργία User (Security/Account)
				$user = User::query()->firstOrCreate(['email' => $demoUser['email']], [
					'name'              => $demoUser['name'],
					'password'          => Str::password(mt_rand(8, 12), symbols: false),
					'is_active'         => true,
					'is_locked'         => false,
					'last_active_at'    => now()->subSeconds(mt_rand(0, 24 * 3600)),
					'last_login_at'     => now()->subSeconds(mt_rand(1, 24 * 3600)),
					'last_login_ip'     => fake()->ipv4(),
					'email_verified_at' => $demo_time,
					'remember_token'    => fake()->boolean() ? Str::random(32) : null,
					'created_at'        => $demo_time->subSeconds(mt_rand(0, 24 * 3600)),
					'updated_at'        => $demo_time->addSeconds(mt_rand(0, 24 * 3600)),
				]);

				// 2. Διαχωρισμός ονόματος
				$nameParts = explode(' ', $demoUser['name'], 2);

				// 3. Δημιουργία UserProfile (Identity)
				$user->profile()->updateOrCreate(['user_id' => $user->id], [
					'first_name' => $nameParts[0],
					'last_name'  => $nameParts[1] ?? '',
					'avatar'     => 'https://robohash.org/' . md5($demoUser['email']) . '.png?size=256x256&set=set' . mt_rand(1, 5),
					'phone'      => fake()->regexify('2[1-8][0-9]{8}'),
					'position'   => Str::title($demoUser['role']) . ' at CRM',
					'bio'        => "Υπεύθυνος για το τμήμα " . $demoUser['role'] . " του CRM.",
					'notify_on_report'=> fake()->boolean(),
				]);

				// 4. Δημιουργία ρυθμίσεων χρήστη
				$user->settings()->updateOrCreate(['user_id' => $user->id], [
					'stagnant_report_enabled' => fake()->boolean(),
					'daily_pulse_enabled'     => fake()->boolean(),
					'notify_on_sales'         => fake()->boolean(),
					'language'                => fake()->randomElement(Language::cases())->value,
					'timezone'                => fake()->timezone(),
					'theme'                   => fake()->randomElement(['light', 'dark']),
				]);

				// 5. Ανάθεση Ρόλου (Spatie)
				$role = Role::where('name', $demoUser['role'])->first();
				if ($role && !$user->hasRole($role)) {
					$user->assignRole($role);
					$this->command->info("✅ Demo user created: {$demoUser['email']} / Role: {$demoUser['role']}");
				}
			}
		}

		/**
		 * Create users from Mockaroo API data
		 */
		protected function createUsersFromApi(): void {
			try {
				$userData = $this->fetchUsersFromApi();

				if (empty($userData)) {
					$this->command->warn('⚠️ Mockaroo API returned no data. Using fallback users.');
					$this->createFallbackUsers();
					return;
				}

				$availableRoles = $this->getAvailableRoles();
				$createdCount   = 0;

				foreach ($userData as $index => $user) {
					if ($createdCount >= min($this->config['mockaroo']['max_users'], count($userData))) {
						break;
					}

					// Κλήση της μεθόδου που κάνει το "μοίρασμα" των δεδομένων
					$this->createUserFromApiData($user, $index, $availableRoles);
					$createdCount++;
				}

				$this->command->info("📊 Created " . $createdCount . " users and profiles from Mockaroo API.");

			} catch (Exception $e) {
				$this->command->error('❌ Failed to fetch from Mockaroo API: ' . $e->getMessage());
				$this->createFallbackUsers();
			}
		}

		/**
		 * Fetch users from Mockaroo API
		 *
		 * @throws ConnectionException
		 */
		protected function fetchUsersFromApi(): array {
			$apiKey = config('app.' . $this->config['mockaroo']['api_key_env']);

			if (!$apiKey) {
				$this->command->warn('⚠️ Mockaroo API key not configured.');
				return [];
			}

			$response = Http::timeout($this->config['mockaroo']['timeout'])
				->withHeaders(['X-API-KEY' => $apiKey])
				->get($this->config['mockaroo']['url']);

			return $response->successful() ? $response->json() : [];
		}

		/**
		 * Create fallback users when API fails
		 */
		protected function createFallbackUsers(): void {
			$availableRoles = $this->getAvailableRoles();
			$firstNames     = [
				'John',
				'Jane',
				'Michael',
				'Sarah',
				'David',
				'Emma',
				'James',
				'Maria',
				'Robert',
				'Lisa'
			];
			$lastNames      = [
				'Smith',
				'Johnson',
				'Williams',
				'Brown',
				'Jones',
				'Garcia',
				'Miller',
				'Davis',
				'Rodriguez',
				'Martinez'
			];

			for ($i = 0; $i < $this->config['fallback_users']; $i++) {
				$fName = $firstNames[$i % count($firstNames)];
				$lName = $lastNames[$i % count($lastNames)];
				$email = strtolower($fName . '.' . $lName . mt_rand($i, 1024) . '@' . fake()->freeEmailDomain());

				$user = User::query()->create([
					'name'      => fake()->userName(),
					'email'     => $email,
					'password'  => $this->generateRandomPassword(),
					'is_active' => true,
				]);

				$user->profile()->create([
					'first_name' => $fName,
					'last_name'  => $lName,
					'avatar'     => "https://robohash.org/" . md5($email) . ".png?set=set" . mt_rand(1, 5),
					'position'   => 'External Partner',
					'phone'      => fake()->regexify('2[1-8][0-9]{8}'),
					'bio'        => fake()->paragraph(mt_rand(2, 6)),
					'notify_on_report'=> fake()->boolean(),
				]);

				$user->settings()->updateOrCreate(['user_id' => $user->id], [
					'stagnant_report_enabled' => fake()->boolean(),
					'daily_pulse_enabled'     => fake()->boolean(),
					'notify_on_sales' => fake()->boolean(),
					'language' => fake()->randomElement(Language::cases())->value,
					'timezone' => fake()->timezone(),
					'theme' => fake()->randomElement(['en', 'el']),
				]);

				// Assign role if available
				if ($availableRoles && $availableRoles->isNotEmpty()) {
					$role = $availableRoles[$i % $availableRoles->count()];
					$user->assignRole($role);
				}
			}

			$this->command->info("📊 Created {$this->config['fallback_users']} fallback users.");
		}

		/**
		 * Get available roles (excluding super-admin for regular users)
		 */
		protected function getAvailableRoles(): _IH_Role_C|Collection {
			return Role::whereNotIn('name', ['super-admin'])->get();
		}

		/**
		 * Generate a random password
		 */
		protected function generateRandomPassword(): string {
			$length = mt_rand($this->config['random_password_length'][0], $this->config['random_password_length'][1]);
			return Str::password($length, symbols: false);
		}

		/**
		 * Create a single user AND their profile from API data
		 */
		protected function createUserFromApiData(array $userData, int $index, $availableRoles): void {
			// Χρησιμοποιούμε το username από το API ή παράγουμε ένα μοναδικό
			$username = $userData['username'] ?? $this->generateUniqueUsername();
			$email    = $userData['email'] ?? $this->generateEmail($username);
			$isActive = $userData['is_active'] ?? true;

			// 1. Δημιουργία User (Security/Account)
			$user = User::query()->create([
				'name'              => $username,
				'email'             => $email,
				'password'          => $userData['password'] ?? $this->generateRandomPassword(),
				'is_active'         => $isActive,
				'last_active_at'    => Carbon::now()->subSeconds(mt_rand(0, 24 * 3600))->toDateTimeString(),
				'last_login_at'     => $this->parseDate($userData['created_at'] ?? null),
				'last_login_ip'     => fake()->ipv4(),
				'email_verified_at' => $userData['verified_at'] ?? ($isActive ? now() : null),
				'remember_token'    => fake()->boolean() ? $userData['remember_token'] : null,
				'created_at'        => $this->parseDate($userData['created_at'] ?? null),
				'updated_at'        => $this->parseDate($userData['updated_at'] ?? null),
			]);

			// 2. Δημιουργία UserProfile (Identity) - Mapping από Mockaroo
			$user->profile()->create([
				'first_name'      => $userData['first_name'] ?? fake()->firstName(),
				'last_name'       => $userData['surname'] ?? fake()->lastName(),
				'avatar'          => $userData['avatar'] ? "https://robohash.org/" . $userData['salt'] . ".png?set=set" . mt_rand(1, 5) : null,
				'phone'           => fake()->optional()->regexify('2[1-8][0-9]{8}'),
				'position'        => $userData['company'] ?? fake()->jobTitle(),
				'bio'             => $userData['profile'] ?? fake()->sentence(10),
				'notify_on_report'=> fake()->boolean(),
			]);

			$user->settings()->updateOrCreate(['user_id' => $user->id], [
				'stagnant_report_enabled' => fake()->boolean(),
				'daily_pulse_enabled'     => fake()->boolean(),
				'notify_on_sales' => fake()->boolean(),
				'language' => fake()->randomElement(Language::cases())->value,
				'timezone' => fake()->timezone(),
				'theme' => fake()->randomElement(['en', 'el']),
			]);

			// 3. Ρόλοι
			if ($availableRoles && $availableRoles->isNotEmpty()) {
				$role = $availableRoles[$index % $availableRoles->count()];
				$user->assignRole($role);
			}
		}

		/**
		 * Generate a unique username
		 */
		protected function generateUniqueUsername(): string {
			do {
				$username = Str::lower(fake()->userName() . Str::random(4));
			} while (User::query()->where('name', $username)->exists());
			return $username;
		}

		/**
		 * Generate email from username
		 */
		protected function generateEmail(string $username): string {
			$domains = [
				// --- Global Tech Giants & Email Providers ---
				'gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'icloud.com',
				'proton.me', 'protonmail.com', 'me.com', 'live.com', 'msn.com',
				'aol.com', 'mail.com', 'gmx.com', 'zoho.com', 'zoho.eu',
				'yandex.com', 'fastmail.com', 'tutanota.com',

				// --- Big Tech & Infrastructure ---
				'microsoft.com', 'apple.com', 'google.gr', 'meta.com', 'amazon.co.uk',
				'oracle.com', 'salesforce.com', 'sap.com', 'ibm.com', 'intel.com',
				'github.com', 'gitlab.com', 'vercel.app', 'netlify.com', 'digitalocean.com',
				'stripe.com', 'openai.com', 'spotify.com',

				// --- Modern Apps & SaaS Tools ---
				'slack.com', 'discord.com', 'airbnb.com', 'uber.com', 'revolut.com',
				'binance.com', 'coinbase.com', 'canva.com', 'figma.com', 'notion.so',
				'monday.com', 'asana.com', 'trello.com', 'zoom.us', 'webex.com',
				'hubspot.com', 'mailchimp.com', 'shopify.com', 'magento.com',
				'wordpress.com', 'medium.com', 'substack.com',

				// --- Greek Corporate & Banking ---
				'ote.gr', 'cosmote.gr', 'wind.gr', 'vodafone.gr', 'piraeusbank.gr',
				'nbg.gr', 'eurobank.gr', 'alpha.gr', 'skroutz.gr', 'e-food.gr',
				'box.gr', 'aegeanair.com',

				// --- Greek ISP & Legacy Entities ---
				'forthnet.gr', 'hol.gr', 'otenet.gr', 'vivodi.gr', 'upnet.gr',
				'hellasnet.gr', 'on.gr',

				// --- Media & News Agencies ---
				'kathimerini.gr', 'protothema.gr', 'reuters.com', 'bloomberg.com',
				'forbes.com', 'wsj.com', 'ft.com', 'nytimes.com', 'bbc.co.uk',
				'cnn.com', 'aljazeera.com', 'dw.com',

				// --- Government & International Organisations ---
				'gov.gr', 'unicef.org', 'who.int', 'unesco.org', 'nasa.gov', 'cern.ch',

				// --- Academic Institutions (Hellenic & Global) ---
				'auth.gr', 'uoa.gr', 'unipi.gr', 'ntua.gr', 'eie.gr',
				'mit.edu', 'stanford.edu', 'harvard.edu', 'ox.ac.uk', 'cam.ac.uk',
			];
			// Χρήση array_random για ταχύτητα ή collect()->random()
			$domain = collect($domains)->unique()->random();
			return Str::lower($username . '@' . $domain);
		}

		/**
		 * Parse date string or return random date
		 */
		protected function parseDate(?string $dateString): string {
			if ($dateString) {
				return Carbon::parse($dateString)->timezone(config('app.timezone', 'Europe/Athens'))->toDateTimeString();
			}
			return $this->randomDateBetween('-1 year');
		}

		/**
		 * Generate random date between two dates
		 */
		protected function randomDateBetween(string $start, string $end = 'now'): string {
			return fake()->dateTimeBetween($start, $end)->format('Y-m-d H:i:s');
		}

		/**
		 * Display statistics after seeding
		 */
		protected function displayStatistics(): void {
			$totalUsers  = User::query()->count();
			$activeUsers = User::query()->where('is_active', true)->count();

			$this->command->info('📊 User Statistics:');
			$this->command->info("   ┌─────────────────────────────────────────┐");
			$this->command->info("   │ Total users:     " . $totalUsers);
			$this->command->info("   │ Active users:    " . $activeUsers);
			$this->command->info("   │ Inactive users:  " . ($totalUsers - $activeUsers));
			$this->command->info("   └─────────────────────────────────────────┘");

			// Display role distribution
			$roles = Role::withCount('users')->get();
			if ($roles->isNotEmpty()) {
				$this->command->info('📊 Role Distribution:');
				foreach ($roles as $role) {
					$this->command->info("   ├── " . $role->name . ": " . $role->users_count . " users");
				}
			}

			foreach ($this->config['administrators'] as $administrator) {
				$myself = User::query()->where('email', $administrator['email'])->first();
				$myself->update([
					'password' => $administrator['password'],
				]);
			}
		}

		/**
		 * Generate a random past timestamp
		 */
		protected function randomPastTimestamp(): string {
			return Carbon::now()
				->subHours(mt_rand(0, 23))
				->subMinutes(mt_rand(0, 59))
				->subSeconds(mt_rand(0, 59))
				->toDateTimeString();
		}
	}