<?php

	namespace App\Http\Controllers;

	use App\Models\AuditLog;
	use Illuminate\Http\Request;

	class AuditLogController extends Controller {
		/**
		 * Display a listing of the resource.
		 */
		public function index() {
			$logs = AuditLog::with([
				'user',
				'auditable'
			])
				->latest()
				->paginate(30);

			return view('admin.audit_logs.index', compact('logs'));
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
			//
		}

		/**
		 * Display the specified resource.
		 */
		public function show(AuditLog $auditLog) {
			return view('admin.audit_logs.show', compact('auditLog'));
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
