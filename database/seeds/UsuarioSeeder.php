<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('usuario')->insert([
            'name' => 'Roger Pitigliani',
            'email' => 'rogerwinter@gmail.com',
            'password' =>  Hash::make('10203040'),
            'login' => 'admin',
        ]);
    }
}
