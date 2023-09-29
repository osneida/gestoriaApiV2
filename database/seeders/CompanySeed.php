<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $company = factory(\App\Models\Company::class)->create([
            "name" => "AITONA D'AGRICULTURA, S.L.",
            "slug" => Str::slug("AITONA D'AGRICULTURA, S.L.", "-"),
            "cif" => "B67266114",

        ]);
        factory(\App\Models\User::class)->create(["name" => "Cristina Abolacia", "company_id" => $company->id, "email" => "cristina@aitona-agricultura.com", "role_id" => \App\Models\User::COMPANY_ROLE]);


        $company->agreements()->sync([2]);

        $company = factory(\App\Models\Company::class)->create([
            "name" => "ALSI 02, S.L.",
            "slug" => Str::slug("ALSI 02, S.L.", "-"),
            "cif" => "B60334562"
        ]);


        $company->agreements()->sync([6]);

        $company = factory(\App\Models\Company::class)->create([
            "name" => "ATAS CORP, S.L.",
            "slug" => Str::slug("ATAS CORP, S.L.", "-"),
            "cif" => "B62270590"
        ]);

        $company->agreements()->sync([6]);

        $company = factory(\App\Models\Company::class)->create([
            "name" => "BROTHAPPS, S.L.",
            "slug" => Str::slug("BROTHAPPS, S.L.", "-"),
            "cif" => "B66696949"
        ]);
        factory(\App\Models\User::class)->create(["name" => "Jan Roures", "company_id" => $company->id, "email" => "jan@brothapps.com", "role_id" => \App\Models\User::COMPANY_ROLE]);

        $company->agreements()->sync([6]);


        $company = factory(\App\Models\Company::class)->create([
            "name" => "DIARMUID MAGOURTY",
            "slug" => Str::slug("DIARMUID MAGOURTY", "-"),
            "cif" => "X1900314P"
        ]);


        $company = factory(\App\Models\Company::class)->create([


            "name" => "EL RINCON DE IARI, S.L.",
            "slug" => Str::slug("EL RINCON DE IARI, S.L.", "-"),
            "cif" => "B55282420"
        ]);
        factory(\App\Models\User::class)->create(["name" => "Yarisel Rojas", "company_id" => $company->id, "email" => "yarirojasmonzon@gmail.com", "role_id" => \App\Models\User::COMPANY_ROLE]);
        $company->agreements()->sync([5]);

        $company = factory(\App\Models\Company::class)->create([

            "name" => "EMPORDA SUNFLOWERS, S.L.",
            "slug" => Str::slug("EMPORDA SUNFLOWERS, S.L.", "-"),
            "cif" => "B67220970"
        ]);
        $company->agreements()->sync([5]);
        factory(\App\Models\User::class)->create(["name" => "Max Roures", "company_id" => $company->id, "email" => "mroures@gmail.com", "role_id" => \App\Models\User::COMPANY_ROLE]);
        factory(\App\Models\User::class)->create(["name" => "Igor Bosch", "company_id" => $company->id, "email" => "igprbosch@gmail.com", "role_id" => \App\Models\User::COMPANY_ROLE]);


        $company = factory(\App\Models\Company::class)->create([
            "name" => "EXCAVACIONS GENIVETT, S.L.",
            "slug" => Str::slug("EXCAVACIONS GENIVETT, S.L.", "-"),
            "cif" => "B25827668"
        ]);

        $company->agreements()->sync([2]);
        //factory(\App\Models\User::class)->create(["name" => "Cristina Abolacia", "company_id" => $company->id, "email" => "", "role_id" => \App\Models\User::COMPANY_ROLE]);

        $company = factory(\App\Models\Company::class)->create([

            "name" => "GODINO GLOBAL MANAGEMENT, S.L.",
            "slug" => Str::slug("GODINO GLOBAL MANAGEMENT, S.L.", "-"),
            "cif" => "B66905852",
            
        ]);


        $company->agreements()->sync([6]);


        $company = factory(\App\Models\Company::class)->create([

            "name" => "IOMAR DO NASCIMENTO",
            "slug" => Str::slug("IOMAR DO NASCIMENTO", "-"),
            "cif" => "39452101V"
        ]);

        $company = factory(\App\Models\Company::class)->create([


            "name" => "LIFEHAUS REAL ESTATE, S.L.",
            "slug" => Str::slug("LIFEHAUS REAL ESTATE, S.L.", "-"),
            "cif" => "B66447533"
        ]);

        $company->agreements()->sync([6]);

        $company = factory(\App\Models\Company::class)->create([

            "name" => "MEDIACABLE SERVICIOS DE PRODUCCION, S.L.",
            "slug" => Str::slug("MEDIACABLE SERVICIOS DE PRODUCCION, S.L.", "-"),
            "cif" => "B61948444"
        ]);

        $company->agreements()->sync([6]);

        $company = factory(\App\Models\Company::class)->create([


            "name" => "MULTIAX-INVERA, S.L.",
            "slug" => Str::slug("MULTIAX-INVERA, S.L.", "-"),
            "cif" => "B64490204"
        ]);


        $company = factory(\App\Models\Company::class)->create([


            "name" => "ONA LLIBRES, S.L.",
            "slug" => Str::slug("ONA LLIBRES, S.L.", "-"),
            "cif" => "B66456781"
        ]);

        factory(\App\Models\User::class)->create(["name" => "Montse Ubeda", "company_id" => $company->id, "email" => "onallibres@gmail.com", "role_id" => \App\Models\User::COMPANY_ROLE]);

        $company->agreements()->sync([7]);


        $company = factory(\App\Models\Company::class)->create([

            "name" => "RAFAELO ANGELO BR51, S.L.",
            "slug" => Str::slug("RAFAELO ANGELO BR51, S.L.", "-"),
            "cif" => "B65655003"
        ]);

        $company->agreements()->sync([4]);

        $company = factory(\App\Models\Company::class)->create([

            "name" => "SHOGATSU, S.L.",
            "slug" => Str::slug("SHOGATSU, S.L.", "-"),
            "cif" => "B66911058",
            "active" => false
        ]);

        $company = factory(\App\Models\Company::class)->create([
            "name" => "THE PLAYER MANAGEMENT, S.L.",
            "slug" => Str::slug("THE PLAYER MANAGEMENT, S.L.", "-"),
            "cif" => "B66827940"
        ]);

        factory(\App\Models\User::class)->create(["name" => "Alex Boesch", "company_id" => $company->id, "email" => "alex@theplayer.es", "role_id" => \App\Models\User::COMPANY_ROLE]);

        $company->agreements()->sync([3]);

        $company = factory(\App\Models\Company::class)->create([

            "name" => "MTPALACIO SOLUTIONS, S.L.",
            "slug" => Str::slug("MTPALACIO SOLUTIONS, S.L.", "-"),
            "cif" => "B67474445",
            "workers_access" => true
        ]);
        factory(\App\Models\User::class)->create(["name" => "Marti Tapia", "company_id" => $company->id, "email" => "marti@tucody.com", "role_id" => \App\Models\User::COMPANY_ROLE]);

        $company->agreements()->sync([6]);

        $company = factory(\App\Models\Company::class)->create([

            "name" => "CBRV RENOVABLES, S.L.",
            "slug" => Str::slug("CBRV RENOVABLES, S.L.", "-"),
            "cif" => "B25844424"
        ]);

        factory(\App\Models\User::class)->create(["name" => "Roger Vidal", "company_id" => $company->id, "email" => "rogervidal@gestionservicios.com", "role_id" => \App\Models\User::COMPANY_ROLE]);

        $company->agreements()->sync([1]);


        $company = factory(\App\Models\Company::class)->create([

            "name" => "ADVANCED NANOTECHNOLOGY S.L.",
            "slug" => Str::slug("ADVANCED NANOTECHNOLOGY S.L.", "-"),
            "cif" => "xxx"
        ]);

        $company->agreements()->sync([1]);

        $company = factory(\App\Models\Company::class)->create([
            "name" => "COYOACAN INVEST, S.L.",
            "slug" => Str::slug("COYOACAN INVEST, S.L.", "-"),
            "cif" => "B67160028",
        ]);
        $company->agreements()->sync([8]);


        $company = factory(\App\Models\Company::class)->create([
            "name" => "NEGOFAM, S.L.",
            "slug" => Str::slug("NEGOFAM, S.L.", "-"),
            "cif" => "B60533510",
        ]);
        $company->agreements()->sync([8]);


        $company = factory(\App\Models\Company::class)->create([
            "name" => "KOMODO INVESTMENT, S.L.",
            "slug" => Str::slug("KOMODO INVESTMENT, S.L.", "-"),
            "cif" => "B65926313",
        ]);
        $company->agreements()->sync([6]);


        $company = factory(\App\Models\Company::class)->create([
            "name" => "MAGOURTY CORPORATE LANGUAGE SERVICE, S.L.",
            "slug" => Str::slug("MAGOURTY CORPORATE LANGUAGE SERVICE, S.L.", "-"),
            "cif" => "B01956051",
        ]);

        $company->agreements()->sync([8]);


        $company = factory(\App\Models\Company::class)->create([
            "name" => "MC SOCIAL NETWORK, S.L.",
            "slug" => Str::slug("MC SOCIAL NETWORK, S.L.", "-"),
            "cif" => "B67218834",
        ]);

        $company->agreements()->sync([3]);


    }
}
