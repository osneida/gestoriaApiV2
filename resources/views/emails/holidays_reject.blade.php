<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Vacances rebutjades</h1>
    <p><b>{{ $worker }}</b>:</p>
    <p>Les vacances sol·licitades del <b>{{ $start_date }}</b> al <b>{{ $end_date }}</b> han estat rebutjades per
        <b>{{ $approver }}</b>.
    </p>

    <br />

    <div>
        <a href="{{ env('CLIENT_URL') }}/#/my-holidays"
            style="background-color: transparent; color: #102257; border: #102257 1px solid; border-radius: 10px; padding: 15px; text-decoration: none; font-weight: bold">Accedeix
            al portal</a>
    </div>
</div>
