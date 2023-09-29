<?php

namespace Database\Seeders;

use App\Models\Agreement;
use Illuminate\Database\Seeder;

class AgreementTableSeeder extends Seeder
{
    public function run()
    {

        $seeds = [
            [
                'name' => 'CONVENI COLECTIU DEL SECTOR D\'EMPRESES D\'ENGINYERIA I OFICINES D\'ESTUDIS TECNICS',
                'help_name' => 'CONVENI D\'ENGENYERIA I OFICINES TECNIQUES',
                'days_of_holidays' => 23, 'holidays_type' => 'h'
            ],
            [
                'name' => 'CONVENI COL·LECTIU AGROPEQUARI DE CATALUNYA',
                'help_name' => 'CONVENI AGROPEQUARI',
                'days_of_holidays' => '',
                'holidays_type' => ''
            ],
            [
                'name' => 'CONVENI COLECTIU DEL SECTOR D\'EMPRESES DE PUBLICITAT',
                'help_name' => 'ONVENI COLECTIU DE PUBLICITAT',
                'days_of_holidays' => '', 'holidays_type' => ''
            ],
            [
                'name' => 'Conveni col·lectiu de treball del sector del comerç de Barcelona',
                'help_name' => 'CONVENI COMERÇ EN GENERAL BCN',
                'days_of_holidays' => '', 'holidays_type' => ''
            ],
            [
                "name" => "CONVENI COLECTIU DEL TREBALL DEL SECTOR D'OFICINES I DESPATXOS DE CATALUNYA",
                "help_name" => "CONVENI OFICINES I DESPATXOS",
                "days_of_holidays" => 23,
                "holidays_type" => "h"
            ],
            [
                "name" => "CONVENI COLECTIU NACIONAL DEL CICLE DE COMERÇ DE PAPER Y ARTS GRÀFIQUES",
                "help_name" => "CONVENI DEL COMERÇ DEL PAPER"
            ],
            [
                "name" => "CONVENI COL·LECTIU ESTATAL PER A LES EMPRESES DE GESTIÓ  I MEDIACIÓ IMMOBILIÀRIA",
                "help_name" => "CONVENI GESTIÓ I MEDIACIÓ IMMOBILÀRIA"
            ],
            [
                "name" => "CONVENI COL·LECTIU AUTONÒMIC D'ENSENYAMENT I FORMACIÓ NO REGLADA DE CATALUNYA",
                "help_name" => "CONVENI ENSENYAMENT I FORMACIÓ NO REGLADA"
            ],

        ];

        Agreement::insert($seeds);
    }
}


/*
  
    //1
        factory(\App\Models\Agreement::class)->create([
            "name" => "CONVENI COLECTIU DEL SECTOR D'EMPRESES D'ENGINYERIA I OFICINES D'ESTUDIS TECNICS",
            "help_name" =>     "CONVENI D'ENGENYERIA I OFICINES TECNIQUES",
            "days_of_holidays" => 23,
            "holidays_type" => "h"

        ]);
        //2
        factory(\App\Models\Agreement::class)->create([
            "name" => "CONVENI COL·LECTIU AGROPEQUARI DE CATALUNYA",
            "help_name" => "CONVENI AGROPEQUARI",
        ]);
        //3
        factory(\App\Models\Agreement::class)->create([
            "name" => "CONVENI COLECTIU DEL SECTOR D'EMPRESES DE PUBLICITAT",
            "help_name" => "CONVENI COLECTIU DE PUBLICITAT"
        ]);
        //4
        factory(\App\Models\Agreement::class)->create([
            "name" => "Conveni col·lectiu de treball del sector del comerç de Barcelona",
            "help_name" => " CONVENI COMERÇ EN GENERAL BCN "
        ]);
        //5
        factory(\App\Models\Agreement::class)->create([
            "name" => "Conveni col·lectiu interprovincial del sector de la indústria d’hostaleria i turisme de Catalunya",
            "help_name" => "CONVENI RESTAURACIÓ GIRONA"
        ]);
        //6
        factory(\App\Models\Agreement::class)->create([
            "name" => "CONVENI COLECTIU DEL TREBALL DEL SECTOR D'OFICINES I DESPATXOS DE CATALUNYA",
            "help_name" => "CONVENI OFICINES I DESPATXOS",
            "days_of_holidays" => 23,
            "holidays_type" => "h"
        ]);
        //7
        factory(\App\Models\Agreement::class)->create([
            "name" => "CONVENI COLECTIU NACIONAL DEL CICLE DE COMERÇ DE PAPER Y ARTS GRÀFIQUES",
            "help_name" => "CONVENI DEL COMERÇ DEL PAPER"
        ]);

        //8
        factory(\App\Models\Agreement::class)->create([
            "name" => "CONVENI COL·LECTIU ESTATAL PER A LES EMPRESES DE GESTIÓ  I MEDIACIÓ IMMOBILIÀRIA",
            "help_name" => "CONVENI GESTIÓ I MEDIACIÓ IMMOBILÀRIA"
        ]);
        //9
        factory(\App\Models\Agreement::class)->create([
            "name" => "CONVENI COL·LECTIU AUTONÒMIC D'ENSENYAMENT I FORMACIÓ NO REGLADA DE CATALUNYA",
            "help_name" => "CONVENI ENSENYAMENT I FORMACIÓ NO REGLADA"
        ]);
  
 */