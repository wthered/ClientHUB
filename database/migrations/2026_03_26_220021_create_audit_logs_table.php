<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('audit_logs', function (Blueprint $table) {
				$table->id();

				// Ποιος έκανε την ενέργεια;
				$table->unsignedInteger('user_id')->nullable()->index();
				$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

				// Τι είδους ενέργεια έγινε; (π.χ. created, updated, deleted, login)
				$table->string('action');

				// Σε ποιο μοντέλο έγινε η ενέργεια; (Polymorphic)
				$table->nullableMorphs('auditable');

				// Λεπτομέρειες (π.χ. τα παλιά και τα νέα values σε JSON)
				$table->json('old_values')->nullable();
				$table->json('new_values')->nullable();

				// Πρόσθετες πληροφορίες (IP Address, User Agent)
				$table->string('ip_address', 45)->nullable();
				$table->text('user_agent')->nullable();

				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('audit_logs');
		}
	};
