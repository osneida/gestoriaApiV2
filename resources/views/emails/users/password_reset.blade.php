<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')

    <h1 style=" color: #102257 ">Canviar contrasenya portal:</h1>

    <p>Has solicitat canviar la contrasenya del nou
    </p>

    <div>
        <a href="{{ env('CLIENT_URL') }}/#/auth/password_reset/{{ $token }}/{{ $email }}"
            style="background-color: transparent; color: #102257; border: #102257 1px solid; border-radius: 10px; padding: 15px; text-decoration: none; font-weight: bold">Cambiar
            contrasenya</a>
    </div>

    <br /> <br /> <br />

    @include('emails.firma')
</div>
