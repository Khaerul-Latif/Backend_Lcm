<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'admin')->first();

        User::updateOrCreate(
            ['email' => 'lifecamp@mri.com'],
            [
                'name' => 'Admin Lifecamp',
                'password' => Hash::make('adminlifecamp123'),
                'role_id' => $role ? $role->id : null,
            ]
        );
    }
}
