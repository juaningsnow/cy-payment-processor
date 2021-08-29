@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="invoice-batch" v-cloak>
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
                        <a v-if="!form.generated" href="{{route('invoice-batches_edit', $id)}}">
                            <button type="button" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></button></a>
                        <button v-if="!form.generated" type="button" @click="showInvoiceListModal = true"
                            class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Add Invoice
                        </button>
                        <button type="button" class="btn btn-info btn-sm" @click="copy">
                            <i class="fas fa-copy"></i>
                        </button>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" v-if="!form.cancelled && form.generated" title="Cancel"
                                    @click="cancel" class="btn btn-danger btn-sm">Cancel</button>
                                <button type="button" title="Export Text File" @click="exportTextFile"
                                    class="btn btn-success btn-sm">Generate</button>
                            </div>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form>
                        <div class="card-body">
                            @include('invoice-batches._form')
                        </div>
                        <invoice-list-modal v-if="showInvoiceListModal" @close="showInvoiceListModal = false"
                            :base-url="baseUrl" :export-base-url="exportBaseUrl" :filters="filters"
                            :filterable="filterable" :sort-ascending="sortAscending" :to-last-page="toLastPage"
                            :invoice-batch-id="form.id" @reload-data="load">
                        </invoice-list-modal>
                        <supplier-modal @reload-data="reloadData" v-if="showSupplierModal"
                            @close="showSupplierModal = false" :supplier-id="supplierIdToUpdate"></supplier-modal>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
    var id = {!! json_encode($id) !!};
    var isShow = true;
    var isEdit = false;
    var indexVariables = {!! json_encode($indexVariables) !!};
    var baseUrl = indexVariables.baseUrl;
    var exportBaseUrl = indexVariables.exportBaseUrl;
    var filterable = indexVariables.filterable;
    var sorter = indexVariables.sorter;
</script>
<script src="{{ mix('js/invoice-batch.js') }}"></script>
@endpush