<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 h1 style=" color: #102257 "><b>{{ ucfirst($type) }}</b> mèdica</h1>

    <p>S'ha afegit la documentació de la
        <b>{{ $type }} mèdica</b> del treballador
        <b>{{ $worker->first_name }} {{ $worker->last_name }}</b> de l'empresa
        <b>{{ $company->name }}</b>
    </p>

    <br /> <br /> <br />
    @include('emails.links.worker')

</div>
