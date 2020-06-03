<?php

use App\Equipe;
use App\User;
use App\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

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

        $faker = Faker::create();

        for ($i = 0; $i < 45; $i++) {
            $u = new User();
            $u->name = $faker->firstName();
            $u->email = $faker->email;
            $u->login = $faker->userName;
            $u->password = Hash::make('10203040');
            $u->atendente = true;
            if (($i % 5) == 0) $u->supervisor = true;
            $u->save();
        }

        $eq = Equipe::first();
        if (!$eq) {

            $e = new Equipe();
            $e->equipe = "Suporte";
            $e->usuario_id = Usuario::where('login', '=', 'admin')->first()->id;
            $e->save();

            $e->usuarios()->sync(1);

            $e = new Equipe();
            $e->equipe = "Comercial";
            $e->usuario_id = Usuario::where('login', '=', 'admin')->first()->id;
            $e->save();

            $e->usuarios()->sync(1);
        }
    }
}
