<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\HasMany;

	class CustomField extends Model {
		protected $fillable = [
			'model_type',
			'name',
			'label',
			'type',
			'options',
			'is_required',
			'sort_order'
		];

		protected $casts = [
			'options'     => 'array',
			// Μετατρέπει αυτόματα το JSON σε PHP Array
			'is_required' => 'boolean',
			'sort_order'  => 'integer',
		];

		/**
		 * Επιστρέφει όλες τις αποθηκευμένες τιμές για αυτό το πεδίο.
		 */
		public function values(): HasMany {
			return $this->hasMany(CustomFieldValue::class);
		}
	}