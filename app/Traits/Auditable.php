<?php

	namespace App\Traits;

	use App\Models\AuditLog;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	/**
	 * @mixin Model
	 */
	trait Auditable {

		public static function bootAuditable(): void {

			// Όταν ενημερώνεται μια εγγραφή
			static::updated(function ($model) {
				$newValues = $model->getDirty();
				$oldValues = array_intersect_key($model->getOriginal(), $newValues);

				// Μην σώσεις log αν δεν άλλαξε τίποτα (ή αν άλλαξε μόνο το timestamp)
				if (empty($newValues) || (count($newValues) === 1 && isset($newValues['updated_at']))) {
					return;
				}

				self::saveAuditLog($model, 'updated', $oldValues, $newValues);
			});

			// Όταν δημιουργείται μια νέα εγγραφή
			static::created(function ($model) {
				self::saveAuditLog($model, 'created', null, $model->getAttributes());
			});

			// Όταν διαγράφεται μια εγγραφή
			static::deleted(function ($model) {
				self::saveAuditLog($model, 'deleted', $model->getOriginal(), null);
			});
		}

		protected static function saveAuditLog($model, $action, $old, $new): void {
			AuditLog::create([
				'user_id'        => Auth::id(),
				'action'         => $action,
				'auditable_id'   => $model->getKey(),
				'auditable_type' => get_class($model),
				'old_values'     => $old,
				'new_values'     => $new,
				'ip_address'     => request()->ip(),
				'user_agent'     => request()->userAgent(),
			]);
		}
	}