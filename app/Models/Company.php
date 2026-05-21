<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\HasMany;
	use Illuminate\Database\Eloquent\SoftDeletes;

	/**
	 * Class Company
	 * Η κορυφή της ιεραρχίας. Μια εταιρεία κατέχει πολλές ομάδες (Teams).
	 */
	class Company extends Model {
		use HasFactory, SoftDeletes;

		/**
		 * Τα πεδία που επιτρέπουν mass assignment.
		 *
		 * @var array<int, string>
		 */
		protected $fillable = [
			'name',
			'description',
			'legal_name',
			'vat_number',
			'tax_office',
			'website',
			'email',
			'phone',
			'industry',
			'logo_path',
			'owner_id',
			'is_active',
		];

		/**
		 * Μετατροπή τύπων δεδομένων.
		 *
		 * @var array<string, string>
		 */
		protected $casts = [
			'is_active'  => 'boolean',
			'created_at' => 'datetime',
			'updated_at' => 'datetime',
			'deleted_at' => 'datetime',
		];

		/**
		 * Ο Ιδιοκτήτης/Υπεύθυνος της εταιρείας (Owner).
		 *
		 * @return BelongsTo
		 */
		public function owner(): BelongsTo {
			return $this->belongsTo(User::class, 'owner_id');
		}

		/**
		 * Οι ομάδες που ανήκουν στην εταιρεία.
		 * Εδώ υλοποιείται το "1 Company hasMany Teams".
		 *
		 * @return HasMany
		 */
		public function teams(): HasMany {
			return $this->hasMany(Team::class);
		}

		/**
		 * Helper Method: Επιστρέφει όλους τους χρήστες που ανήκουν
		 * σε όλες τις ομάδες της εταιρείας (Through relationship).
		 */
		public function allMembers() {
			// Αν θέλεις να τραβήξεις όλα τα μέλη όλων των ομάδων της εταιρείας
			return $this->hasManyThrough(User::class, Team::class);
		}
	}
