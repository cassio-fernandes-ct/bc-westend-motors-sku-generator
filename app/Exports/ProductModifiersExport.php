<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductModifiersExport implements FromCollection, WithHeadings
{
    protected $data, $headings;

    public function __construct(array $data)
    {
        $this->data = $data;
        // $this->headings = $headings;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            "PRODUCT_ID",
            "PRODUCT_NAME",
            "OPTION_SET_COUNT",
            "OPTION_SET_MAX_VARIANTS",
            "OPTION_SET_1",
            "OPTION_SET_2",
            "OPTION_SET_3",
            "OPTION_SET_4",
            "OPTION_SET_5",
            "OPTION_SET_6",
            "OPTION_SET_7",
            "OPTION_SET_8",
            "OPTION_SET_9",
            "OPTION_SET_10",
        ];
    }
}
