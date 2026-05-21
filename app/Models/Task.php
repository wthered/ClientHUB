<?php

	namespace App\Models;

	use App\Enums\Tasks\TaskPriority;
	use App\Enums\Tasks\TaskStatus;
	use App\Models\Users\User;
	use App\Traits\Filterable;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\MorphTo;
	use App\Models\Scopes\TaskScope;
	use Illuminate\Database\Eloquent\Builder;
	use Illuminate\Database\Eloquent\SoftDeletes;

	class Task extends Model {
		use Filterable, SoftDeletes;

		protected $fillable = [
			'subject',
			'description',
			'status',
			'priority',
			'due_date',
			'completed_at',
			'user_id',
			'creator_id',
			'taskable_type',
			'taskable_id'
		];

		protected $casts = [
			'due_date'     => 'datetime',
			'completed_at' => 'datetime',
			'status'       => TaskStatus::class,
			'priority'     => TaskPriority::class,
		];

		/**
		 * Η πολυμορφική σχέση.
		 * Επιστρέφει το Lead ή το Opportunity στο οποίο ανήκει το task.
		 */
		public function taskable(): MorphTo {
			return $this->morphTo();
		}

		/**
		 * Ο χρήστης που πρέπει να κάνει το task.
		 */
		public function user(): BelongsTo {
			return $this->belongsTo(User::class, 'user_id');
		}

		/**
		 * Ο χρήστης που δημιούργησε το task.
		 */
		public function creator(): BelongsTo {
			return $this->belongsTo(User::class, 'creator_id');
		}

		/**
		 * Scope Functionality
		 */
		public function scopeFilter(Builder $query, array $filters): Builder {
			return (new TaskScope())->scopeFilter($query, $filters);
		}
	}