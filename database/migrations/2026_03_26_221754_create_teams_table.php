<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			// 1. Teams Table
			Schema::create('teams', function (Blueprint $table) {
				$table->increments('id');

				// Βασικά Στοιχεία
				$table->string('name');
				$table->string('description')->nullable();

				// Η Κορυφή: Σύνδεση με Εταιρεία (Αν υπάρχει πίνακας companies)
				$table->unsignedInteger('company_id')->nullable();
				$table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

				// Ο Επόπτης (Manager): High-level supervisor
				$table->unsignedInteger('manager_id')->nullable()->comment('The big boss overseeing the team');
				$table->foreign('manager_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

				// Ο Αρχηγός (Leader): Operational head, συνήθως μέλος της ομάδας
				$table->unsignedInteger('leader_id')->nullable()->comment('The team leader/captain');
				$table->foreign('leader_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

				// Κατάσταση
				$table->boolean('is_active')->default(true);

				// Timestamps & Soft Deletes
				$table->timestamps();
				$table->softDeletes();

				$table->comment('Teams organized by Company, with dedicated Manager and Leader roles.');
			});

			// 2. Team User Pivot Table (The "Pyramid" Base)
			Schema::create('team_user', function (Blueprint $table) {
				$table->id();

				$table->unsignedInteger('team_id');
				$table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');

				$table->unsignedInteger('user_id');
				$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

				// Ρόλος εντός της ομάδας
				$table->string('role')->default('member')->comment('member, admin, viewer, etc.');

				$table->timestamps();

				// Διασφάλιση μοναδικότητας: Ένας χρήστης, μία φορά στην ίδια ομάδα
				$table->unique(['team_id', 'user_id']);

				$table->comment('Links users to teams and defines their specific role within that group.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('teams');
		}
	};
