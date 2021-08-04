@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="invoice" v-cloak>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="row align-items-center" v-if="!initializationComplete" style="height: 100px">
                    <div class="col-12 text-center h4">
                        <i class="fas fa-circle-notch fa-spin"></i> Initializing...
                    </div>
                </div>
                <div v-else>
                    <div class="card-header">
                        <a v-if="!form.isGenerated" href="{{route('invoice_edit', $id)}}">
                            <button type="button" class="btn btn-info"><i class="fas fa-edit"></i></button></a>

                        <button type="button" @click="refreshAttachments" class="btn btn-info">Sync Xero Data</button>
                        <a target="_blank" :href="form.xeroUrl">
                            <button type="button" class="btn btn-info">Open in Xero</button></a>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form>
                        <div class="card-body">
                            @include('invoices._form')
                            <div class="row">
                                <table class="table table-simple">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Xero Attachments</th>
                                        </tr>
                                        <tr>
                                            <td class="text-left">
                                                <button type="button" class="btn btn-success btn-xs"
                                                    @click="showInvoiceAttachmentModal = true">
                                                    Add Attachment
                                                </button>

                                            </td>
                                        </tr>
                                        <tr>
                                            <th>FileName</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in form.attachments.data">
                                            <td>
                                                <a target="_blank" :href="item.url">
                                                    @{{item.name}}
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <invoice-attachment-modal v-show="showInvoiceAttachmentModal" :invoice-id="form.id"
                                    @close="showInvoiceAttachmentModal = false"></invoice-attachment-modal>
                            </div>
                            <div class="row" v-if="form.invoicePayments.data.length > 0">
                                <table class="table table-simple">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="text-center">Payments</th>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in form.invoicePayments.data">
                                            <td>
                                                @{{item.date}}
                                            </td>
                                            <td>@{{item.amount | numeric}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row" v-if="form.invoiceCredits.data.length > 0">
                                <table class="table table-simple">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="text-center">Credits</th>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in form.invoiceCredits.data">
                                            <td>
                                                @{{item.date}}
                                            </td>
                                            <td>@{{item.amount | numeric}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    var id = {!! json_encode($id) !!};
    var isShow = true;
</script>
<script src="{{ mix('js/invoice.js') }}"></script>
@endpush