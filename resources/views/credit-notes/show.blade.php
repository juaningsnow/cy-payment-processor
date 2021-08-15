@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="credit-note" v-cloak>
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
                            <button type="button" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></button></a>

                        <button type="button" @click="syncXeroData" class="btn btn-info">Sync Xero Data</button>
                        <a target="_blank" :href="form.xeroUrl">
                            <button type="button" class="btn btn-info btn-sm">Open in Xero To Allocate
                                Credit</button></a>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form>
                        <div class="card-body">
                            @include('credit-notes._list')
                            <div class="row">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th class="text-right">Invoice Amount</th>
                                            <th class="text-right">Applied Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="form.creditNoteAllocations.data.length > 0">
                                        <tr v-for="item in form.creditNoteAllocations.data">
                                            <td>
                                                @{{item.invoice.invoiceNumber}}
                                            </td>
                                            <td class="text-right">
                                                @{{item.invoice.total}}
                                            </td>
                                            <td class="text-right">
                                                @{{item.amount}}
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td class="text-center" colspan="3">
                                                No Allocation,Open in Xero to Allocate credit
                                            </td>
                                        </tr>
                                    </tbody>
                                    {{-- <tfoot>
                                        <tr>
                                            <td class="text-left" colspan="3">
                                                <button type="button" @click="showAllocationModal = true"
                                                    class="btn btn-success btn-sm">
                                                    Add Allocation
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                    <allocation-modal v-if="showAllocationModal" @close="showAllocationModal = false"
                                        :supplier-id="form.supplierId"
                                        @add-credit-note-allocation="addCreditNoteAllocation">
                                    </allocation-modal> --}}
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
<script src="{{ mix('js/creditnotes.js') }}"></script>
@endpush