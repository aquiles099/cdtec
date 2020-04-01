<?php

use Illuminate\Database\Seeder;
use App\Role;
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role= new Role();
        $role->code="admin";
        $role->description="admin";
        $role->save();
        $role= new Role();
        $role->code="consultor";
        $role->description="consultor";
        $role->save();
        $role= new Role();
        $role->code="client";
        $role->description="client";
        $role->save();
    }
}
