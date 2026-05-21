<?php

	namespace App\Models;

	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphTo;

	class Note extends Model {
		use HasFactory;

		/**
		 * The attributes that are mass assignable.
		 */
		protected $fillable = [
			'user_id',
			'notable_id',
			'notable_type',
			'content',
		];

		/**
		 * Get the owning notable model (polymorphic relationship).
		 */
		public function notable(): MorphTo {
			return $this->morphTo();
		}

		/**
		 * Get the user who created the note.
		 */
		public function user(): BelongsTo {
			return $this->belongsTo(User::class);
		}
	}
