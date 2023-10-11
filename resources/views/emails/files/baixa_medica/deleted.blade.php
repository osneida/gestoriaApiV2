<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Nova baixa medica de treballador</h1>
    <p>
        S'ha eliminat una baixa medica del treballador <b>{{ $file->worker->name }}</b>
    </p>
    <br /> <br /> <br />
    @include('emails.links.worker')
</div>
