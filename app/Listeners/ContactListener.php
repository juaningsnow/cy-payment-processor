<?php

namespace App\Listeners;

use App\Http\Interpreters\XeroInterpreter;
use App\Models\Account;
use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ContactListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $xeroInterpreter = resolve(XeroInterpreter::class);
        $contact = $xeroInterpreter->getContact($event->contactId);
        if ($contact->ContactStatus == "ARCHIVED") {
            $this->deleteSupplier($contact->ContactID);
        }
        if ($contact->ContactStatus == "ACTIVE") {
            if ($event->isCreate) {
                $this->createSupplier(
                    $contact->Name,
                    $contact->EmailAddress,
                    $contact->ContactID,
                    $contact->BankAccountDetails ? $contact->BankAccountDetails : null,
                    $contact->PurchasesDefaultAccountCode ? $contact->PurchasesDefaultAccountCode : null
                );
            } else {
                $contact = $xeroInterpreter->getContact($event->contactId);
                $this->updateSupplier(
                    $contact->Name,
                    $contact->EmailAddress,
                    $contact->ContactID,
                    $contact->BankAccountDetails ? $contact->BankAccountDetails : null,
                    $contact->PurchasesDefaultAccountCode ? $contact->PurchasesDefaultAccountCode : null
                );
            }
        }
    }

    private function deleteSupplier($xeroId)
    {
        $supplier = Supplier::where('xero_contact_id', $xeroId)->first();
        if ($supplier) {
            $supplier->delete();
        }
    }

    private function createSupplier($name, $email, $xeroContactId, $accountNumber = null, $accountCode = null)
    {
        $account = Account::where('code', $accountCode)->first();
        $company = Company::first();
        $supplier = new Supplier();
        $supplier->fromXero = true;
        $supplier->name = $name;
        $supplier->payment_type = "FAST";
        $supplier->email = $email;
        $supplier->xero_contact_id = $xeroContactId;
        $supplier->company_id = $company->getId();
        $supplier->account_id = $account->id;
        $supplier->save();
    }
    
    private function updateSupplier($name, $email, $xeroContactId, $accountNumber = null, $accountCode = null)
    {
        $supplier = Supplier::where('xero_contact_id', $xeroContactId)->first();
        $account = Account::where('code', $accountCode)->first();
        $company = Company::first();
        if (!$supplier) {
            $this->createSupplier($name, $email, $xeroContactId, $accountNumber, $accountCode);
        } else {
            $supplier->fromXero = true;
            $supplier->name = $name;
            $supplier->payment_type = "FAST";
            $supplier->email = $email;
            $supplier->xero_contact_id = $xeroContactId;
            $supplier->company_id = $company->getId();
            $supplier->account_id = $account->id;
            $supplier->save();
        }
    }
}
