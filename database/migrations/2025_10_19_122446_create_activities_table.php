<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('activity_types', function (Blueprint $table) {
				$table->increments('id');

				$table->string('name')->unique();
				$table->string('slug')->unique()->nullable();
				$table->string('icon')->nullable()->comment('Icon class or name for UI');
				$table->string('color')->nullable()->comment('Color code for UI display');

				$table->text('description')->nullable();

				$table->boolean('is_active')->default(true);

				$table->timestamps();

				$table->comment('Lookup table for standardized activity types (Call, Meeting, Email, Task, Demo, etc.)');
			});

			Schema::create('activities', function (Blueprint $table) {
				$table->increments('id');
				$table->string('type')->comment('Type of activity: call, meeting, email, task, sms, demo, etc.');
				$table->string('subject')->nullable()->comment('Short title or subject of the activity');
				$table->text('description')->nullable()->comment('Detailed description or content of the activity');
				$table->text('content')->nullable()->comment('Full content (especially for emails/SMS)');

				$table->string('direction')->nullable()->default('outbound')->comment('inbound or outbound (mainly for communications)');

				// Optional: add priority for tasks
				$table->string('priority')->nullable()->default('medium')->comment('low, medium, high, urgent');

				$table->dateTime('due_at')->nullable()->comment('When this activity is due');
				$table->dateTime('completed_at')->nullable()->comment('When the activity was actually completed');
				$table->dateTime('sent_at')->nullable()->comment('When the communication was sent');

				$table->string('status')->default('pending')->comment('Status: pending, in_progress, completed, sent, failed, cancelled');
				$table->boolean('is_completed')->default(false)->comment('Quick flag for completed activities');

				// Polymorphic relationship
				$table->unsignedInteger('activitable_id');
				$table->string('activitable_type');
				$table->index(['activitable_type', 'activitable_id']);

				// Foreign keys
				$table->unsignedInteger('owner_id')->nullable()->comment('User who owns or is responsible for this activity');
				$table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');

				$table->unsignedInteger('activity_type_id')->nullable()->comment('Reference to standardized activity type');
				$table->foreign('activity_type_id')->references('id')->on('activity_types')->onDelete('set null');

				// Additional indexes for common queries
				$table->index(['owner_id', 'status', 'due_at']);
				$table->index(['activitable_type', 'status']);
				$table->index(['type', 'status']);

				$table->text('notes')->nullable();
				$table->boolean('is_active')->default(true);

				$table->softDeletes();
				$table->timestamps();

				$table->comment('Unified activities and communications table. Supports calls, meetings, emails, tasks, SMS, etc. Polymorphic to accounts, contacts, opportunities, etc.');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('activities');
		}
	};
