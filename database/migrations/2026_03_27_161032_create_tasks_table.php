<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('tasks', function (Blueprint $table) {
				$table->increments('id');
				$table->string('subject');
				$table->text('description')->nullable();
				$table->string('status')->default('pending')->index();
				$table->string('priority')->default('medium');

				$table->dateTime('due_date')->nullable();
				$table->dateTime('completed_at')->nullable();

				// The User responsible
				$table->unsignedInteger('user_id')->index();
				$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

				$table->unsignedInteger('creator_id')->index()->nullable();
				$table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

				// Polymorphic link (to Lead, Opportunity, Account, etc.)
				$table->nullableMorphs('taskable');

				$table->timestamps();
				$table->softDeletes();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('tasks');
		}
	};
