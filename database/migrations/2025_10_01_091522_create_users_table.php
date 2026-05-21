<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('users', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->unique();
				$table->string('email')->unique()->nullable();
				$table->string('password');
				$table->timestamp('email_verified_at')->nullable();

				// Status & Security
				$table->boolean('is_active')->default(true);
				$table->boolean('is_locked')->default(false);
				$table->string('lock_reason')->nullable();
				$table->timestamp('last_active_at')->nullable();
				$table->timestamp('last_login_at')->nullable();
				$table->string('last_login_ip')->nullable();
				$table->integer('failed_login_attempts')->default(0);

				$table->rememberToken();
				$table->timestamps();
				$table->softDeletes();
			});

			Schema::create('user_profiles', function (Blueprint $table) {
				$table->unsignedInteger('user_id');
				$table->string('avatar')->nullable();
				$table->string('first_name');
				$table->string('last_name');
				$table->string('phone')->nullable();
				$table->string('position')->nullable();
				$table->text('bio')->nullable();
				$table->boolean('notify_on_report')->default(false);
				$table->timestamps();
				$table->softDeletes();

				$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			});

			Schema::create('user_settings', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('user_id')->unique();

				// Notification Toggles
				$table->boolean('stagnant_report_enabled')->default(true);
				$table->boolean('daily_pulse_enabled')->default(true);
				$table->boolean('notify_on_sales')->default(true);

				// Localization & UI
				$table->string('language', 5)->default('el');
				$table->string('timezone')->default('Europe/Athens');
				$table->string('theme')->default('light')->comment('light, dark');

				$table->timestamps();

				$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			});

			Schema::create('password_reset_tokens', function (Blueprint $table) {
				$table->string('email')->primary();
				$table->string('token');
				$table->timestamp('created_at')->nullable();
			});

			Schema::create('sessions', function (Blueprint $table) {
				$table->string('id')->primary();
				$table->foreignId('user_id')->nullable()->index();
				$table->string('ip_address', 45)->nullable();
				$table->text('user_agent')->nullable();
				$table->longText('payload');
				$table->integer('last_activity')->index();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('users');
			Schema::dropIfExists('password_reset_tokens');
			Schema::dropIfExists('sessions');
		}
	};
