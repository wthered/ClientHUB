<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('activity_logs', function (Blueprint $table) {
				$table->string('log_type')->default('activity')->comment('activity, audit, system, notification');

				$table->string('event')->comment('What happened: created, updated, deleted, status_changed, note_added, etc.');
				$table->text('description')->nullable()->comment('Human-readable description of the action');

				// Polymorphic: which record this log belongs to (Account, Contact, Opportunity, etc.)
				$table->unsignedInteger('loggable_id')->comment('which record this log belongs to');
				$table->string('loggable_type')->comment('Account, Contact, Opportunity, etc.)');
				$table->index([
					'loggable_type',
					'loggable_id'
				]);

				// Who performed the action
				$table->unsignedInteger('user_id')->nullable();
				$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

				// Optional extra data (JSON)
				$table->json('properties')->nullable()->comment('Additional data like old/new values, IP, etc.');

				$table->ipAddress()->nullable();
				$table->string('user_agent')->nullable();

				$table->timestamps();

				// Table-level comment
				$table->comment('Activity and audit log for the entire CRM. Tracks user actions on accounts, contacts, opportunities, etc.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('activity_logs');
		}
	};
