<?php

use App\Atendimento;
use App\Classificacao;
use App\Contato;
use App\Equipe;
use App\Mensagem;
use App\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargaFakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker\Factory::create();

        for ($d = 0; $d < 10; $d++) {

            $dt = date("Y-m-d", strtotime("-{$d} day"));
            $qtd = $faker->numberBetween(3, 20);
            echo "\nData: {$dt} - Qtde: {$qtd}";

            for ($i = 0; $i < $qtd; $i++) {



                $eu = DB::table("equipe_usuario")->inRandomOrder()->first();

                if (!$eu) {
                    echo "\nNenhum atendente encontrado\n";
                    exit(0);
                }

                $atendente = Usuario::find($eu->usuario_id);
                $equipe = Equipe::find($eu->equipe_id);





                $canal = $faker->randomElement(["facebook", "telegram", "chat"]);
                $hora = $faker->randomElement(["08", "09", "10", "11", "14", "15", "16", "17"]);
                $qtdmsgs = $faker->numberBetween(5, 30);
                $co = new Contato();
                $co->full_name = $faker->name;
                $co->identity = $faker->text(5) . "@" . $canal;
                $co->photo_uri = "https://s3-sa-east-1.amazonaws.com/msging.net/telegram/AgACAgEAAxUAAV7xgotCehzPDFlYal0lKxXBDuvVAAKrpzEbRS8lH4RWOGsfcZSP_w8UMAAEAQADAgADYwADzpwGAAEaBA";
                $co->source = ucfirst($canal);
                $co->save();



                $a = new Atendimento();
                $a->contato_id = $co->id;
                $a->status = "end";
                $a->classificacao_id = Classificacao::where("default_timeout", "=", false)->where("default_invalidas", "=", false)->inRandomOrder()->first()->id;
                $a->usuario_id = $atendente->id;
                $a->protocolo = time();
                $a->created_at = "$dt {$hora}:00";
                $a->updated_at = "$dt {$hora}:00";
                $a->datahora_inicio = "$dt {$hora}:00";
                $a->datahora_fila = "$dt {$hora}:10";
                $a->datahora_atende = "$dt {$hora}:20";
                $a->canal = $canal;
                $a->message_id = $faker->sha1;
                $a->remote_id = $co->identity;
                $a->equipe_id = $equipe->id;
                $a->save();


                $min = 10;
                $dir = 'in';
                for ($j = 0; $j < $qtdmsgs; $j++) {
                    $m = new Mensagem();
                    $m->atendimento_id = $a->id;
                    $m->direcao = $dir;
                    $m->type = 'text/plain';
                    $m->content = $faker->text($faker->numberBetween(5, 100));
                    $m->created_at = "$dt {$hora}:{$min}";
                    $m->updated_at = "$dt {$hora}:{$min}";
                    $m->save();

                    $a->datahora_termino = "$dt {$hora}:{$min}";
                    $a->save();


                    $dir = ($dir == 'in') ? 'out' : 'in';
                    $min++;
                }
            }
        }

        echo "\n\n";
    }
}
