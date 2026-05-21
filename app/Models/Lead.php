<?php

	namespace App\Models;

	use App\Contracts\Taskable;
	use App\Enums\Leads\LeadPriority;
	use App\Enums\Leads\LeadStatus;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Scopes\LeadScope;
	use App\Models\Users\User;
	use App\Traits\Auditable;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphMany;
	use Illuminate\Database\Eloquent\Relations\MorphToMany;
	use Illuminate\Database\Eloquent\SoftDeletes;
	use Illuminate\Support\Facades\DB;
	use Throwable;

	class Lead extends Model implements Taskable {
		use SoftDeletes, HasFactory, Auditable;

		protected $fillable = [
			'first_name',
			'last_name',
			'job_title',
			'company_name',
			'email',
			'website',
			'phone',
			'source',
			'status',
			'priority',
			'estimated_value',
			'owner_id',
			'converted_to_contact_id',
			'converted_to_account_id',
			'converted_to_opportunity_id',
			'converted_at',
			'converted_by',
			'last_contacted_at',
			'notes',
			'is_active'
		];

		protected $casts = [
			'estimated_value'   => 'decimal:2',
			'converted_at'      => 'datetime',
			'last_contacted_at' => 'datetime',
			'is_active'         => 'boolean',
			'status'            => LeadStatus::class,
			'priority'          => LeadPriority::class,
		];

		protected static function booted(): void {}

		// --- Accessors ---

		public function getFullNameAttribute(): string {
			return trim($this->first_name." " . $this->last_name);
		}

		public function getTaskableLabelAttribute(): string {
			return "🎯 " . $this->full_name;
		}

		public function getTaskableUrlAttribute(): string {
			return route('leads.show', ['lead' => $this->id]);
		}

		// --- Relationships ---

		public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }

		// Μέσα στο Lead.php ή Opportunity.php
		public function tasks(): MorphMany {
			return $this->morphMany(Task::class, 'taskable');
		}

		public function convertedContact(): BelongsTo { return $this->belongsTo(Contact::class, 'converted_to_contact_id'); }

		public function convertedAccount(): BelongsTo { return $this->belongsTo(Account::class, 'converted_to_account_id'); }

		public function convertedOpportunity(): BelongsTo { return $this->belongsTo(Opportunity::class, 'converted_to_opportunity_id'); }

		public function tags(): MorphToMany { return $this->morphToMany(Tag::class, 'taggable'); }

		public function customFieldValues(): MorphMany { return $this->morphMany(CustomFieldValue::class, 'model'); }

		// --- Business Logic ---

		public function isConvertible(): bool {
			return !$this->isConverted() && !in_array($this->status, [
					'junk',
					'lost'
				]);
		}

		public function isConverted(): bool {
			return !is_null($this->converted_at);
		}

		/**
		 * Convert Lead to Customer entities.
		 *
		 * @throws Throwable
		 */
		public function convert(array $accountData, array $contactData, ?array $opportunityData = null): array {
			return DB::transaction(function () use ($accountData, $contactData, $opportunityData) {
				// Create Account
				$account = Account::create($accountData + [
						'owner_id' => $this->owner_id,
					]);

				// Create Contact
				$contact = Contact::create($contactData + [
						'account_id' => $account->id,
						'owner_id'   => $this->owner_id,
					]);

				// Create Opportunity
				$opportunity = $opportunityData ? Opportunity::create($opportunityData + [
						'account_id' => $account->id,
						'contact_id' => $contact->id,
						'owner_id'   => $this->owner_id,
					]) : null;

				// Update Lead
				$this->update([
					'status'                      => LeadStatus::CONVERTED,
					'converted_to_contact_id'     => $contact->id,
					'converted_to_account_id'     => $account->id,
					'converted_to_opportunity_id' => $opportunity?->id,
					'converted_at'                => now(),
					'is_active'                   => false,
				]);

				return compact('account', 'contact', 'opportunity');
			});
		}

		// --- Scopes ---

		/**
		 * Bridge for the unified filter
		 */
		public function scopeFilter(Builder $query, array $filters): Builder {
			return (new LeadScope())->scopeFilter($query, $filters);
		}

		public function scopeNew(Builder $query): Builder {
			return (new LeadScope())->scopeNew($query);
		}

		public function scopeQualified(Builder $query): Builder {
			return (new LeadScope())->scopeQualified($query);
		}

		public function scopeConverted(Builder $query): Builder {
			return (new LeadScope())->scopeConverted($query);
		}

		public function scopeNotContactedSince(Builder $query, int $days = 7): Builder {
			return (new LeadScope())->scopeNotContactedSince($query, $days);
		}
	}