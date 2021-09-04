@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="index" v-cloak>
    <div class="card">
        <div class="card-header">
            <button type="button" @click="refreshCreditNotes" class="btn btn-success btn-sm">
                Refresh Credit Notes
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
                :filters="filters" v-on:update-items="(val) => items = val">
                <table class="table table-responsive">
                    <thead>
                        <tr>
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
                            <th>
                                <a v-on:click="setSorter('currency_code')">
                                    Currency <i class="fa" :class="getSortIcon('currency_code')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('status')">
                                    Status <i class="fa" :class="getSortIcon('status')"></i>
                                </a>
                            </th>
                            <th class="text-right">
                                <a v-on:click="setSorter('total')">
                                    Total <i class="fa" :class="getSortIcon('total')"></i>
                                </a>
                            </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" v-if="!isLoading">
                            <td>@{{item.supplier ? item.supplier.name : "--"}}</td>
                            <td>@{{item.date}}</td>
                            <td>@{{item.currency ? item.currency.code : "--"}}</td>
                            <td>@{{item.status}}</td>
                            <td class="text-right">@{{item.total | numeric}}</td>
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
    var companyId = indexVariables.companyId;
    var filters = indexVariables.filters;
</script>
<script src="{{ mix('js/index.js') }}"></script>
@endpush