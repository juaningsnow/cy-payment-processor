<?php

namespace App\Exports;

use App\Models\InvoiceBatchDetail;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SummaryExport implements FromCollection, WithHeadings, WithMapping, Responsable
{
    private $dateFrom;
    private $dateTo;
    use Exportable;


    public function __construct($dateFrom, $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function headings(): array
    {
        return [
            'Supplier',
            'Account Number',
            'Swift Code',
            'Amount',
            'Invoice Number',
            'Payment Type'
        ];
    }

    public function map($detail): array
    {
        return [
            $detail->getSupplier()->getName(),
            $detail->getSupplier()->getAccountNumber(),
            $detail->getSupplier()->getSwiftCode(),
            $detail->getAmount(),
            $detail->getInvoiceNumber(),
            $detail->getSupplier()->getPaymentType()
        ];
    }

    public function collection()
    {
        return InvoiceBatchDetail::dateFrom($this->dateFrom)->dateTo($this->dateTo)->get();
    }
}
