@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="index" v-cloak>
    <div class="card">
        <div class="card-header">
            <button type="button" v-if="selected.length > 0" @click="showBatchModal = true"
                class="btn btn-success btn-sm">
                Add To Batch
            </button>
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
                <table class="table">
                    <thead>
                        <tr>
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
                            <th class="text-center">
                                <a v-on:click="setSorter('status')">
                                    Status <i class="fa" :class="getSortIcon('status')"></i>
                                </a>
                            </th>
                            <th class="text-right">
                                <a v-on:click="setSorter('amount')">
                                    Amount <i class="fa" :class="getSortIcon('name')"></i>
                                </a>
                            </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" v-if="!isLoading" @click="addOrRemoveToSelected(item)">
                            <td>@{{item.invoiceNumber}}</td>
                            <td>@{{item.supplier.name}}</td>
                            <td>@{{item.date}}</td>
                            <td class="text-center">
                                <span v-if="item.status == `Generated and Paid`"
                                    class="badge badge-success">@{{item.status}}</span>
                                <span v-if="item.status == `Batched`" class="badge badge-info">@{{item.status}}</span>
                                <span v-if="item.status == `Unpaid`" class="badge badge-danger">@{{item.status}}</span>
                                <span v-else class="badge badge-success">@{{item.status}}</span>
                            </td>
                            <td class="text-right">@{{item.amount | numeric}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a :href="item.showUrl"><button type="button" class="btn btn-default"><i
                                                class="far fa-eye"></i></button></a>
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
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    var indexVariables = {!! json_encode($indexVariables) !!};
    var baseUrl = indexVariables.baseUrl;
    var exportBaseUrl = indexVariables.exportBaseUrl;
    var filterable = indexVariables.filterable;
    var sorter = indexVariables.sorter;
</script>
<script src="{{ mix('js/invoice-index.js') }}"></script>
@endpush