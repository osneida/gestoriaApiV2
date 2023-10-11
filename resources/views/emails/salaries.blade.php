<div style="background-color: #EEEEEE; padding: 50px; text-align: center;">
    @include('emails.logo')
    <h1 style=" color: #102257 ">Salaris</h1>
  


    @foreach ($salaries as $company => $workers)

        <h2>{{$company}}</h2>
        <div>

        <table style="margin: auto" >
                <tr>
                    <th  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">Treballador</th>
                    <th  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">Fecha</th>
                    <th  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">Salari</th>
                </tr>
                @foreach($workers as $worker => $ss)
                    @foreach($ss as $index => $s)
                    <tr>
                        @if($index === 0)
                            <td  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;" rowspan="{{count($ss)}}">{{$worker}}</td>
                        @endif
                        <td  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">{{date("m/Y",strtotime($s["start_date"]))}}</td>
                        <td  style="border-bottom: 1px solid grey; padding: 2px; margin: 0;">{{number_format($s["salary"], 2, ",", ".")}} €</td>
                    </tr>
                    @endforeach
                @endforeach
        </table>
        </div>

    @endforeach


    
</div>
