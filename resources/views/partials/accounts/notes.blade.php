<div class="section-header">
	<h3>Account Notes</h3>
</div>

<div class="quick-note-form mb-4">
	<form action="{{ route('accounts.notes.store', $account->id) }}" method="POST">
		@csrf
		<textarea name="content" class="form-control note-textarea" placeholder="Γράψτε μια νέα σημείωση εδώ..." required></textarea>
		<div class="text-right">
			<button type="submit" class="btn-primary-sm">Προσθήκη Σημείωσης</button>
		</div>
	</form>
</div>

<div class="notes-timeline">
	@forelse($notes as $note)
		<div class="note-card">
			<div class="note-header">
                <span class="note-author">
                    <i class="fas fa-user-circle"></i> {{ $note->user->name ?? 'System' }}
                </span>
				<span class="note-date">{{ $note->created_at->diffForHumans() }}</span>
			</div>
			<div class="note-body">
				{!! nl2br(e($note->content)) !!}
			</div>
			<div class="note-footer">
				<form action="{{ route('notes.destroy', $note->id) }}" method="POST" onsubmit="return confirm('Διαγραφή σημείωσης;')">
					@csrf @method('DELETE')
					<button type="submit" class="btn-delete-link"><i class="fas fa-trash"></i></button>
				</form>
			</div>
		</div>
	@empty
		<p class="text-center text-muted py-4">Δεν υπάρχουν σημειώσεις για αυτόν τον λογαριασμό.</p>
	@endforelse
</div>