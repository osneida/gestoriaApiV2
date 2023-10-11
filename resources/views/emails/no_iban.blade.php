<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Falten ibans de treballadors</h1>
    <p>Recordem que no s’ha informat el compte bancari dels treballadors. Per poder ingressar la nomina de cada
        treballador correctament i en el temps corresponent és imprescindible tenir aquesta informació actualitzada.</p>


    <p>No tenim el iban dels seguents treballadors: </p>
    <br /> <br /> <br />
    @foreach ($workers as $worker)

        @include('emails.links.worker_with_name')
    @endforeach



    @include('emails.firma')

</div>
