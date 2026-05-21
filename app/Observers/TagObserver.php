<?php

	namespace App\Observers;

	use App\Models\Tag;
	use Illuminate\Support\Str;

	class TagObserver {
		/**
		 * Handle the Tag "creating" event.
		 */
		public function creating(Tag $tag): void {
			if (empty($tag->slug)) {
				$tag->slug = Str::slug($tag->name);
			}
		}

		/**
		 * Handle the Tag "updating" event.
		 */
		public function updating(Tag $tag): void {
			// Αν αλλάξει το όνομα, ίσως θέλεις να ενημερώνεται και το slug
			if ($tag->isDirty('name') && !$tag->isDirty('slug')) {
				$tag->slug = Str::slug($tag->name);
			}
		}
	}
