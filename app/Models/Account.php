<?php

	namespace App\Models;

	use App\Models\Activities\Activity;
	use App\Models\Invoices\Invoice;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Scopes\AccountScope;
	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\HasMany;
	use Illuminate\Database\Eloquent\Relations\HasManyThrough;
	use Illuminate\Database\Eloquent\Relations\MorphMany;
	use Illuminate\Database\Eloquent\Relations\MorphToMany;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Class Account
	 * Represents a business entity or client within the CRM.
	 */
	class Account extends Model {
		use SoftDeletes, HasFactory;

		/**
		 * The attributes that are mass assignable.
		 *
		 * @var array<int, string>
		 */
		protected $fillable = [
			'name',
			'email',
			'phone',
			'website',
			'address',
			'city',
			'state',
			'country',
			'postal_code',
			'industry',
			'employee_count',
			'annual_revenue',
			'owner_id',
			'is_active',
			'notes',
		];

		// Σχέση με τον User
		public function user(): BelongsTo {
			return $this->belongsTo(User::class);
		}

		/**
		 * The attributes that should be cast.
		 *
		 * @var array<string, string>
		 */
		protected $casts = [
			'annual_revenue' => 'decimal:2',
			'employee_count' => 'integer',
			'is_active'      => 'boolean',
			'deleted_at'     => 'datetime',
		];

		protected static function booted(): void {
			static::addGlobalScope(new AccountScope());
		}

		// --- Accessors & Mutators ---

		public function getFullAddressAttribute(): string {
			return implode(', ', array_filter([
				$this->address, $this->city, $this->state, $this->postal_code, $this->country,
			]));
		}

		public function getPrimaryContactAttribute(): ?Contact {
			return $this->contacts()->where('is_primary', true)->first();
		}

		public function getTotalOpportunityValueAttribute(): float {
			return (float) $this->opportunities()->where('status', '!=', 'lost')->sum('amount');
		}

		public function getOpenOpportunitiesCountAttribute(): int {
			return $this->opportunities()->where('status', 'open')->count();
		}

		// --- Relationships ---

		/**
		 * Σχέση: Ο λογαριασμός ανήκει σε έναν υπάλληλο (Owner).
		 * Τον ονομάζουμε 'employee' για να ταιριάζει με το $account->employee->username
		 */
		public function owner(): BelongsTo {
			// Ορίζουμε ρητά το owner_id ως foreign key
			return $this->belongsTo(User::class, 'owner_id', 'id');
		}

		public function contacts(): HasMany {
			return $this->hasMany(Contact::class, 'account_id');
		}

		public function opportunities(): HasMany {
			return $this->hasMany(Opportunity::class, 'account_id');
		}

		public function invoices(): HasMany {
			return $this->hasMany(Invoice::class, 'account_id');
		}

		public function payments(): HasManyThrough {
			return $this->hasManyThrough(Payment::class, Invoice::class, 'account_id', 'invoice_id');
		}

		// --- Polymorphic Relationships ---

		public function activities(): MorphMany {
			return $this->morphMany(Activity::class, 'activitable');
		}

		public function notes(): MorphMany {
			return $this->morphMany(Note::class, 'notable');
		}

		public function documents(): MorphMany {
			return $this->morphMany(Document::class, 'documentable');
		}

		public function tags(): MorphToMany {
			return $this->morphToMany(Tag::class, 'taggable');
		}

		public function customFieldValues(): MorphMany {
			return $this->morphMany(CustomFieldValue::class, 'model');
		}
	}