<?php

	namespace App\Models;

	use App\Models\Users\User;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphTo;

	class AuditLog extends Model {
		protected $fillable = [
			'user_id',
			'action',
			'auditable_id',
			'auditable_type',
			'old_values',
			'new_values',
			'ip_address',
			'user_agent',
		];

		protected $casts = [
			'old_values' => 'array',
			'new_values' => 'array',
		];

		public function user(): BelongsTo {
			return $this->belongsTo(User::class);
		}

		public function auditable(): MorphTo {
			return $this->morphTo();
		}
	}
