<?php

	namespace App\Models\Users;

	// use Illuminate\Contracts\Auth\MustVerifyEmail;
	use App\Models\Account;
	use App\Models\Team;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Relations\BelongsToMany;
	use Illuminate\Database\Eloquent\Relations\HasMany;
	use Illuminate\Database\Eloquent\Relations\HasOne;
	use Illuminate\Database\Eloquent\SoftDeletes;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Support\Str;
	use Spatie\Permission\Traits\HasRoles;

	class User extends Authenticatable {

		use HasFactory, Notifiable, HasRoles, SoftDeletes;

		// Ninja Tip: Αυτόματο load των settings για να μην κάνεις έξτρα queries
		protected $with = ['settings'];

		/**
		 * The attributes that are mass assignable.
		 *
		 * @var list<string>
		 */
		protected $fillable = [
			'name',
			'email',
			'password',
			'is_active',
			'is_locked',
			'lock_reason',
			'last_login_at',
			'last_login_ip',
			'last_active_at',
			'failed_login_attempts'
		];

		/**
		 * The attributes that should be hidden for serialization.
		 *
		 * @var list<string>
		 */
		protected $hidden = [
			'password',
			'remember_token',
		];

		// ΑΛΛΑΓΗ: Πρώτα το 'user_id' (foreign) και μετά το 'id' (local)
		public function profile(): HasOne {
			return $this->hasOne(UserProfile::class, 'user_id', 'id')->withDefault([
				'first_name' => 'System',
				'last_name'  => 'User',
			]);
		}

		/**
		 * Get the user's initials (e.g. William Wallace -> WW)
		 */
		public function getInitialsAttribute(): string {
			return Str::of($this->name)->explode(' ')->map(function ($word) {
				return Str::substr($word, 0, 1);
			})->take(2)->implode('');
		}

		public function accounts(): HasMany {
			return $this->hasMany(Account::class, 'owner_id');
		}

		public function teams(): BelongsToMany {
			return $this->belongsToMany(Team::class)->withPivot('role');
			// Σημείωση: Το αυτόματο Enum casting σε pivot columns
			// είναι λίγο "δύστροπο" στο Laravel, οπότε η χρήση του Enum στο
			// Blade όπως την κάναμε είναι η πιο ασφαλής οδός.
		}

		public function settings(): HasOne|User {
			return $this->hasOne(UserSetting::class);
		}

		protected function casts(): array {
			return [
				'email_verified_at' => 'datetime',
				'password'          => 'hashed',
				'is_active'         => 'boolean',
				'is_locked'         => 'boolean',
				'last_login_at'     => 'datetime',
				'last_active_at'    => 'datetime',
			];
		}
	}
