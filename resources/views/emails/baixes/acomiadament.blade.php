<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style="color: #10225E">Acomiadament</h1>

    <p><b>{{ $company->name }}</b> Ha sol·licitat l'acomiadament amb data efectiva
        <b>{{ date_format(date_create_from_format('Y-m-d', $contract->contract_end_date), 'd-m-Y') }}</b>
    del treballador <b>{{ $worker->first_name }}</b> <b>{{ $worker->last_name }}</b>. Aquest treballador no havia gaudit de <b>{{$contract->not_enjoyed_vacancies}} dies de vacances</b></p>

    <br /><br /><br />
    @include('emails.links.worker')

</div>
