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
        $role->code="administrador";
        $role->description="administrador";
        $role->save();
        $role= new Role();
        $role->code="consultor";
        $role->description="consultor";
        $role->save();
        $role= new Role();
        $role->code="cliente";
        $role->description="cliente";
        $role->save();
    }
}
