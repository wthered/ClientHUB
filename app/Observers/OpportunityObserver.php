<?php

	namespace App\Observers;

	use App\Enums\Opportunities\OpportunityStageStatus;
	use App\Models\Opportunities\Opportunity;
	use App\Models\Opportunities\Stage;
	use App\Notifications\Opportunities\OpportunityWonNotification;
	use App\Notifications\Opportunities\StageChangedNotification;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;

	class OpportunityObserver {
		/**
		 * Handle the Opportunity "created" event.
		 */
		public function created(Opportunity $opportunity): void {
			DB::table('activity_logs')->insert([
				'log_type'      => 'activity',
				'event'         => 'created',
				'description'   => "Η ευκαιρία '".$opportunity->name."' δημιουργήθηκε.",
				'loggable_id'   => $opportunity->id,
				'loggable_type' => get_class($opportunity),
				'user_id'       => Auth::id() ?? $opportunity->owner_id,
				'created_at'    => now(),
				'updated_at'    => now(),
			]);
		}

		/**
		 * Handle the Opportunity "saving" event.
		 * Εκτελείται ΠΡΙΝ γραφτεί οτιδήποτε στη βάση.
		 */
		public function saving(Opportunity $opportunity): void {
			// 1. Συγχρονισμός Status & Probability βάσει του Stage
			if ($opportunity->isDirty('stage_id')) {
				$stage = Stage::query()->find($opportunity->stage_id);

				if ($stage) {
					$opportunity->status = $stage->status;

					if ($stage->status === OpportunityStageStatus::WON) {
						$opportunity->probability = 100;
						$opportunity->is_active   = false;
						$opportunity->closed_at   = now();
					} elseif ($stage->status === OpportunityStageStatus::LOST) {
						$opportunity->probability = 0;
						$opportunity->is_active   = false;
						$opportunity->closed_at   = now();
					} else {
						$opportunity->is_active   = true;
						$opportunity->closed_at   = null;
					}
				}
			}

			// 2. Business Logic: Καθαρισμός loss_reason αν δεν είναι Lost
			if ($opportunity->status !== OpportunityStageStatus::LOST) {
				$opportunity->loss_reason = null;
			}
		}

		/**
		 * Handle the Opportunity "updated" event.
		 */
		public function updated(Opportunity $opportunity): void {
			// Ειδοποίηση & Log για αλλαγή Stage
			if ($opportunity->isDirty('stage_id')) {
				$oldStageId = $opportunity->getOriginal('stage_id');
				$newStage = $opportunity->stage;
				$oldStage = Stage::query()->find($oldStageId);

				// Καταγραφή στο Activity Log
				DB::table('activity_logs')->insert([
					'log_type'      => 'activity',
					'event'         => 'status_changed',
					'description'   => "Η ευκαιρία '".$opportunity->name."' μετακινήθηκε από το στάδιο '".$oldStage?->name."' στο στάδιο '".$newStage?->name."'.",
					'loggable_id'   => $opportunity->id,
					'loggable_type' => get_class($opportunity),
					'user_id'       => Auth::id(),
					'properties'    => json_encode([
						'old_stage' => $oldStageId,
						'new_stage' => $opportunity->stage_id,
					]),
					'created_at'    => now(),
					'updated_at'    => now(),
				]);

				// ALERT: Ειδοποίηση στον Owner (αν την αλλαγή την έκανε άλλος)
				if (Auth::id() !== $opportunity->owner_id && $newStage) {
					$opportunity->owner->notify(new StageChangedNotification($opportunity, $newStage));
				}
			}

			// ALERT: Ειδοποίηση για WON Status
			if ($opportunity->isDirty('status') && $opportunity->status === OpportunityStageStatus::WON) {
				$opportunity->owner->notify(new OpportunityWonNotification($opportunity));
			}
		}

		/**
		 * Handle the Opportunity "deleted" event.
		 */
		public function deleted(Opportunity $opportunity): void
		{
			// Προαιρετικά: Log deletion
		}
	}
