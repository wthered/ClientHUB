<form action="{{ route('tags.store') }}" method="POST" class="tag-form">
	@csrf
	<input type="text" name="name" placeholder="Όνομα Tag (π.χ. Hot Lead)" required>

	<div class="color-picker-wrapper">
		<label>Χρώμα:</label>
		<input type="color" name="color" value="#3490dc">
	</div>

	<button type="submit" class="btn-primary">Προσθήκη</button>
</form>

<div class="tags-grid">
	@foreach($tags as $tag)
		<span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">
            {{ $tag->name }}
            <form action="{{ route('tags.destroy', $tag->id) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="delete-tag">&times;</button>
            </form>
        </span>
	@endforeach
</div>