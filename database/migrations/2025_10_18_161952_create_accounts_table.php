<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('accounts', function (Blueprint $table) {
				$table->increments('id');

				$table->string('name')->comment('Company or organization name');
				$table->string('email')->nullable()->comment('Primary contact email for the account');
				$table->string('phone')->nullable()->comment('Primary contact phone number');
				$table->string('website')->nullable()->comment('Company website URL');

				$table->text('address')->nullable()->comment('Physical or billing address');
				$table->string('city')->nullable();
				$table->string('state')->nullable();
				$table->string('country')->nullable();
				$table->string('postal_code')->nullable();

				$table->string('industry')->nullable()->comment('Industry sector (e.g., Technology, Healthcare)');
				$table->integer('employee_count')->nullable()->comment('Approximate number of employees');
				$table->decimal('annual_revenue', 15, 2)->nullable()->comment('Annual revenue in base currency');

				$table->boolean('is_active')->default(true)->comment('Indicates if the account is currently active');

				// Expanded foreign key syntax (unsigned integer)
				$table->unsignedInteger('owner_id')->nullable()->comment('User who owns/manages this account');
				$table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');

				$table->text('notes')->nullable()->comment('Additional notes about the account');

				$table->timestamps();
				$table->softDeletes();

				// Table-level comment
				$table->comment('Stores company/organization accounts (clients) in the CRM. One account can have multiple contacts.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('accounts');
		}
	};
