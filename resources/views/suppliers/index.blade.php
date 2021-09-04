@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="index" v-cloak>
    <div class="card">
        <div class="card-header">
            <a href="{{route('supplier_create')}}">
                <button type="button" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i>
                    Supplier </button></a>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <index :filterable="filterable" :export-base-url="exportBaseUrl" :base-url="baseUrl" :sorter="sorter"
                :sort-ascending="sortAscending" v-on:update-loading="(val) => isLoading = val"
                v-on:update-items="(val) => items = val">
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>
                                <a v-on:click="setSorter('name')">
                                    Name <i class="fa" :class="getSortIcon('name')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('purpose_code')">
                                    Purpose <i class="fa" :class="getSortIcon('purpose_code')"></i>
                                </a>

                            </th>
                            <th>
                                <a v-on:click="setSorter('payment_type')">
                                    Payment Type <i class="fa" :class="getSortIcon('payment_type')"></i>
                                </a>

                            </th>
                            <th>
                                <a v-on:click="setSorter('account_number')">
                                    Account Number <i class="fa" :class="getSortIcon('account_number')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('bank_name')">
                                    Bank <i class="fa" :class="getSortIcon('bank_name')"></i>
                                </a>
                            </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" v-if="!isLoading">
                            <td>@{{item.name}}</td>
                            <td>@{{item.purpose ? item.purpose.name : '--'}}</td>
                            <td>@{{item.paymentType}}</td>
                            <td>@{{item.accountNumber ? item.accountNumber : '--'}}</td>
                            <td>@{{item.bank ? item.bank.name : '--'}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a :href="item.showUrl"><button type="button" class="btn btn-default"><i
                                                class="far fa-eye"></i></button></a>
                                    <a :href="item.editUrl"><button type="button" class="btn btn-info"><i
                                                class="fas fa-edit"></i></button></a>
                                    <button type="button" class="btn btn-danger"
                                        @click="destroy(`/api/suppliers/${item.id}`,`/suppliers`)"><i
                                            class="fas fa-trash"></i></button>
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
</script>
<script src="{{ mix('js/index.js') }}"></script>
@endpush