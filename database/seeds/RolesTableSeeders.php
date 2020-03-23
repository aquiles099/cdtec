<?php

use Illuminate\Database\Seeder;
use App\Role;
class RolesTableSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role= new Role();
        $role->name="admin";
        $role->save();
    }
}
