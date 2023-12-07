<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class WorkerHoursExport implements FromArray
{
    protected $wh;

    public function __construct(array $wh)
    {
        $this->wh = $wh;
    }

    public function array(): array
    {
        return $this->wh;
    }
}
