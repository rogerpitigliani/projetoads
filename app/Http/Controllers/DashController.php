<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashController extends Controller
{
    public function index()
    {

        // dd(request()->segment(1));

        return view('dashboard');
    }

    public function data(Request $request)
    {
        $qtde_canal = ["facebook" => 0, "telegram" => 0, "chat" => 0, "whatsapp" => 0];
        $qtde_equipes = [];


        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if ($request->get('start')) {
            $start = $request->get('start');
            $end = $request->get('end');
        }

        // *** QTDE CANAL ***
        $res = DB::select("select
        a.canal,
        count(1) as qtde
    from atendimento a
    where ( datahora_inicio between '{$start}' and '{$end}')
    and (status = 'end')
    group by a.canal;");

        $total = 0;

        foreach ($res as $reg) {
            $qtde_canal[$reg->canal] = $reg->qtde;
            $total += $reg->qtde;
        }

        // *** QTDE EQUIPE ***
        $res = DB::select("select
            e.equipe,
            count(1) as qtde
        from atendimento a
        join equipe e on e.id = a.equipe_id
        where ( datahora_inicio between '{$start}' and '{$end}')
        and (status = 'end')
        group by e.equipe
        order by qtde desc;
        ");

        $equipes_values = [];
        $equipes_labels = [];

        foreach ($res as $reg) {
            $equipes_values[] = $reg->qtde;
            $equipes_labels[] = $reg->equipe;
        }



        $resclassif = DB::select("select
                    c.classificacao ,
                    count(1) as qtde
                from atendimento a
                join classificacao c on c.id = a.classificacao_id
                where ( a.datahora_inicio between '{$start}' and '{$end}')
                and (a.status = 'end')
                group by c.classificacao
                order by qtde desc;");

        $restipoclassif = DB::select("select
                    c.tipo ,
                    count(1) as qtde
                from atendimento a
                join classificacao c on c.id = a.classificacao_id
                where ( a.datahora_inicio between  '{$start}' and '{$end}')
                and (a.status = 'end')
                group by c.tipo
                order by qtde desc;
                ");

        $classif_values = [];
        $classif_labels = [];

        foreach ($resclassif as $reg) {
            $classif_values[] = $reg->qtde;
            $classif_labels[] = $reg->classificacao;
        }

        // *** TOP 10 Usuarios
        $top10usuarios = DB::select("select
                    u.login ,
                    count(1) as qtde
                from atendimento a
                join usuario u on u.id = a.usuario_id
                where ( a.datahora_inicio between '{$start}' and '{$end}')
                and (a.status = 'end')
                group by u.login
                order by qtde desc
                limit 10;");


        $resfila = DB::select("select
            count(1) as qtde
        from atendimento a
        where ( status = 'fila' );");

        $reschat = DB::select("select
            count(1) as qtde
        from atendimento a
        where ( status = 'chat' );");

        $qtdfila = $resfila[0]->qtde;
        $qtdchat = $reschat[0]->qtde;


        $retorno = [
            "qtde" => $qtde_canal,
            "equipe_values" => $equipes_values,
            "equipe_labels" => $equipes_labels,
            "classif_values" => $classif_values,
            "classif_labels" => $classif_labels,
            "top10usuario" => $top10usuarios,
            "classif" => $resclassif,
            "tipoclassif" => $restipoclassif,
            "total" => $total,
            "fila" => $qtdfila,
            "chat" => $qtdchat,

        ];




        return response()->json($retorno);
    }
}
