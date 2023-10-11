<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Anul·lació de vacances</h1>
    <p>El treballador <b>{{ $worker }}</b> ha anul·lat les vacances del <b>{{ $start_date }}</b> al
        <b>{{ $end_date }}</b>.
    </p>
    <br />

    <div>
        <a href="{{ env('CLIENT_URL') }}/#/holidays"
            style="background-color: transparent; color: #102257; border: #102257 1px solid; border-radius: 10px; padding: 15px; text-decoration: none; font-weight: bold">Accedeix
            al portal</a>
    </div>
</div>
