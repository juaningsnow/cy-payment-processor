@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="index" v-cloak>
    <div class="card">
        <div class="card-header">
            <button type="button" @click="showInvoiceModal = true" class="btn btn-success btn-sm"><i class="fa fa-plus"
                    aria-hidden="true"></i>
                Invoice </button>
            <button type="button" v-if="selected.length > 0" @click="addToBatch" class="btn btn-success btn-sm">
                Add To Batch
            </button>
            <button type="button" @click="showMarkAsPaidModal = true" class="btn btn-success btn-sm">
                Mark as Paid
            </button>
            <button type="button" v-if="selected.length > 0" @click="deleteAll" class="btn btn-danger btn-sm">
                Delete Selected
            </button>
            <button type="button" @click="refreshInvoices" class="btn btn-success btn-sm">
                Refresh Invoices
            </button>
            <a href="https://app.dext.com/login" target="_blank">
                <button type="button" class="btn btn-info btn-sm"
                    title="Can't find your invoice? publish them in dext."> Open Dext
                </button>
            </a>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <index ref="index" :filterable="filterable" :export-base-url="exportBaseUrl" :base-url="baseUrl"
                :sorter="sorter" :sort-ascending="sortAscending" v-on:update-loading="(val) => isLoading = val"
                v-on:update-items="(val) => items = val">
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <td @click="selectAll" class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input" v-model="allSelected" type="checkbox"
                                        id="flexCheckChecked">
                                </div>
                            </td>
                            <th>
                                <a v-on:click="setSorter('invoice_number')">
                                    Invoice Number <i class="fa" :class="getSortIcon('invoice_number')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('supplier_name')">
                                    Supplier <i class="fa" :class="getSortIcon('supplier_name')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('date')">
                                    Date <i class="fa" :class="getSortIcon('date')"></i>
                                </a>
                            </th>
                            <th class="text-right">
                                <a v-on:click="setSorter('currency_code')">
                                    Currency <i class="fa" :class="getSortIcon('currency_code')"></i>
                                </a>
                            </th>
                            <th class="text-right">
                                <a v-on:click="setSorter('amount_paid')">
                                    Amount Paid <i class="fa" :class="getSortIcon('amount_paid')"></i>
                                </a>
                            </th>
                            <th class="text-right">
                                <a v-on:click="setSorter('amount_due')">
                                    Amount Due <i class="fa" :class="getSortIcon('amount_due')"></i>
                                </a>
                            </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" v-if="!isLoading" @click="addOrRemoveToSelected(item)">
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="selected" :value="item"
                                        :id="item.id">
                                </div>
                            </td>
                            <td>@{{ item . invoiceNumber }}</td>
                            <td>@{{ item . supplier . name }}</td>
                            <td>@{{ item . date }}</td>
                            <td>@{{ item . currency . code }}</td>
                            <td class="text-right">@{{ (item . amountPaid) | numeric }}</td>
                            <td class="text-right">@{{ (item . amountDue) | numeric }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a :href="item.showUrl"><button type="button" class="btn btn-default"><i
                                                class="far fa-eye"></i></button></a>
                                    <a :href="item.editUrl"><button type="button" class="btn btn-info"><i
                                                class="fas fa-edit"></i></button></a>
                                    <button type="button" class="btn btn-danger"
                                        @click="destroy(`/api/invoices/${item.id}`,`/invoices`)"><i
                                            class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </index>
            <invoice-modal v-if="showInvoiceModal" @close="showInvoiceModal = false" @reload-data="reloadData">
            </invoice-modal>
            <batch-modal :selected-invoices="selected" v-if="showBatchModal" @close="showBatchModal = false"
                @reload-data="reloadData"></batch-modal>
            <mark-as-paid-modal :selected="selected" v-if="showMarkAsPaidModal" :company-id="companyId"
                @close="showMarkAsPaidModal = false" @reload-data="reloadData"></mark-as-paid-modal>
            <add-multiple-to-batch-modal :selected-invoices="selected" v-if="showAddToBatchModal"
                @close="showAddToBatchModal = false" @reload-data="reloadData"></add-multiple-to-batch-modal>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
    var indexVariables = {!! json_encode($indexVariables) !!};
        var baseUrl = indexVariables.baseUrl;
        var exportBaseUrl = indexVariables.exportBaseUrl;
        var filterable = indexVariables.filterable;
        var sorter = indexVariables.sorter;
        var companyId = indexVariables.companyId;

</script>
<script src="{{ mix('js/invoice-index.js') }}"></script>
@endpush