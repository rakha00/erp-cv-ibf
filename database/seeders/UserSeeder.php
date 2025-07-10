<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$now = now();
		$users = [
			[
				'name' => 'Admin Utama',
				'email' => 'admin@example.com',
				'password' => Hash::make('password'),
				'email_verified_at' => $now,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'name' => 'Manajer Penjualan',
				'email' => 'manager.sales@example.com',
				'password' => Hash::make('password'),
				'email_verified_at' => $now,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'name' => 'Staff Gudang',
				'email' => 'staff.gudang@example.com',
				'password' => Hash::make('password'),
				'email_verified_at' => $now,
				'created_at' => $now,
				'updated_at' => $now,
			],
		];

		User::query()->insert($users);
	}
}