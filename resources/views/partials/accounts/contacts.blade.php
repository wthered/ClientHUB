<div class="section-header">
	<h3>Contacts ({{ $contacts->count() }})</h3>
	<button type="button" class="btn-primary-sm" id="addContactBtn">New Contact</button>
</div>

<table class="data-table">
	<thead>
	<tr>
		<th>Contact Details</th> {{-- Ενοποιημένη κεφαλίδα --}}
		<th>Phone</th>
		<th>Role</th>
	</tr>
	</thead>
	<tbody>
	@foreach($contacts as $contact)
		<tr class="{{ $contact->is_primary ? 'row-primary' : '' }}">
			<td>
				<div class="contact-profile">
					{{-- Το αστέρι για τον Primary --}}
					@if($contact->is_primary)
						<i class="fas fa-star primary-star" title="Primary Contact"></i>
					@endif

					<div class="contact-info">
						<span class="contact-name">{{ $contact->first_name }} {{ $contact->last_name }}</span>
						<span class="contact-email">{{ $contact->email }}</span>
					</div>
				</div>
			</td>
			<td><span class="text-tabular">{{ $contact->phone }}</span></td>
			<td>
				<div class="role-wrapper">
					<span class="badge">{{ $contact->position ?? 'N/A' }}</span>
					@if($contact->is_primary)
						<span class="badge badge-primary-status">Primary</span>
					@endif
				</div>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>