<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void {
			Schema::create('communications', function (Blueprint $table) {
				$table->increments('id');

				// Polymorphic relation
				$table->unsignedInteger('communicable_id');
				$table->string('communicable_type');
				$table->index(['communicable_type', 'communicable_id']); // Index για ταχύτητα στο timeline

				$table->string('type')->index(); // email, call, sms, meeting, WhatsApp
				$table->string('direction')->default('outbound')->index()->comment('inbound, outbound');

				$table->string('subject')->nullable();
				$table->longText('content'); // Χρησιμοποιούμε longText για μεγάλα emails

				// --- ΤΑ ΝΕΑ ΠΕΔΙΑ ΠΟΥ ΛΕΙΠΟΥΝ ---
				$table->string('from')->nullable()->comment('Email address or Phone number of sender');
				$table->string('to')->nullable()->comment('Email address or Phone number of recipient');

				$table->integer('duration')->nullable()->comment('Duration in seconds (για calls/meetings)');
				$table->string('result')->nullable()->comment('π.χ. busy, no_answer, completed, interested');

				$table->json('metadata')->nullable()->comment('Για headers, message IDs, ή API responses');
				// -------------------------------

				$table->string('status')->default('sent')->index();
				$table->timestamp('occurred_at')->useCurrent()->comment('Πιο γενικό όνομα από το sent_at (π.χ. για meetings)');

				$table->unsignedInteger('user_id');
				$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

				$table->timestamps();
				$table->softDeletes(); // Πάντα χρήσιμο σε logs
			});

		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void {
			Schema::dropIfExists('communications');
		}
	};
