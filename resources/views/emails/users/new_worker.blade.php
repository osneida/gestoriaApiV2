<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')

    <h1 style=" color: #102257 ">Usuari acces portal del treballador</h1>
    <h1 style=" color: #102257 ">Usuario de acceso al portal del trabajador</h1>
    @if ($mod)
        <p>
            Se t'ha reiniciat la contraseña per a que puguis accedir al portal del treballador. els teus accesos son els
            seguents:
        </p>
        <p>
            Se te ha reiniciado la contraseña para que puedas acceder al portal del trabajador. tus accesos son los
            siguientes:
        </p>
    @else
        <p>
            Se t'ha creat un usuari per a que puguis accedir al portal del treballador. els teus accesos son els
            seguents:
        </p>
        <p>
            Se ha creado un usuario para que puedas acceder al portal del trabajador. tus accesos son los
            siguientes:
        </p>
    @endif
    <div>
        <p><strong>Usuari/Usuario: </strong>
            {{ $user->email }}</p>
        <p><strong>Contrasenya/Contraseña: </strong>
            {{ $password }}</p>
    </div>
    <br />
    <div>
        <a href="{{ env('CLIENT_URL') }}"
            style="background-color: transparent; color: #102257; border: #102257 1px solid; border-radius: 10px; padding: 15px; text-decoration: none; font-weight: bold">Accedeix/Accede
            </a>
    </div>

    <br /> <br /> <br />

    @include('emails.firma')
</div>
