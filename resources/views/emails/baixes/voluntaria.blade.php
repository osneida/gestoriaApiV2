<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Baixa voluntaria</h1>

    <p>S'ha afegit la documentació de la baixa voluntaria
        comunicada el dia
        <b>{{ date_format(date_create_from_format('Y-m-d', $contract->contract_end_comunication_date), 'd-m-Y') }}</b>
        del treballador <b>{{ $worker->first_name }}</b> <b>{{ $worker->last_name }}</b>
        de la empresa <b>{{ $company->name }}</b>
        amb data efectiva <b>{{ date_format(date_create_from_format('Y-m-d', $contract->contract_end_date), 'd-m-Y') }}</b>. Aquest treballador no havia gaudit de <b>{{$contract->not_enjoyed_vacancies}} dies de vacances</b></p>

    <br /><br /><br />
    @include('emails.links.worker')

</div>
