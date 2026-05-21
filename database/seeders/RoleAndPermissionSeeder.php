<?php

	namespace Database\Seeders;

	use Illuminate\Database\Seeder;
	use Spatie\Permission\Models\Permission;
	use Spatie\Permission\Models\Role;
	use Spatie\Permission\PermissionRegistrar;

	class RoleAndPermissionSeeder extends Seeder {
		public function run(): void {
			app(PermissionRegistrar::class)->forgetCachedPermissions();

			// 2. Πλήρης Λίστα Permissions ανά Group
			$groups = [
				'accounts'      => ['view accounts', 'create accounts', 'edit accounts', 'delete accounts'],
				'contacts'      => ['view contacts', 'create contacts', 'edit contacts', 'delete contacts'],
				'leads'         => ['view leads', 'create leads', 'edit leads', 'convert leads', 'delete leads'],
				'opportunities' => ['view opportunities', 'create opportunities', 'edit opportunities', 'delete opportunities', 'close opportunities'],
				'activities'    => ['view activities', 'create activities', 'edit activities', 'delete activities'],
				'notes'         => ['view notes', 'create notes', 'edit notes', 'delete notes'],
				'documents'     => ['view documents', 'upload documents', 'delete documents'],
				'products'      => ['view products', 'create products', 'edit products', 'delete products'],
				'financial'     => [
					'view invoices', 'create invoices', 'edit invoices', 'delete invoices',
					'view payments', 'create payments', 'delete payments'
				],
				'reporting'     => ['view dashboard', 'view reports', 'export data'],
				'system'        => ['manage teams', 'manage users', 'manage roles & permissions', 'view audit logs'],
			];

			foreach (collect($groups)->flatten() as $permission) {
				Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
			}

			// 3. Ρόλοι
			$superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
			$admin      = Role::firstOrCreate(['name' => 'admin']);
			$salesMgr   = Role::firstOrCreate(['name' => 'Sales Manager']);
			$salesRep   = Role::firstOrCreate(['name' => 'Sales Representative']);
			$marketing  = Role::firstOrCreate(['name' => 'Marketing']);
			$support    = Role::firstOrCreate(['name' => 'Customer Support']);

			// 4. Ανάθεση (Inheritance Logic)

			// --- Sales Representative ---
			// Θέλει να βλέπει τι πουλάει
			$repPerms = array_merge(
				$groups['accounts'],
				$groups['contacts'],
				$groups['leads'],
				['view opportunities', 'create opportunities', 'edit opportunities'],
				$groups['activities'],
				$groups['notes'],
				['view documents', 'upload documents'],
				['view products'],
				['view dashboard']
			);
			$salesRep->syncPermissions($repPerms);

			// --- Sales Manager ---
			// Ο Manager συχνά εκδίδει την προσφορά/τιμολόγιο
			$mgrPerms = array_merge($repPerms, [
				'convert leads', 'delete leads', 'close opportunities',
				'view invoices', 'create invoices',
				'manage teams', 'view reports', 'export data'
			]);
			$salesMgr->syncPermissions($mgrPerms);

			// --- Marketing ---
			// Για να ξέρει τι καμπάνιες να κάνει
			$marketingPerms = array_merge(
				$groups['leads'],
				['view contacts', 'create contacts', 'edit contacts'],
				['view accounts'],
				['view activities', 'create activities'],
				['view products'],
				['view dashboard']
			);
			$marketing->syncPermissions($marketingPerms);

			// --- Customer Support ---
			// Πρέπει να ξέρει αν ο πελάτης είναι "πληρωμένος"
			$supportPerms = array_merge(
				['view accounts', 'view contacts'],
				['view invoices', 'view payments'],
				$groups['activities'],
				$groups['notes'],
				['view documents'],
				['view dashboard']
			);
			$support->syncPermissions($supportPerms);

			// --- Admin ---
			// Όλα εκτός από το core system security (roles/permissions)
			$adminPerms = Permission::whereNotIn('name', ['manage roles & permissions'])->get();
			$admin->syncPermissions($adminPerms);

			// --- Super Admin ---
			$superAdmin->syncPermissions(Permission::all());

			$this->command->info('🎉 CRM Complete Refactor: Invoices & Products included!');
		}
	}