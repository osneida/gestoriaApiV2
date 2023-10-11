<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">

    @include('emails.logo')
    <h1 style=" color: #102257 ">Modificació de contracte</h1>

    <p>L'empresa <b>{{ $company->name }}</b>
        @switch($contract->end_motive)
        @case(1)
            ha modificat les hores del contracte
        @break
        @case(2)
            ha renovat el contracte
        @break
        @case(3)
            ha transformat el contracte
        @break
@endswitch


         del treballador <b>{{ $worker->first_name }}</b>
        <b>{{ $worker->last_name }}</b> amb data efectiva <b>{{ date_format(date_create_from_format('Y-m-d', $contract->contract_start_date), 'd-m-Y') }}</b></p>

  <br /> <br /> <br />
    @include('emails.links.worker')
    @include('emails.firma')
</div>
