<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        /**
         * Run the migrations.
         */
        public function up(): void {
            Schema::create('contacts', function (Blueprint $table) {
                $table->increments('id')->comment('Primary key: unique contact ID');
                $table->unsignedInteger('account_id')->nullable()->comment('Foreign key linking this contact to a client');
                $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
                $table->string('first_name')->comment('First name of the contact person');
                $table->string('last_name')->nullable()->comment('Last name of the contact person');
                $table->string('email')->nullable()->comment('Email address of the contact');
                $table->string('phone')->nullable()->comment('Phone number of the contact');
	            $table->text('address')->nullable();
	            $table->string('city')->nullable();
	            $table->string('country')->nullable();
	            $table->text('notes')->nullable();
	            $table->string('job_title')->nullable()->comment('Job title or role of the contact at the client company');
                $table->boolean('is_primary')->default(false)->comment('Marks whether this contact is the primary contact for the client');

	            $table->unsignedInteger('owner_id')->nullable();
	            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
                $table->timestamps();
	            $table->softDeletes();

                $table->comment('Stores individual contacts for clients, including primary contacts and their roles.');
            });

        }

        /**
         * Reverse the migrations.
         */
        public function down(): void {
            Schema::dropIfExists('contacts');
        }
    };
