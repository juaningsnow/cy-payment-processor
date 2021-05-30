<?php

namespace App\Utils;

use App\Models\InvoiceBatch;
use App\Models\InvoiceBatchDetail;
use App\Models\Supplier;
use App\Models\User;

class InvoiceBatchTextFileGenerator
{
    public static function generate(InvoiceBatch $batch, User $user)
    {
        $heading = static::generateHeadingLine($batch, $user);
        $lines = static::generateLineDetails($batch, $user);
        $combined = array_merge([$heading], $lines);
        $finalOutput = implode("\n", $combined);
        return $finalOutput;
    }

    private static function generateLineDetails(InvoiceBatch $batch, User $user)
    {
        $lines = [];
        $supplierIds = $batch->getInvoiceBatchDetails()->map(function ($detail) {
            return $detail->invoice->supplier->id;
        })->unique();
        $grouped = static::groupBySupplierWithTotal($supplierIds, $batch->id);
        
        foreach ($grouped as $group) {
            $lines[] = static::makeTransactionLine($group);
            foreach ($group->details as $detail) {
                $lines[] = static::makeTransactionDetailLine($detail);
            }
        }
        return $lines;
    }

    private static function makeTransactionDetailLine($detail)
    {
        $prefix = "INV";
        $description = "Repayment to {$detail->invoice->supplier->name} [{$detail->invoice->invoice_number}] Amt: {$detail->invoice->amount}";
        $desc = substr(static::rightPaddingGenerator($description, " ", 97), 0, 97);
        $finalOutput = substr($prefix.$desc, 0, 100);
        return $finalOutput;
    }

    private static function makeTransactionLine($group)
    {
        $bankCode = substr($group->supplier->bank->swift, 0, 11);
        $accountNumber = substr(static::rightPaddingGenerator($group->supplier->account_number, " ", 34), 0, 34);
        $supplierName = substr(static::rightPaddingGenerator($group->supplier->name, " ", 140), 0, 140);
        $filler1 = substr(static::rightPaddingGenerator(" ", " ", 3), 0, 3);
        $amount = substr(static::rightPaddingGenerator($group->total * 100, " ", 17), 0, 17);
        $pymntD = substr(static::rightPaddingGenerator($group->remarks, " ", 35), 0, 35);
        $purpose = substr(static::rightPaddingGenerator($group->supplier->purpose->name, " ", 4), 0, 4);
        $filler2 = substr(static::rightPaddingGenerator(" ", " ", 315), 0, 315);
        $e = "E";
        $email = substr(static::rightPaddingGenerator($group->supplier->email, " ", 255), 0, 255);
        $filler3 = substr(static::rightPaddingGenerator(" ", " ", 185), 0, 185);
        $combination =
            $bankCode.
            $accountNumber.
            $supplierName.
            $filler1.
            $amount.
            $pymntD.
            $purpose.
            $filler2.
            $e.
            $email.
            $filler3;
        return substr($combination, 0, 1000);
    }


    private static function groupBySupplierWithTotal($supplierIds, $batchId)
    {
        $batch = InvoiceBatch::find($batchId);
        $data = [];
        foreach ($supplierIds as $id) {
            $batchDetails = InvoiceBatchDetail::supplierId($id)->where('invoice_batch_id', $batchId)->get();
            $totalAmount = $batchDetails->sum(function ($detail) {
                return $detail->invoice->amount;
            });
            $supplier = Supplier::find($id);
            $data[] = (object)[
                'supplier' => $supplier,
                'total' => $totalAmount,
                'details' => $batchDetails,
                'remarks' => "Payment to {$supplier->name} batch {$batch->batch_name}."
            ];
        }
        return $data;
    }

    private static function generateHeadingLine(InvoiceBatch $batch, User $user)
    {
        $transactionTypeCode = "10";
        $filler1 = static::rightPaddingGenerator(" ", " ", 11);
        $originatingBankCode = substr($user->bank->swift, 0, 11);
        $accountNumber = static::rightPaddingGenerator($user->account_number, " ", 34);
        $filler2 = static::rightPaddingGenerator(" ", " ", 147);
        $clearing = "FAST";
        $referenceNumber = static::rightPaddingGenerator($batch->batch_name, " ", 16);
        $date = $batch->date->format('dmY');
        $combinedHeadingLine =
            substr($transactionTypeCode, 0, 2).
            substr($filler1, 0, 11).
            substr($originatingBankCode, 0, 11).
            substr($accountNumber, 0, 34).
            substr($filler2, 0, 145).
            substr($clearing, 0, 4).
            substr($referenceNumber, 0, 16).
            substr($date, 0, 8);
        return substr(static::rightPaddingGenerator($combinedHeadingLine, " ", 1000), 0, 1000);
    }

    private static function rightPaddingGenerator($value, $padding, $length)
    {
        return str_pad($value, $length, $padding, STR_PAD_RIGHT);
    }
}
