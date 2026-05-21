<?php

	namespace Database\Factories\Opportunities;

	use App\Enums\Opportunities\OpportunityStage;
	use App\Enums\Opportunities\OpportunityStageStatus;
	use App\Enums\Opportunities\OpportunityUserRole;
	use App\Models\Account;
	use App\Models\Contact;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Opportunities\Stage;
	use App\Models\Product;
	use App\Models\Users\User;
	use Closure;
	use Illuminate\Database\Eloquent\Factories\Factory;

	class OpportunityFactory extends Factory {
		protected $model = Opportunity::class;

		public function definition(): array {
			return [
				'name'        => $this->faker->words(3, true),
				'amount'      => 0,
				'currency'    => 'EUR',
				'close_date'  => $this->faker->dateTimeBetween('first day of January this year', 'yesterday'),

				// Χρησιμοποιούμε Closures. Το Laravel θα τα εκτελέσει ΜΟΝΟ αν
				// δεν περάσεις εσύ κάποιο ID μέσω του recycle() ή του create([])
				'stage_id'    => fn() => Stage::factory(),
				'account_id'  => fn() => Account::factory(),
				'contact_id'  => fn() => Contact::factory(),
				'owner_id'    => fn() => User::factory(),

				'notes'       => $this->faker->optional()->realText(),
				'is_active'   => fake()->boolean(),
			];
		}

		/**
		 * Βελτιωμένο Configure
		 */
		public function configure(): static {
			return $this->afterMaking(function (Opportunity $opportunity) {
				if ($opportunity->stage_id instanceof Closure) {
					$opportunity->stage_id = ($opportunity->stage_id)();
				}

				$stageId = $opportunity->stage_id instanceof Stage ? $opportunity->stage_id->id : $opportunity->stage_id;
				$stage = Stage::query()->find($stageId);

				if ($stage) {
					// Πάρε το status έτοιμο από το Stage (αφού το φτιάξαμε σωστά στο Seeder)
					$opportunity->status = $stage->status;
					$opportunity->probability = $this->getProbability($stage->name);
					$opportunity->is_active = ($opportunity->status->value === OpportunityStageStatus::OPEN->value);

					$opportunity->loss_reason = ($opportunity->status->value === OpportunityStageStatus::LOST->value) ? $this->faker->realText() : null;
				}
			})->afterCreating(function (Opportunity $opportunity) {
				// 1. Δημιουργία Line Items (Προϊόντα)
				$products = Product::all();
				if ($products->isNotEmpty()) {
					$totalAmount = 0;
					$itemsCount = mt_rand(1, 4);

					for ($i = 0; $i < $itemsCount; $i++) {
						$product = $products->random();
						$qty = mt_rand(1, 5);
						$price = $product->price ?? mt_rand(50, 500);
						$subtotal = $qty * $price;

						// Εδώ καλείται η σχέση items() που φτιάξαμε στο Model
						$opportunity->items()->create([
							'product_id' => $product->id,
							'quantity'   => $qty,
							'unit_price' => $price,
							'total'      => $subtotal,
							'tax_rate'   => 24.00,
						]);
						$totalAmount += $subtotal;
					}
					$opportunity->update(['amount' => $totalAmount]);
				}

				// 2. Team Selling (Pivot table)
				$users = User::query()->inRandomOrder()->limit(rand(2, 8))->pluck('id');
				$opportunity->users()->attach($users, ['role' => fake()->randomElement(OpportunityUserRole::cases())->value]);
			});
		}

		private function getProbability(string $name): int {
			return match ($name) {
				'Lead' => mt_rand(5, 20),
				'Proposal' => mt_rand(30, 50),
				'Negotiation' => mt_rand(60, 85),
				'Won' => 100,
				'Lost' => 0,
				default => 50,
			};
		}
	}
