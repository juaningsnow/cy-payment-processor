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
            if ($invoice->paid_by == 'Cash') {
                $accountCode = $invoice->company->cashAccount->code;
            } else {
                $accountCode = $invoice->companyOwner->account->code;
            }
            $body = [
                'Invoice' => [
                    'InvoiceId' => $invoice->xero_invoice_id
                ],
                "Account" => [
                    "Code" => $accountCode,
                ],
                "Date" => Carbon::now()->toDateString(),
                "Amount" => $invoice->amount_due,
            ];
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoice->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->put($this->baseUrl.'/Payments');
            $data = json_decode($response->getBody()->getContents());
            $this->syncPayments($this->getInvoice($invoice->xero_invoice_id, $invoice->company->xero_tenant_id));
            $this->syncCredits($this->getInvoice($invoice->xero_invoice_id, $invoice->company->xero_tenant_id));
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function makeBatchPayment(InvoiceBatch $invoiceBatch)
    {
        $accountId = Account::where('code', $invoiceBatch->company->getDefaultAccountCode())->where('company_id', $invoiceBatch->company->id)->first()->xero_account_id;
        $payToSupplier = $invoiceBatch->supplier()->exists() ? $invoiceBatch->supplier : null;
        try {
            $body = [
                "Account" => [
                    "AccountID" => $accountId,
                ],
                "Date" => Carbon::now()->toDateString(),
                "IsReconciled" => true,
                "Payments" => $this->assembleInvoiceBatchDetailForBatchPayment($invoiceBatch->invoiceBatchDetails->all(), $payToSupplier)
            ];
            $response = Http::withHeaders($this->getTenantDefaultHeaders($invoiceBatch->company->xero_tenant_id))->withBody(
                json_encode($body),
                'application/json'
            )->put($this->baseUrl.'/BatchPayments');
            $data = json_decode($response->getBody()->getContents());
            if (is_object($data)) {
                if (property_exists($data, 'BatchPayments')) {
                    $invoiceBatch->xero_batch_payment_id = $data->BatchPayments[0]->BatchPaymentID;
                    $invoiceBatch->save();
                }
            }
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
                "Amount" => $attribute->amount
            ];
        }, $invoiceBatchDetails);
    }

    public function getPayment($invoiceId, $tenantId)
    {
        $url = $this->baseUrl.'/Payments?where='.urlencode('Invoice.InvoiceId=guid("'.$invoiceId.'")');
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($tenantId))->get($url);
            $data = json_decode($response->getBody()->getContents());
            return $data;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }

    public function getBatchPayment($batchPaymentId, $tenantId)
    {
        $url = $this->baseUrl.'/BatchPayments/'.$batchPaymentId;
        try {
            $response = Http::withHeaders($this->getTenantDefaultHeaders($tenantId))->get($url);
            $data = json_decode($response->getBody()->getContents());
            return $data;
        } catch (Exception $e) {
            throw new GeneralApiException($e);
        }
    }
}
