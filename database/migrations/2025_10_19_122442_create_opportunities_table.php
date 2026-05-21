<?php

	use App\Enums\Opportunities\OpportunityStageStatus;
	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('opportunities', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->index();
				$table->decimal('amount', 15)->nullable();
				$table->char('currency', 3)->default('EUR'); // Consistency with Invoices/Payments
				// Η ημερομηνία που "στοχεύει" ο πωλητής να κλείσει το deal
				$table->date('close_date')->nullable()->comment('Η εκτιμώμενη ημερομηνία κλεισίματος της ευκαιρίας');

				// Η πραγματική στιγμή που το status έγινε Won ή Lost
				$table->timestamp('closed_at')->nullable()->comment('Η πραγματική ημερομηνία και ώρα που η ευκαιρία οριστικοποιήθηκε (Won/Lost)');

				// Rely on stage_id for logic, remove the string 'stage' to avoid data mismatch
				$table->unsignedInteger('stage_id')->nullable();
				$table->foreign('stage_id')->references('id')->on('stages')->onDelete('set null');

				$table->unsignedInteger('probability')->default(0);
				$table->string('status')->default(OpportunityStageStatus::OPEN->value)->index();
				$table->string('loss_reason')->nullable()->comment('Why the deal was lost');

				$table->unsignedInteger('account_id')->nullable();
				$table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');

				$table->unsignedInteger('contact_id')->nullable();
				$table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');

				$table->unsignedInteger('owner_id')->nullable();
				$table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');

				$table->text('notes')->nullable();
				$table->boolean('is_active')->default(true);
				$table->softDeletes();
				$table->timestamps();
			});

			Schema::create('opportunities_users', function (Blueprint $table) {
				$table->increments('id');

				// Expanded foreign keys
				$table->unsignedInteger('opportunity_id');
				$table->foreign('opportunity_id')->references('id')->on('opportunities')->onDelete('cascade');

				$table->unsignedInteger('user_id');
				$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

				$table->string('role')->default('member')->comment('Role of the user on this opportunity: owner, collaborator, viewer');

				$table->timestamps();

				// Prevent duplicate assignments
				$table->unique(['opportunity_id', 'user_id']);

				$table->comment('Pivot table: Many-to-many relationship between opportunities and users (team selling).');
			});

			Schema::create('opportunities_items', function (Blueprint $table) {
				$table->increments('id');

				$table->unsignedInteger('opportunity_id');
				$table->foreign('opportunity_id')->references('id')->on('opportunities')->onDelete('cascade');

				$table->unsignedInteger('product_id');
				// 'restrict' is smart here; prevents deleting a product that is linked to an active deal
				$table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');

				$table->unsignedInteger('quantity')->default(1);

				// Financials with consistent precision
				$table->decimal('unit_price', 15);
				$table->decimal('discount', 15)->default(0)->comment('Total discount for this line');
				$table->decimal('tax_rate', 5)->default(0)->comment('Tax percentage (e.g., 24.00 for Greece)');

				// Total is vital here since you aren't using a Model to calculate it on the fly
				$table->decimal('total', 15)->comment('Final calculated total for this item');

				$table->text('notes')->nullable();
				$table->timestamps();

				$table->comment('Line items accessed via $opportunity->items()');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('opportunities');
		}
	};
