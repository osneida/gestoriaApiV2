<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Falten mail de treballadors</h1>
    <p>Recordem que no s’ha informat del email dels treballadors. Es obligatori i necessari tenir aquesta informació
        actualitzada per a realitzar futurs tràmits.</p>


    <p>No tenim el email dels seguents treballadors: </p>

    @foreach ($workers as $worker)

        @include('emails.links.worker_with_name')
    @endforeach


    @include('emails.firma')
</div>
