<?php

namespace App\Http\Interpreters\Traits;

use App\Models\Account;
use App\Models\Invoice;
use App\Models\InvoiceBatch;
use BaseCode\Common\Exceptions\GeneralApiException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

trait PaymentTrait
{
    public function makePayment(Invoice $invoice)
    {
        try {
            $body = [
                'Invoice' => [
                    'InvoiceId' => $invoice->xero_invoice_id
                ],
                "Account" => [
                    "Code" => $invoice->company->getDefaultAccountCode(),
                ],
                "Date" => Carbon::now()->toDateString(),
                "Amount" => $invoice->amount,
            ];
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoice->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->put($this->baseUrl.'/Payments');
            $data = json_decode($response->getBody()->getContents());
            $invoice->xero_payment_id = $data->Payments[0]->PaymentID;
            $invoice->save();
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function makeBatchPayment(InvoiceBatch $invoiceBatch)
    {
        $accountId = Account::where('code', $invoiceBatch->company->getDefaultAccountCode())->first()->xero_account_id;
        $payToSupplier = $invoiceBatch->supplier()->exists() ? $invoiceBatch->supplier : null;
        try {
            $body = [
                "Account" => [
                    "AccountID" => $accountId,
                ],
                "Date" => Carbon::now()->toDateString(),
                "Details" => $invoiceBatch->batch_name,
                "Payments" => $this->assembleInvoiceBatchDetailForBatchPayment($invoiceBatch->invoiceBatchDetails->all(), $payToSupplier)
            ];
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoiceBatch->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->put($this->baseUrl.'/BatchPayments');
            $data = json_decode($response->getBody()->getContents());
            $invoiceBatch->xero_batch_payment_id = $data->BatchPayments[0]->BatchPaymentID;
            $invoiceBatch->save();
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function cancelBatchPayment(InvoiceBatch $invoiceBatch)
    {
        try {
            $body = [
                "Status" => "DELETED"
            ];
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoiceBatch->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->post($this->baseUrl.'/BatchPayments/'.$invoiceBatch->xero_batch_payment_id);
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    private function assembleInvoiceBatchDetailForBatchPayment(array $invoiceBatchDetails, $payToSupplier = null)
    {
        return array_map(function ($attribute) use ($payToSupplier) {
            return [
                "BankAccountNumber" => $payToSupplier ? $payToSupplier->account_number."_".$payToSupplier->bank->swift : $attribute->invoice->supplier->account_number."_".$attribute->invoice->supplier->bank->swift,
                "Invoice" => [
                    "InvoiceID" => $attribute->invoice->xero_invoice_id,
                ],
                "Details" => $attribute->invoice->invoice_number,
                "Amount" => $attribute->invoice->amount
            ];
        }, $invoiceBatchDetails);
    }
}
