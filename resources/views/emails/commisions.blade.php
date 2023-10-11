<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Comisions</h1>
  


    @foreach ($commisions as $company => $workers)

        <h2>{{$company}}</h2>
        <div>

        <table style="margin: auto" >
                <tr>
                    <th  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">Treballador</th>
                    <th  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">Fecha</th>
                    <th  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">Tipus</th>
                    <th  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">Quantitat</th>
                    <th  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">Observacions</th>
                </tr>
                @foreach($workers as $worker => $cs)
                    @foreach($cs as $index => $c)
                    <tr>
                        @if($index === 0)
                            <td  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;" rowspan="{{count($cs)}}">{{$worker}}</td>
                        @endif
                        <td  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">{{date("d/m/Y",strtotime($c["start_date"]))}}</td>
                        <td  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">{{$c["type"]}}</td>
                        <td  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">{{number_format($c["import"], 2, ",", ".")}} €</td>
                        <td  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">{{$c["observation"]}}</td>
                    </tr>
                    @endforeach
                @endforeach
        </table>
        </div>

    @endforeach


    
</div>
