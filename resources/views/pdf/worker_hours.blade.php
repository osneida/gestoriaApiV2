<!DOCTYPE html>

<html>

<head></head>

<body>
    <table>
        <thead>
            <tr>
                @foreach ($tableComplete['header'] as $w)

                    <td>{{ $w }}</td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($tableComplete['body'] as $line)
                <tr>
                    @foreach ($line as $key => $l)
                        @if ($key === 0)
                            <td>{{ date('d-m-Y', strtotime($l)) }}</td>
                        @else
                            <td>{{ $l }}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>FOOTER</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
