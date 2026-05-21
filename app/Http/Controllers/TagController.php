<?php

	namespace App\Http\Controllers;

	use App\Models\Tag;
	use Illuminate\Http\Request;

	class TagController extends Controller {
		/**
		 * Display a listing of the resource.
		 */
		public function index() {
			$tags = Tag::query()->orderBy('name')->get();
			return view('tags.index', compact('tags'));
		}

		/**
		 * Show the form for creating a new resource.
		 */
		public function create() {
			//
		}

		/**
		 * Store a newly created resource in storage.
		 */
		public function store(Request $request) {
			$valid = $request->validate([
				'name'  => 'required|unique:tags,name|max:50',
				'color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/' // Validation για hex color
			]);

			Tag::query()->create($valid);
			return back()->with('success', 'Το Tag δημιουργήθηκε!');
		}

		/**
		 * Display the specified resource.
		 */
		public function show(string $id) {
			//
		}

		/**
		 * Show the form for editing the specified resource.
		 */
		public function edit(string $id) {
			//
		}

		/**
		 * Update the specified resource in storage.
		 */
		public function update(Request $request, string $id) {
			//
		}

		/**
		 * Remove the specified resource from storage.
		 */
		public function destroy(string $id) {
			//
		}
	}
