@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="index" v-cloak>
    <div class="card">
        <div class="card-header">
            <a href="{{route('user_create')}}">
                <button type="button" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i>
                    User </button></a>
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
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <a v-on:click="setSorter('name')">
                                    Name <i class="fa" :class="getSortIcon('name')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('username')">
                                    Username <i class="fa" :class="getSortIcon('username')"></i>
                                </a>
                            </th>
                            <th>
                                <a v-on:click="setSorter('email')">
                                    Email <i class="fa" :class="getSortIcon('email')"></i>
                                </a>
                            </th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" v-if="!isLoading">
                            <td>@{{item.name}}</td>
                            <td>@{{item.username}}</td>
                            <td>@{{item.email}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a :href="item.showUrl"><button type="button" class="btn btn-default"><i
                                                class="far fa-eye"></i></button></a>
                                    <a :href="item.editUrl"><button type="button" class="btn btn-info"><i
                                                class="fas fa-edit"></i></button></a>
                                    <button type="button" class="btn btn-danger"
                                        @click="destroy(`/api/users/${item.id}`,`/users`)"><i
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