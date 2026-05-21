<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphTo;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class Document extends Model {
		use SoftDeletes, HasFactory;

		protected $fillable = [
			'file_id',
			'documentable_id',
			'documentable_type',
			'name',
			'original_name',
			'disk',
			'checksum',
			'category',
			'uploaded_by',
			'description',
			'is_active'
		];

		// Αυτό κάνει αυτόματα load το file μαζί με το document
		protected $with = ['file'];

		/**
		 * Η σύνδεση με το "φυσικό" αρχείο.
		 */
		public function file(): BelongsTo {
			return $this->belongsTo(File::class);
		}

		/**
		 * Polymorphic relation: Account, Contact, Lead, Opportunity κλπ.
		 */
		public function documentable(): MorphTo {
			return $this->morphTo();
		}
	}