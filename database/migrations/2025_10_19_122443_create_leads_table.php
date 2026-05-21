<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('leads', function (Blueprint $table) {
				$table->increments('id');

				// Lead information
				$table->string('first_name');
				$table->string('last_name');
				$table->string('job_title')->nullable(); // Added
				$table->string('company_name')->nullable();
				$table->string('email')->nullable();
				$table->string('website')->nullable();    // Added
				$table->string('phone')->nullable();

				// Lead tracking
				$table->string('source')->default('manual')->index();
				$table->string('status')->default('new')->index(); // Added index for the dashboard
				$table->string('priority')->default('medium');
				$table->decimal('estimated_value', 15)->nullable();

				// Foreign keys
				$table->unsignedInteger('owner_id')->nullable();
				$table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');

				// Conversion tracking
				$table->unsignedInteger('converted_to_contact_id')->nullable();
				$table->foreign('converted_to_contact_id')->references('id')->on('contacts')->onDelete('set null');

				$table->unsignedInteger('converted_to_account_id')->nullable();
				$table->foreign('converted_to_account_id')->references('id')->on('accounts')->onDelete('set null');

				$table->unsignedInteger('converted_to_opportunity_id')->nullable();
				$table->foreign('converted_to_opportunity_id')->references('id')->on('opportunities')->onDelete('set null');

				$table->timestamp('converted_at')->nullable();
				$table->unsignedInteger('converted_by')->nullable();
				$table->foreign('converted_by')->references('id')->on('users')->onDelete('set null');

				$table->timestamp('last_contacted_at')->nullable();
				$table->text('notes')->nullable();
				$table->boolean('is_active')->default(true);

				$table->softDeletes();
				$table->timestamps();

				// Indexes
				$table->index(['status', 'owner_id']);
				$table->index('email');
				$table->index(['first_name', 'last_name']);
				$table->index('company_name');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('leads');
		}
	};
