<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('addresses', function (Blueprint $table) {
				$table->increments('id');
				// Polymorphic relationship (μπορεί να ανήκει σε Account, Contact, Lead, κλπ.)
				$table->unsignedInteger('addressable_id');
				$table->string('addressable_type');
				$table->index([
					'addressable_type',
					'addressable_id'
				]);

				$table->string('type')->default('billing')->comment('billing, shipping, office, home, other');

				$table->text('address_line1');
				$table->text('address_line2')->nullable();

				$table->string('city');
				$table->string('state')->nullable();
				$table->string('postal_code')->nullable();
				$table->string('country');

				$table->string('phone')->nullable();
				$table->string('email')->nullable();

				$table->boolean('is_primary')->default(false)->comment('Εάν είναι η κύρια διεύθυνση');

				// Expanded foreign key (προαιρετικά - ποιος την δημιούργησε)
				$table->unsignedInteger('created_by')->nullable();
				$table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

				$table->text('notes')->nullable();

				$table->boolean('is_active')->default(true);

				$table->softDeletes();
				$table->timestamps();

				$table->comment('Polymorphic addresses table. Μπορεί να χρησιμοποιηθεί για Accounts, Contacts και άλλα μοντέλα.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('addresses');
		}
	};
