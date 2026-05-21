<?php

	namespace App\Models\Opportunities;

	use App\Contracts\Taskable;
	use App\Enums\Opportunities\OpportunityStage;
	use App\Enums\Opportunities\OpportunityStageStatus;
	use App\Models\Account;
	use App\Models\Activity;
	use App\Models\ActivityLog;
	use App\Models\Contact;
	use App\Models\Pipeline;
	use App\Models\Tag;
	use App\Models\Task;
	use App\Models\Users\User;
	use App\Observers\OpportunityObserver;
	use App\Traits\HasEmployeeScope;
	use App\Traits\LogsActivity;
	use Illuminate\Database\Eloquent\Attributes\ObservedBy;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\BelongsToMany;
	use Illuminate\Database\Eloquent\Relations\HasMany;
	use Illuminate\Database\Eloquent\Relations\HasOneThrough;
	use Illuminate\Database\Eloquent\Relations\MorphMany;
	use Illuminate\Database\Eloquent\Relations\MorphToMany;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Class Opportunity
	 * * Αντιπροσωπεύει μια πιθανή πώληση ή συμφωνία (deal) στο CRM.
	 * Συνδέεται με Λογαριασμούς, Επαφές και παρακολουθείται μέσω των Sales Pipelines.
	 */
	#[ObservedBy([OpportunityObserver::class])]
	class Opportunity extends Model implements Taskable {
		use SoftDeletes, HasFactory, HasEmployeeScope, LogsActivity;

		/** @var array<int, string> */
		protected $fillable = [
			'name',
			'amount',
			'currency',
			'close_date',
			'probability',
			'status',
			'account_id',
			'contact_id',
			'stage_id',
			'owner_id',
			'notes',
			'is_active',
		];

		/** @var array<string, string> */
		protected $casts = [
			'status'      => OpportunityStageStatus::class,
			'amount'      => 'decimal:2',
			'close_date'  => 'date',
			'probability' => 'integer',
			'is_active'   => 'boolean',
		];

		// --- Accessors & Mutators ---

		/**
		 * Επιστρέφει τον πλήρη τίτλο της ευκαιρίας μαζί με το ποσό και το νόμισμα.
		 */
		public function getFullTitleAttribute(): string {
			return $this->name." (" . number_format($this->amount, 2) . " ".$this->currency . ")";
		}

		/**
		 * Επιστρέφει το OpportunityStage Enum βασισμένο στο όνομα του σταδίου.
		 */
		public function getStageTypeAttribute(): OpportunityStage {
			// Αντιστοίχιση του string 'name' ή 'slug' από τη βάση με το Enum
			return OpportunityStage::tryFrom($this->stage->name) ?? OpportunityStage::DISCOVERY;
		}

		/**
		 * Υπολογίζει την αναμενόμενη αξία (weighted value) βάσει της πιθανότητας κλεισίματος.
		 */
		public function getWeightedValueAttribute(): float {
			return ($this->amount * $this->probability) / 100;
		}

		/**
		 * Ελέγχει αν η ευκαιρία έχει κλείσει (είτε ως κερδισμένη είτε ως χαμένη).
		 */
		public function getIsClosedAttribute(): bool {
			return in_array($this->status, [
				OpportunityStageStatus::WON,
				OpportunityStageStatus::LOST,
			]);
		}

		public function getTaskableLabelAttribute(): string {
			return "💰 " . $this->name;
		}

		public function getTaskableUrlAttribute(): string {
			return route('opportunities.show', $this->id);
		}

		// --- Relationships ---

		/**
		 * Ο Λογαριασμός (Εταιρεία) στον οποίο ανήκει η ευκαιρία.
		 */
		public function account(): BelongsTo {
			return $this->belongsTo(Account::class);
		}

		/**
		 * Η κύρια Επαφή για αυτή τη συμφωνία.
		 */
		public function contact(): BelongsTo {
			return $this->belongsTo(Contact::class);
		}

		/**
		 * Το τρέχον Στάδιο της ευκαιρίας στο pipeline.
		 */
		public function stage(): BelongsTo {
			return $this->belongsTo(Stage::class, 'stage_id');
		}

		/**
		 * Το Pipeline στο οποίο ανήκει η ευκαιρία (μέσω του Stage).
		 */
		public function pipeline(): HasOneThrough {
			return $this->hasOneThrough(Pipeline::class, Stage::class, 'id', 'id', 'stage_id', 'pipeline_id');
		}

		/**
		 * Ο Υπεύθυνος (Owner) της ευκαιρίας.
		 */
		public function owner(): BelongsTo {
			return $this->belongsTo(User::class, 'owner_id');
		}

		/**
		 * Η ομάδα χρηστών που συμμετέχει στη διαχείριση της ευκαιρίας.
		 */
		public function users(): BelongsToMany {
			return $this->belongsToMany(User::class, 'opportunities_users')->withTimestamps();
		}

		/**
		 * Πολυμορφικά Tags που έχουν ανατεθεί στην ευκαιρία.
		 */
		public function tags(): MorphToMany {
			return $this->morphToMany(Tag::class, 'taggable');
		}

		public function tasks(): MorphMany {
			return $this->morphMany(Task::class, 'taskable');
		}

		// --- Business Logic Methods ---

		/**
		 * Τα επιμέρους είδη (προϊόντα/υπηρεσίες) της ευκαιρίας.
		 */
		public function items(): HasMany {
			return $this->hasMany(OpportunityItem::class);
		}

		/**
		 * Το ιστορικό δραστηριοτήτων της ευκαιρίας.
		 * Εδώ φέρνουμε τα Sales Activities (Calls, Meetings κλπ) και τα Audit Logs.
		 */
		public function activities(): MorphMany {
			return $this->morphMany(Activity::class, 'activitable');
		}

		/**
		 * ΑΝ θέλεις να φέρνεις ΚΑΙ τα Audit Logs (LogsActivity Trait)
		 * θα πρέπει να ορίσεις μια ξεχωριστή σχέση (π.χ. logs)
		 */
		public function auditLogs(): MorphMany {
			// Θα χρειαστείς ένα Model "ActivityLog" που να δείχνει στον πίνακα activity_logs
			return $this->morphMany(ActivityLog::class, 'loggable');
		}
	}