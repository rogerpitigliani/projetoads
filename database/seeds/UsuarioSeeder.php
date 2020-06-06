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
            'admin' => true,
            'supervisor' => true,
            'atendente' => true,
        ]);

        $faker = Faker::create();

        $users = [
            ["name" => "Fernando Oliveira", "email" => "fernando@empresa.com.br", "login" => "fernando.oliveira"],
            ["name" => "José Rodrigues", "email" => "joserodrigues@empresa.com.br", "login" => "jose.rodrigues"],
            ["name" => "Marcia Silveira", "email" => "marciasilveira@empresa.com.br", "login" => "marcia.silveira"],
            ["name" => "Renata Silva", "email" => "renatasilva@empresa.com.br", "login" => "renata.silva"],
            ["name" => "Daniel Matos", "email" => "danielmatos@empresa.com.br", "login" => "daniel.matos"],
            ["name" => "Airton Santos", "email" => "airtons@empresa.com.br", "login" => "airton.santos"],
            ["name" => "Cristiam da Rosa", "email" => "cristiamrosa@empresa.com.br", "login" => "cristiam.rosa"],
            ["name" => "Daniele Fernandes", "email" => "danielef@empresa.com.br", "login" => "daniele.fernandes"],
        ];


        // for ($i = 0; $i < 45; $i++) {
        foreach ($users as $i => $us) {
            $u = new User();
            $u->name = $us["name"];
            $u->email = $us["email"];
            $u->login = $us["login"];
            $u->password = Hash::make('10203040');
            $u->atendente = true;
            $u->supervisor = (($i % 3) == 0);
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
