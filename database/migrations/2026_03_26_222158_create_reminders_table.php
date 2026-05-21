<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('reminders', function (Blueprint $table) {
				$table->increments('id');

				// Polymorphic relation (reminder for contact, deal, company, etc.)
				$table->unsignedInteger('remindable_id');
				$table->string('remindable_type');

				// Reminder details
				$table->string('title');
				$table->text('notes')->nullable();
				$table->timestamp('due_at');
				$table->string('status')->default('pending')->comment('pending, done, dismissed');

				// Assigned to user
				$table->unsignedInteger('user_id');
				$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

				// Created by user
				$table->unsignedInteger('created_by');
				$table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

				$table->timestamps();
			});

		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('reminders');
		}
	};
