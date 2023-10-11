<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">{{ $subject }}</h1>


    <p>{{ $msg }}
    </p>
    <p>Gràcies</p>
    <br />
    @if ($adjuntar)
        <a href="{{ env('CLIENT_URL') }}/#/"
            style="background-color: transparent; color: #102257; border: #102257 1px solid; border-radius: 10px; padding: 15px; text-decoration: none; font-weight: bold">Anar al portal</a>
    @endif
</div>
