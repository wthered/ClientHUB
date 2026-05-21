<?php

	namespace App\Traits;

	use App\Models\ActivityLog;
	use App\Models\AuditLog;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Request;
	use Illuminate\Support\Facades\Auth;

	trait LogsActivity {

		protected static function bootLogsActivity(): void {
			static::created(function (Model $model) {
				static::recordLogs($model, 'created', 'Δημιουργήθηκε νέα εγγραφή');
			});

			static::updated(function (Model $model) {
				static::recordLogs($model, 'updated', 'Ενημέρωση στοιχείων');
			});

			static::deleted(function (Model $model) {
				static::recordLogs($model, 'deleted', 'Η εγγραφή διαγράφηκε');
			});
		}

		protected static function recordLogs(Model $model, string $action, string $description): void {
			$userId = Auth::id();
			$ip = Request::ip();
			$agent = Request::userAgent();

			// 1. Εγγραφή στο ActivityLog (για το Timeline του χρήστη)
			ActivityLog::create([
				'log_type'      => 'activity',
				'event'         => $action,
				'description'   => "{$description}: " . ($model->name ?? $model->id),
				'loggable_id'   => $model->id,
				'loggable_type' => get_class($model),
				'user_id'       => $userId,
				'properties'    => [
					'old' => array_intersect_key($model->getOriginal(), $model->getDirty()),
					'new' => $model->getDirty(),
				],
				'ip_address'    => $ip,
				'user_agent'    => $agent,
			]);

			// 2. Εγγραφή στο AuditLog (για τον Administrator / Security)
			if ($action === 'updated' || $action === 'deleted') {
				AuditLog::create([
					'user_id'        => $userId,
					'action'         => $action,
					'auditable_id'   => $model->id,
					'auditable_type' => get_class($model),
					'old_values'     => array_intersect_key($model->getOriginal(), $model->getDirty()),
					'new_values'     => $model->getDirty(),
					'ip_address'     => $ip,
					'user_agent'     => $agent,
				]);
			}
		}

		/**
		 * Η σχέση που θα καλείς στο show.blade.php
		 */
		public function activities() {
			return $this->morphMany(ActivityLog::class, 'loggable')->latest();
		}
	}
