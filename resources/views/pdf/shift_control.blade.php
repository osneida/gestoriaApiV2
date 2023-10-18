<!DOCTYPE html>

<html>

<head>
    <style>
        table td {
            border: black 1px solid;
            min-height: 100px;
        }

        p {
            margin-bottom: 0;
        }

        .date span {
            background-color: grey
                /*#0000FF*/
            ;
            padding: 2px;
            color: white;
            display: inline-block;
            border-radius: 100%;
            width: 20px;
            height: 20px;
            margin-top: 10px
        }

        .start {
            color: green;
        }

        .end {
            color: red;
        }

        td .info {

            width: 100px;
        }

        .firma {
            display: inline-block;
            width: 49%;
        }

        .firma div {
            border: black 1px solid;
            height: 60px;
            width: 100%;
        }

    </style>
</head>

<body>
    <section>

        <h2>Jornada de {{ $info['worker_name'] }} de {{ $info['month'] }}</h2>
        <table>
            <thead>
                <tr>
                    <th> Dilluns </th>
                    <th> Dimarts</th>
                    <th> Dimecres</th>
                    <th> Dijous</th>
                    <th> Divendres</th>
                    <th> Dissabte</th>
                    <th> Diumenge</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($weeks as $week)
                    <tr>
                        @foreach ($week as $day)
                            <td>
                                @if (isset($day['date']))
                                    <div class="info"
                                        style="height: {{$info['max'] > 4 ? $info['max'] * 28 : 112 }}px;">
                                        <div class="date">
                                            <span>{{ date('d', strtotime($day['date'])) }}</span>
                                            {{ number_format($day['dif'], 2) }} h
                                        </div>
                                        @foreach ($day['sc'] as $sc)
                                            <span>
                                                {{ $sc['hour'] }}
                                                <span
                                                    class="{{ $sc['action'] }}">{{ $sc['action'] == 'start' ? 'Entrar' : 'Salir' }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-bottom: 5px">
            <p>
                Hores Totals del mes: {{ number_format($info['total'], 2) }} h
            </p>
        </div>
        <div>
            <div class="firma">
                <p>Firma del treballador</p>
                <div></div>
            </div>
            <div class="firma">
                <p>Firma de la empresa</p>
                <div></div>
            </div>
        </div>
    </section>

</body>

</html>
