<?php

	namespace App\Models\Users;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class UserProfile extends Model {
		use SoftDeletes, HasFactory;

		public    $incrementing = false;
		protected $primaryKey   = 'user_id';
		/**
		 * Τα πεδία που επιτρέπεται να γεμίσουν μαζικά (Mass Assignment).
		 */
		protected $fillable = [
			'user_id',
			'avatar',
			'first_name',
			'last_name',
			'phone',
			'position',
			'bio',
		];

		protected $casts = [
			'settings'  => 'array',
		];

		/**
		 * Σχέση: Το Profile ανήκει σε έναν Χρήστη (Inverse of HasOne).
		 * * @return BelongsTo
		 */
		public function user(): BelongsTo {
			return $this->belongsTo(User::class, 'user_id', 'id');
		}

		/**
		 * Accessor για το πλήρες ονοματεπώνυμο.
		 * Χρήση: $profile->full_name
		 */
		public function getFullNameAttribute(): string {
			return $this->first_name . " " . $this->last_name;
		}

		public function getAvatarUrlAttribute() {
			if (!$this->avatar) {
				// Επιστρέφει μια default εικόνα αν προτιμάς αντί για γράμματα
				// return asset('images/default-avatar.png');
				return null;
			}

			if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
				return $this->avatar;
			}

			return asset('storage/' . $this->avatar);
		}
	}
