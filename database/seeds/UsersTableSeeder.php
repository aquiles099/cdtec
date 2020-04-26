<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$user=new User();
    	$user->name="admin";
    	$user->last_name="test";
    	$user->office="test";
    	$user->business="test";
    	$user->region="test";
    	$user->city="test";
    	$user->phone="0000-000-00-00";
    	$user->email="admin@cdtec.cl";
    	$user->password=Hash::make("12345678");
    	$user->id_role=1;//admin
    	$user->save();
        $user=new User();
        $user->name="consultor";
        $user->last_name="test";
        $user->office="test";
        $user->business="test";
        $user->region="test";
        $user->city="test";
        $user->phone="0000-000-00-00";
        $user->email="consultor@cdtec.cl";
        $user->password=Hash::make("12345678");
        $user->id_role=2;//consultor
        $user->save();
    }
}
