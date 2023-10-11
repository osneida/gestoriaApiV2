<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')

    <h1 style=" color: #102257 ">Alta treballador nou</h1>

    <p>L'empresa <b>{{ $company->name }}</b> ha sol·licitat l'alta del treballador <b>{{ $worker->first_name }}
            {{ $worker->last_name }}</b> amb data d'inici <b>{{ date('d-m-Y', strtotime($contract->contract_start_date)) }}</b></p>

    <br /> <br /> <br />
    @include('emails.links.worker')
</div>
