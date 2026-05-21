<?php

	namespace App\Models;

	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\BelongsToMany;

	/**
	 * Class Team
	 * * Αντιπροσωπεύει μια ομάδα μέσα στο CRM.
	 * Υποστηρίζει ιεραρχική δομή με σύνδεση σε Εταιρεία, Manager και Leader.
	 */
	class Team extends Model {

		use HasFactory;

		/**
		 * Τα πεδία που επιτρέπουν μαζική ανάθεση.
		 *
		 * @var array<int, string>
		 */
		protected $fillable = [
			'name',
			'description',
			'company_id',
			'manager_id',
			'leader_id',
			'is_active',
		];

		/**
		 * Αυτόματη μετατροπή τύπων δεδομένων (Casting).
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
		 * Γενική σχέση με τους χρήστες (Pivot).
		 * Επιστρέφει όλους τους συνδεδεμένους χρήστες ανεξαρτήτως ρόλου.
		 * * @return BelongsToMany
		 */
		public function users(): BelongsToMany {
			return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
		}

		/**
		 * Ο Manager της ομάδας (High-level Supervisor).
		 * Συνήθως ο υπεύθυνος που επιβλέπει την ομάδα από πλευράς διοίκησης.
		 * * @return BelongsTo
		 */
		public function manager(): BelongsTo {
			return $this->belongsTo(User::class, 'manager_id');
		}

		/**
		 * Ο Leader της ομάδας (Operational Head).
		 * * @return BelongsTo
		 */
		public function leader(): BelongsTo {
			return $this->belongsTo(User::class, 'leader_id')->with(['profile']);
		}

		/**
		 * Τα μέλη της ομάδας ταξινομημένα κατά ID.
		 * Χρησιμοποιείται για την εμφάνιση της λίστας των υπαλλήλων στον πίνακα.
		 * Περιλαμβάνει τις πληροφορίες ρόλου από τον pivot πίνακα.
		 * * @return BelongsToMany
		 */
		public function members(): BelongsToMany {
			return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps()->orderBy('users.id');
		}

		/**
		 * Η μητρική Εταιρεία (Top-level Entity).
		 * Η ομάδα ανήκει υποχρεωτικά ή προαιρετικά σε μια συγκεκριμένη εταιρεία.
		 * * @return BelongsTo
		 */
		public function company(): BelongsTo {
			return $this->belongsTo(Company::class);
		}
	}
