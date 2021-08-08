@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="index" v-cloak>
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <index :filterable="filterable" :export-base-url="exportBaseUrl" :base-url="baseUrl" :sorter="sorter"
                :sort-ascending="sortAscending" v-on:update-loading="(val) => isLoading = val" :filters="filters"
                v-on:update-items="(val) => items = val">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <a v-on:click="setSorter('batch_name')">
                                    Batch # <i class="fa" :class="getSortIcon('batch_name')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('date')">
                                    Date <i class="fa" :class="getSortIcon('date')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('name')">
                                    Name <i class="fa" :class="getSortIcon('name')"></i>
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
                            <td>@{{item.batchName}}</td>
                            <td>@{{item.date}}</td>
                            <td>@{{item.name}}</td>
                            <td>
                                <span v-if="item.status == `Not Yet Generated`" class="badge badge-info">Not Yet
                                    Generated</span>
                                <span v-if="item.status == `Generated`" class="badge badge-success">Generated</span>
                                <span v-if="item.status == `Cancelled`" class="badge badge-danger">Cancelled</span>
                            </td>
                            <td class="text-right">@{{item.total | numeric}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a :href="item.showUrl"><button type="button" class="btn btn-default"><i
                                                class="far fa-eye"></i></button></a>
                                    <a :href="item.editUrl"><button type="button" class="btn btn-info"><i
                                                class="fas fa-edit"></i></button></a>
                                    <button type="button" class="btn btn-danger"
                                        @click="destroy(`/api/invoice-batches/${item.id}`,`/invoice-batches`)"><i
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
    var filters = indexVariables.filters;
</script>
<script src="{{ mix('js/index.js') }}"></script>
@endpush