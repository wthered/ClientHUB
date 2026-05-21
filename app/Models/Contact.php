<?php

	namespace App\Models;

	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\HasMany;
	use Illuminate\Database\Eloquent\Relations\MorphMany;
	use Illuminate\Database\Eloquent\SoftDeletes;
	use Illuminate\Support\Str;

	class Contact extends Model {
		use HasFactory, SoftDeletes;

		protected $fillable = [
			'client_id',
			'first_name',
			'last_name',
			'email',
			'phone',
			'address',
			'city',
			'country',
			'notes',
			'position',
			'job_title',
			'is_primary',
			'account_id',
			'owner_id',
		];

		/*
		|--------------------------------------------------------------------------
		| Relationships
		|--------------------------------------------------------------------------
		*/

		// Η σχέση με τα Custom Fields (Polymorphic)
		public function customFieldValues(): MorphMany {
			return $this->morphMany(CustomFieldValue::class, 'model');
		}

		public function account(): BelongsTo {
			return $this->belongsTo(Account::class);
		}

		public function owner(): BelongsTo {
			return $this->belongsTo(User::class, 'owner_id');
		}

		public function deals(): HasMany {
			return $this->hasMany(Deal::class);
		}

		/*
		|--------------------------------------------------------------------------
		| Accessors
		|--------------------------------------------------------------------------
		*/

		public function getFullNameAttribute(): string {
			return trim($this->first_name." ".$this->last_name);
		}

		/**
		 * Get the contact's avatar URL.
		 * Priority: 1. Uploaded File -> 2. Gravatar -> 3. Null (Initials)
		 */
		public function getAvatarUrlAttribute($value): ?string {
			// If a file was manually uploaded in the future
			if ($value) {
				return asset('storage/' . $value);
			}

			// If no file, try Gravatar
			if ($this->email) {
				$hash = md5(Str::lower(trim($this->email)));
				// 'd=404' tells Gravatar to return a 404 error if the user doesn't have an account
				// This allows our Blade 'onerror' or 'if' logic to catch it and show initials instead
				return "https://www.gravatar.com/avatar/".$hash."?s=200&d=mp";
			}

			return null;
		}

		/*
		|--------------------------------------------------------------------------
		| Attributes
		|--------------------------------------------------------------------------
		*/
		public function getInitialsAttribute(): string {
			return mb_strtoupper(
				mb_substr($this->first_name ?? '', 0, 1) .
				mb_substr($this->last_name ?? '', 0, 1)
			);
		}
	}
