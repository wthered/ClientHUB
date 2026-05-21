<div class="properties-wrapper">
	<table class="data-table details-table">
		<thead>
		<tr>
			<th>Πεδίο</th>
			<th style="width: 35%;">Παλιά Τιμή</th>
			<th style="width: 35%;">Νέα Τιμή</th>
		</tr>
		</thead>
		<tbody>
		@php
			// Διαχωρισμός παλιών και νέων τιμών (Spatie/Custom convention)
			$old = $properties['old'] ?? [];
			$new = $properties['attributes'] ?? $properties;

			// Φιλτράρουμε τυχόν εσωτερικά κλειδιά που δεν θέλουμε να δείξουμε
			$excludedKeys = ['id', 'created_at', 'updated_at', 'deleted_at'];
		@endphp

		@forelse($new as $key => $value)
			@continue(in_array($key, $excludedKeys))

			<tr>
				<td class="bold">
					<span class="text-muted">{{ str_replace('_', ' ', ucfirst($key)) }}</span>
				</td>
				<td>
					@if(isset($old[$key]))
						<span class="status-badge event-danger" style="text-transform: none; font-size: 0.75rem;">
                                {{ is_array($old[$key]) ? json_encode($old[$key]) : $old[$key] }}
                            </span>
					@else
						<span class="text-muted">—</span>
					@endif
				</td>
				<td>
                        <span class="status-badge event-success" style="text-transform: none; font-size: 0.75rem;">
                            {{ is_array($value) ? json_encode($value) : ($value ?? 'NULL') }}
                        </span>
				</td>
			</tr>
		@empty
			<tr>
				<td colspan="3" class="text-center p-4">
					<i class="fas fa-info-circle text-muted"></i>
					<p class="text-muted mt-2">Δεν βρέθηκαν αναλυτικές καταγραφές μεταβολών.</p>
				</td>
			</tr>
		@endforelse
		</tbody>
	</table>
</div>

<style>
    /* Ειδικά styles μόνο για το modal table */
    .details-table {
        margin-bottom: 0;
        font-size: 0.85rem;
    }
    .details-table td {
        padding: 10px 15px !important;
    }
    .details-table .bold {
        color: var(--secondary);
        font-weight: 600;
    }
    .properties-wrapper {
        max-height: 450px;
        overflow-y: auto;
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
    }
</style>