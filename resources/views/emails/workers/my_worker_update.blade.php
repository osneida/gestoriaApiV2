<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Modificacio dades mestre treballador</h1>

    <p>El treballador <b>{{ $worker->full_name_with_dni }}</b> ha modificat el seu {{ $camp }}</p>
    <p>El seu nou {{ $camp }} es
        @if ($camp == 'address')
            <b>{{ $value['address'] }} {{ $value['number'] }} {{ $value['pis'] }} {{ $value['porta'] }},
                {{ $value['city'] }}</b>.
        @else
            <b>{{ $value }}</b>.
        @endif
    </p>
    <br />
    @include('emails.links.worker')

</div>
