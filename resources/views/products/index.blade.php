@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="index">
    <div class="card">
        <div class="card-header">
            <a href="{{route('product_create')}}">
                <button type="button" class="btn btn-success btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> Product </button></a>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i> 
                </button>
            </div>
        </div>
        <div class="card-body">
            <index 
                :filterable="filterable" 
                :export-base-url="exportBaseUrl" 
                :base-url="baseUrl" 
                :sorter="sorter" 
                :sort-ascending="sortAscending" 
                v-on:update-loading="(val) => isLoading = val" 
                v-on:update-items="(val) => items = val">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Date And Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in items" v-if="!isLoading">
                            <td>@{{item.name}}</td>
                            <td>@{{item.category}}</td>
                            <td>@{{item.date_and_time}}</td>
                            <td>
                                <div class="btn-group">
                                    <a :href="item.editUrl"><button type="button" class="btn btn-info"><i class="fas fa-edit"></i></button></a>
                                    <button type="button" class="btn btn-danger" @click="destroy(`/api/products/${item.id}`)"><i class="fas fa-trash"></i></button>
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
<script src="{{ mix('js/index.js') }}"></script>
@endpush