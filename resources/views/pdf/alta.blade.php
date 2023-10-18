<!DOCTYPE html>
<html>

<head></head>

<body>

    @include('pdf.firma')
    <h1>Alta treballador</h1>
    <p><strong>Nom i cognoms:</strong> {{ $worker->first_name }} {{ $worker->last_name }}</p>
    <p><strong>DNI/NIE/Passaport nº: </strong>{{ $worker->dni }}</p>
    <p><strong>Seguretat Social: </strong> {{ $contract->nss }}</p>
    <p><strong>Empresa: </strong>{{ $contract->company->name }}</p>
    <p><strong>Data inici:</strong>
        {{ date('d-m-Y', strtotime($contract->contract_start_date)) }}
    </p>
    <p><strong>Adreça:</strong>
        @if ($contract->address)
            {{ $contract->address }} {{ $contract->number }} {{ $contract->pis }} {{ $contract->porta }}
            {{ $contract->postal_code }} {{ $contract->city }}
        @else
            Mateixa que el DNI
        @endif
    </p>
    <p>
        <strong>Data de finalitzacio: </strong>
        @if ($contract->contract_end_date)
            {{ date('d-m-Y', strtotime($contract->contract_end_date)) }}
        @else
            -
        @endif
    </p>
    <p><strong>Tipus de contracte: </strong>

        @switch($contract->contract_type)
            @case(1)
                Indefinit
            </p>
        @break

        @case(2)
            Indefinit discontinu</p>
            <p><strong>Motiu: </strong>{{ $contract->contract_reason }}</p>
        @break

        @case(3)
            Temporal @if ($contract->temporal_comment)
                - {{ $contract->temporal_comment }}
            @endif
            </p>
            <p><strong>Motiu: </strong>{{ $contract->contract_reason }}</p>
        @break

        @case(4)
            Obra i servei</p>

            <p><strong>Motiu: </strong>{{ $contract->contract_reason }}</p>
        @break

        @case(5)
            Becari (Conveni escoles)</p>
        @break

        @default
            </p>
    @endswitch
    <p><strong>Te estudis universitaris: </strong> {{ $worker->university ? 'Si' : 'No' }}</p>

    <p><strong>Jornada: </strong>
        @switch($contract->working_day_type)
            @case(1)
                Completa
            </p>
        @break

        @case(2)
            Parcial </p>
            <p> <strong>Horas totales: </strong> {{ $contract->total_hours }} </p>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td width="50"></td>
                    <td width="50" style="border: 1px solid black;">Dilluns</td>
                    <td width="50" style="border: 1px solid black">Dimarts</td>
                    <td width="50" style="border: 1px solid black">Dimecres</td>
                    <td width="50" style="border: 1px solid black">Dijous</td>
                    <td width="50" style="border: 1px solid black">Divendres</td>
                    <td width="50" style="border: 1px solid black">Dissabte</td>
                    <td width="50" style="border: 1px solid black">Diumenge</td>
                </tr>
                <tr>
                    <td width="50" style="border: 1px solid black">Mati</td>
                    @for ($i = 1; $i <= 7; $i++)
                        <td width="50" style="border: 1px solid black; font-size: 14px; text-align:center;">
                            {{ json_decode($contract->hours_worked_start, true)['morning' . $i] }} -
                            {{ json_decode($contract->hours_worked_end, true)['morning' . $i] }}
                        </td>
                    @endfor
                </tr>
                <tr>
                    <td width="50" style="border: 1px solid black">Tarda</td>


                    @for ($i = 1; $i <= 7; $i++)
                        <td width="50" style="border: 1px solid black; font-size: 14px; text-align:center;">
                            {{ json_decode($contract->hours_worked_start, true)['afternoon' . $i] }} -
                            {{ json_decode($contract->hours_worked_end, true)['afternoon' . $i] }}
                        </td>
                    @endfor
                </tr>
                <tr>
                    <td width="50" style="border: 1px solid black">Matinada</td>


                    @for ($i = 1; $i <= 7; $i++)
                        <td width="50" style="border: 1px solid black; font-size: 14px; text-align:center;">
                            {{ json_decode($contract->hours_worked_start, true)['early_morning' . $i] }} -
                            {{ json_decode($contract->hours_worked_end, true)['early_morning' . $i] }}
                        </td>
                    @endfor
                </tr>
            </table>
        @break

        @default
    @endswitch
    <p>
        <strong>Categoria: </strong>
        @if ($contract->category !== null)
            {{ $contract->category->name }}
        @endif

    </p>
    <p>
        @if ($contract->salary != null)
            <strong>Sou brut anual:</strong> {{ number_format($contract->salary, 2, ',', '.') }} €
        @endif
        @if ($contract->salary_by_hour != null)
            <strong>Sou brut per hora:</strong> {{ number_format($contract->salary_by_hour, 2, ',', '.') }} €/h
        @endif
    </p>
    <p>
        <strong>Numero de pagues: </strong>
        {{ $contract->number_of_payments }}
    </p>

    <p>
        <strong>Iban: </strong>
        {{ $contract->iban }}
    </p>

    <p>
        <strong>Observacions: </strong>
        {{ $contract->observations ?? '-' }}
    </p>
</body>

</html>
