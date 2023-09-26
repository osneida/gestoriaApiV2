<?php

use App\Models\Agreement;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //CONVENI D'ENGENYERIA I OFICINES TECNIQUES


        factory(\App\Models\Category::class)->create([
            "name" => "Llicenciats i titulats 2n i 3r cicle universitari i analista",
            "level" => "1",
            "salary" =>  23973.88,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Dimplomats i titulats 1r cicle universitari Cap Superior",
            "level" => "2",
            "salary" => 18074.56,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Programador d'ordinadors",
            "level" => "3",
            "salary" => 17429.02,
            "agreement_id" => 1
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic de càlcul o disseny",
            "level" => "3",
            "salary" => 17429.02,
            "agreement_id" => 1
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Cap de primera",
            "level" => "3",
            "salary" => 17429.02,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Deliniant-Projectista",
            "level" => "4",
            "salary" => 15979.04,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de segona",
            "level" => "4",
            "salary" => 15979.04,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Programador de maquines auxiliars",
            "level" => "4",
            "salary" => 15979.04,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Deliniant",
            "level" => "5",
            "salary" =>  14277.48,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic de primera",
            "level" => "5",
            "salary" =>  14277.48,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Oficial de primera administratiu",
            "level" => "5",
            "salary" =>  14277.48,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Operador d'ordinadors",
            "level" => "5",
            "salary" =>  14277.48,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Dibuixant",
            "level" => "6",
            "salary" =>  12300.82,
            "agreement_id" => 1
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic de segona",
            "level" => "6",
            "salary" =>  12300.82,
            "agreement_id" => 1
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Oficial de segona administratiu",
            "level" => "6",
            "salary" =>  12300.82,
            "agreement_id" => 1
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Perforista",
            "level" => "6",
            "salary" =>  12300.82,
            "agreement_id" => 1
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Gravador",
            "level" => "6",
            "salary" =>  12300.82,
            "agreement_id" => 1
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Conserge",
            "level" => "6",
            "salary" =>  12300.82,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Telefonista-Recepcionista",
            "level" => "7",
            "salary" => 11888.24,
            "agreement_id" => 1
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Vigilant",
            "level" => "7",
            "salary" => 11888.24,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar tècnic",
            "level" => "8",
            "salary" => 11065.04,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar administratiu",
            "level" => "8",
            "salary" => 11065.04,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Telefonista",
            "level" => "8",
            "salary" => 11065.04,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ordenança",
            "level" => "8",
            "salary" => 11065.04,
            "agreement_id" => 1
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Personal de neteja",
            "level" => "8",
            "salary" => 11065.04,
            "agreement_id" => 1
        ]);

        //AGRARIO


        factory(\App\Models\Category::class)->create([
            "name" => "Titulat de Grau superior",
            "level" => "1",
            "salary" =>  14708.32,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Titulat de Grau mig",
            "level" => "2",
            "salary" => 14032.39,
            "agreement_id" => 2
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap de Secció",
            "level" => "2",
            "salary" =>  19863.48,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap administratiu",
            "level" => "3",
            "salary" => 13347.21,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Encarregat capataç",
            "level" => "3",
            "salary" => 13347.21,
            "agreement_id" => 2
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Oficial administratiu",
            "level" => "4",
            "salary" =>  12712.95,
            "agreement_id" => 2
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Oficial oficis classics",
            "level" => "4",
            "salary" =>  12712.95,
            "agreement_id" => 2
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Tractorista/Maquinista",
            "level" => "4",
            "salary" =>  12712.95,
            "agreement_id" => 2
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Xofer",
            "level" => "4",
            "salary" =>  12712.95,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Venedor",
            "level" => "4",
            "salary" =>  12712.95,
            "agreement_id" => 2
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar administratiu",
            "level" => "5",
            "salary" => 12120.36,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Especialista",
            "level" => "5",
            "salary" => 12120.36,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Pastor",
            "level" => "5",
            "salary" => 12120.36,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Guardia",
            "level" => "5",
            "salary" => 12120.36,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Envasador",
            "level" => "5",
            "salary" => 12120.36,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de laboratori",
            "level" => "5",
            "salary" => 12120.36,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Peo",
            "level" => "6",
            "salary" => 11513.88,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Aspirant administratiu",
            "level" => "7",
            "salary" => 11161.15,
            "agreement_id" => 2
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant",
            "level" => "7",
            "salary" => 11161.15,
            "agreement_id" => 2
        ]);


        //PUBLICIDAD


        factory(\App\Models\Category::class)->create([
            "name" => "Director general",
            "level" => "1.1",
            "salary" => 21909.38,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director financer",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director administratiu",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director comercial",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director Creatiu Ejecutiu",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director de mitjans",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director de comptes",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director de Marketing",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director de qualitat",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director de tecnologia",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director de tràfic",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director de producció digital, audiovisual i gràfica",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director de recursos humans",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Responable de Mitjans Socials",
            "level" => "1.2",
            "salary" => 19475.46,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Supervisor de comptes",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Supervisor de trafic",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de planificació",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de compra de mitjans",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap d'adminsitració",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de personal",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap d'atenció al client",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Planificador estrategic",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director creatiu",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Director d'art Senior",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Redactor",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Encarregat de publicitat exterior",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Community Manager",
            "level" => "2.3",
            "salary" => 18557.57,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Encarregat de bases de dades",
            "level" => "2.4",
            "salary" => 18455.05,
            "agreement_id" => 3
        ]);



        factory(\App\Models\Category::class)->create([
            "name" => "Director d'art Junior",
            "level" => "3.4",
            "salary" => 18455.05,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Redactor",
            "level" => "3.4",
            "salary" => 18455.05,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Executiu de comptes senior",
            "level" => "3.4",
            "salary" => 18455.05,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Analista de dades",
            "level" => "3.4",
            "salary" => 18455.05,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Dissenyador Web",
            "level" => "3.4",
            "salary" => 18455.05,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Planificador de mitjans",
            "level" => "3.5",
            "salary" => 18390.13,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Comprador de mitjans",
            "level" => "3.5",
            "salary" => 18390.13,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic informatic",
            "level" => "3.5",
            "salary" => 18390.13,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap d'estudis",
            "level" => "3.5",
            "salary" => 18390.13,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant de producció",
            "level" => "3.6",
            "salary" => 17190.07,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Tecninc Artfinalista",
            "level" => "3.6",
            "salary" => 17190.07,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Programador",
            "level" => "3.7",
            "salary" =>  17060.42,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Executiu de comptes junior",
            "level" => "3.7",
            "salary" => 17060.42,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Dibuixant-Montador",
            "level" => "3.8",
            "salary" =>  15795.61,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Oficial informatic",
            "level" => "3.8",
            "salary" =>  15795.61,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Assistent de direcció",
            "level" => "4.7",
            "salary" => 16609.05,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Oficial Administratiu",
            "level" => "4.8",
            "salary" => 15377.71,
            "agreement_id" => 3
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar administratiu",
            "level" => "4.9",
            "salary" => 15427.26,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Oficial de primera de publicitat exterior",
            "level" => "5.7",
            "salary" => 16609.05,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Oficial d'oficis",
            "level" => "5.8",
            "salary" => 15367.71,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Oficial de segona de publicitat exterior",
            "level" => "5.8",
            "salary" => 15367.71,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Subaltern",
            "level" => "5.10",
            "salary" => 15367.84,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar d'oficina",
            "level" => "5.11",
            "salary" => 14489.95,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Personal de neteja",
            "level" => "5.11",
            "salary" => 14489.95,
            "agreement_id" => 3
        ]);



        factory(\App\Models\Category::class)->create([
            "name" => "Supervisor",
            "level" => "6.3",
            "salary" => 18066.59,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap de magatzem",
            "level" => "6.3",
            "salary" => 18066.59,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap de gravació",
            "level" => "6.8",
            "salary" => 15377.71,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Oficial de maquina",
            "level" => "6.8",
            "salary" => 15377.71,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Cap d'equip",
            "level" => "6.8",
            "salary" => 15377.71,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Transportista",
            "level" => "6.8",
            "salary" => 15377.71,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Teleoperador",
            "level" => "6.8",
            "salary" => 15377.71,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Manipulador",
            "level" => "6.9",
            "salary" => 15019.10,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Gravador",
            "level" => "6.9",
            "salary" => 15019.10,
            "agreement_id" => 3
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Distribuidor",
            "level" => "6.11",
            "salary" => 14106.59,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Emmagatzemador",
            "level" => "6.11",
            "salary" => 14106.59,
            "agreement_id" => 3
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant de maquines",
            "level" => "6.11",
            "salary" => 14106.59,
            "agreement_id" => 3
        ]);

        //Comerç BCN

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de recepció",
            "level" => "1",
            "salary" => 22155.84,
            "agreement_id" => 4
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap de cuina",
            "level" => "1",
            "salary" => 22155.84,
            "agreement_id" => 4
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap de sala o restaurant",
            "level" => "1",
            "salary" => 22155.84,
            "agreement_id" => 4
        ]);



        factory(\App\Models\Category::class)->create([
            "name" => "Segon cap de recepció",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 4
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Segon cap de cuina",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 4
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Segon cap de sala o restaurant",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 4
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Gerent de centre",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 4
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Encarregat/da general",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Recepcionista",
            "level" => "3",
            "salary" => 19863.48,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Administratiu/va",
            "level" => "3",
            "salary" => 19863.48,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cuiner",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cambrer",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Bàrman",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 4
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Sommelier",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 4
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Especialista de servei (animador/a turística, punxadiscos (DJ)…) ",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 4
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Encarregat/ada de manteniment i serveis auxiliars",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 4
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Reboster/a",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 4
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Caixer/a",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant de cuina",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant de cambrer",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant de barman",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant d'animació",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de recepció i consergeria (porter de cotxes, porter d'accessos…)",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 4
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de cuina (pinches de més de 18 anys…)",
            "level" => "6",
            "salary" => 16803.78,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Rentaplats",
            "level" => "7",
            "salary" => 16177.84,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de pisos i neteja ( netejador/a, Fregador/a…)",
            "level" => "7",
            "salary" => 16177.84,
            "agreement_id" => 4
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Pinches fins 18 anys",
            "level" => "8",
            "salary" => 10342.64,
            "agreement_id" => 4
        ]);

        //HOSTELERIA GIRONA

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de recepció",
            "level" => "1",
            "salary" => 22155.84,
            "agreement_id" => 5
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap de cuina",
            "level" => "1",
            "salary" => 22155.84,
            "agreement_id" => 5
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap de sala o restaurant",
            "level" => "1",
            "salary" => 22155.84,
            "agreement_id" => 5
        ]);



        factory(\App\Models\Category::class)->create([
            "name" => "Segon cap de recepció",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 5
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Segon cap de cuina",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 5
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Segon cap de sala o restaurant",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 5
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Gerent de centre",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 5
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Encarregat/da general",
            "level" => "2",
            "salary" => 21009.94,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Recepcionista",
            "level" => "3",
            "salary" => 19863.48,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Administratiu/va",
            "level" => "3",
            "salary" => 19863.48,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cuiner",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cambrer",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Bàrman",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 5
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Sommelier",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 5
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Especialista de servei (animador/a turística, punxadiscos (DJ)…) ",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 5
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Encarregat/ada de manteniment i serveis auxiliars",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 5
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Reboster/a",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 5
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Caixer/a",
            "level" => "4",
            "salary" => 18489.94,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant de cuina",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant de cambrer",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant de barman",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Ajudant d'animació",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de recepció i consergeria (porter de cotxes, porter d'accessos…)",
            "level" => "5",
            "salary" => 17004.54,
            "agreement_id" => 5
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de cuina (pinches de més de 18 anys…)",
            "level" => "6",
            "salary" => 16803.78,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Rentaplats",
            "level" => "7",
            "salary" => 16177.84,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de pisos i neteja ( netejador/a, Fregador/a…)",
            "level" => "7",
            "salary" => 16177.84,
            "agreement_id" => 5
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Pinches fins 18 anys",
            "level" => "8",
            "salary" => 10342.64,
            "agreement_id" => 5
        ]);
        //OFICINAS Y DESPACHOS


        factory(\App\Models\Category::class)->create([
            "name" => "Titulat de grau superior",
            "level" => "1",
            "salary" => 26331.68,
            "agreement_id" => 6
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Titulat de grau mig",
            "level" => "2",
            "salary" => 22161.52,
            "agreement_id" => 6
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap d'equips informatics, analistes, programadors d'ordinadors…",
            "level" => "3",
            "salary" => 21446.62,
            "agreement_id" => 6
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Cap de segona, programadors de maquines auxiliars, administradors test...",
            "level" => "3",
            "salary" => 20612.59,
            "agreement_id" => 6
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Encarregats",
            "level" => "4",
            "salary" => 19540.27,
            "agreement_id" => 6
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Conductor",
            "level" => "5",
            "salary" => 19063.68,
            "agreement_id" => 6
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Perforistes",
            "level" => "5",
            "salary" => 19063.68,
            "agreement_id" => 6
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Entrevistadors",
            "level" => "6",
            "salary" => 17514.74,
            "agreement_id" => 6
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Enquestadors",
            "level" => "6",
            "salary" => 17514.74,
            "agreement_id" => 6
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar",
            "level" => "6",
            "salary" => 14530.76,
            "agreement_id" => 6
        ]);




        factory(\App\Models\Category::class)->create([
            "name" => "Consergers",
            "level" => "7.1",
            "salary" => 14297.77,
            "agreement_id" => 6
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Peons",
            "level" => "7.1",
            "salary" => 14297.77,
            "agreement_id" => 6,
            "has_salary_by_hour" => true
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Netejadors",
            "level" => "7.3",
            "salary" => 12729.43,
            "agreement_id" => 6
        ]);

        //COMERCIO PAPEL


        factory(\App\Models\Category::class)->create([
            "name" => "Director General",
            "level" => "0",
            "salary" =>  23755.71,
            "agreement_id" => 7
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Director de Divisió",
            "level" => "0",
            "salary" =>   22340.57,
            "agreement_id" => 7
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Director de departament",
            "level" => "0",
            "salary" =>  21378.70,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Titulat de Grau superior",
            "level" => "1",
            "salary" =>  23419.77,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de compres",
            "level" => "1",
            "salary" =>  20020.21,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de personal",
            "level" => "1",
            "salary" =>  20020.21,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap d'informatica",
            "level" => "1",
            "salary" =>  20020.21,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap financer",
            "level" => "1",
            "salary" =>  20020.21,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Titulat de Grau mig",
            "level" => "2",
            "salary" => 20019.61,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Responsable de secció",
            "level" => "2",
            "salary" => 18208.25,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Comptable",
            "level" => "3",
            "salary" => 16849.73,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Oficial adminsitratiu",
            "level" => "3",
            "salary" => 15434.33,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Programador",
            "level" => "3",
            "salary" => 16849.73,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Administratiu-Atencio al client",
            "level" => "3",
            "salary" => 14811.16,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic de manteniment informatic",
            "level" => "3",
            "salary" => 14657.02,
            "agreement_id" => 7
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic de sistemes d'informació",
            "level" => "3",
            "salary" => 14811.16,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Dependent",
            "level" => "3",
            "salary" => 14811.16,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar administratiu",
            "level" => "4",
            "salary" =>  14189.14,
            "agreement_id" => 7
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Telefonista",
            "level" => "4",
            "salary" => 13934.26,
            "agreement_id" => 7
        ]);


        //IMMOBILARIA

        factory(\App\Models\Category::class)->create([
            "name" => "Director Gerent",
            "level" => "1",
            "salary" =>  18484.31,
            "agreement_id" => 8
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Director Administratiu",
            "level" => "1",
            "salary" =>  18484.31,
            "agreement_id" => 8
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Director Comercial",
            "level" => "1",
            "salary" =>  14200.00,
            "agreement_id" => 8
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Director/a d'Oficina",
            "level" => "1",
            "salary" =>  14200.00,
            "agreement_id" => 8
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic de Gestió d'Actius amb Titulació de Grau Superior",
            "level" => "2",
            "salary" =>  21328.08,

            "agreement_id" => 8
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic de Gestió d'Actius amb Titulació de Grau Mig",
            "level" => "2",
            "salary" =>  19905.69,
            "agreement_id" => 8
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic en Taxació d'Actius",
            "level" => "2",
            "salary" =>16351.51,
            "agreement_id" => 8
        ]);
        factory(\App\Models\Category::class)->create([
            "name" => "Tècnic en Sostenibilitat, Certificació i Eficiència Energètica",
            "level" => "2",
            "salary" => 16351.51,
            "agreement_id" => 8
        ]);



        factory(\App\Models\Category::class)->create([
            "name" => "Gestor de Màrketing i Comunicació",
            "level" => "3",
            "salary" =>  14150,
            "agreement_id" => 8
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Comercial Captadors Visitadors",
            "level" => "3",
            "salary" =>  14000,
            "agreement_id" => 8
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Comercial Financer",
            "level" => "3",
            "salary" =>  14000,
            "agreement_id" => 8
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Coordinador",
            "level" => "4",
            "salary" =>
            16351.51,
            "agreement_id" => 8
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Secretari",
            "level" => "4",
            "salary" =>
            16351.51,
            "agreement_id" => 8
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Oficial Administratiu",
            "level" => "4",
            "salary" =>
            16351.51,
            "agreement_id" => 8
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Administració",
            "level" => "4",
            "salary" =>  14937.29,
            "agreement_id" => 8
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Oficial de Serveis",
            "level" => "5",
            "salary" =>  14218.72,
            "agreement_id" => 8
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de Serveis",
            "level" => "5",
            "salary" =>
            14000,
            "agreement_id" => 8
        ]);

        //CONVENI ENSENYAMENT I FORMACIÓ NO REGLADA

        factory(\App\Models\Category::class)->create([
            "name" => "Professor",
            "level" => "1",
            "salary" =>  18466.00,
            "agreement_id" => 9
        ]);


        factory(\App\Models\Category::class)->create([
            "name" => "Expert",
            "level" => "2",
            "salary" => 24584.00,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Orientador professional",
            "level" => "2",
            "salary" => 16926,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Consultor",
            "level" => "2",
            "salary" => 18466,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Informàtic",
            "level" => "2",
            "salary" => 18466,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Bibliotecari",
            "level" => "2",
            "salary" => 16926,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Intèrpret/Traductor",
            "level" => "2",
            "salary" => 16926,
            "agreement_id" => 9
        ]);



        factory(\App\Models\Category::class)->create([
            "name" => "Cap d'Administració",
            "level" => "3",
            "salary" => 18844,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Responsable de Secció",
            "level" => "3",
            "salary" => 17458,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Oficial Administratiu",
            "level" => "3",
            "salary" =>
            15148,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar Administratiu",
            "level" => "3",
            "salary" =>
            13678,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Cap de Serveis Generals",
            "level" => "4",
            "salary" =>
            17514,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Oficial de Serveis",
            "level" => "4",
            "salary" =>
            15176,
            "agreement_id" => 9
        ]);

        factory(\App\Models\Category::class)->create([
            "name" => "Auxiliar de Serveis",
            "level" => "4",
            "salary" =>
            14560,
            "agreement_id" => 9
        ]);


        $agreements = Agreement::get();

        foreach ($agreements as $a) {
            factory(\App\Models\Category::class)->create([
                "name" => "Becari",
                "level" => "-",
                "salary" => 0,
                "agreement_id" => $a->id
            ]);
            factory(\App\Models\Category::class)->create([
                "name" => "Administrador",
                "level" => "-",
                "salary" => 0,
                "agreement_id" => $a->id
            ]);
        }
    }
}
