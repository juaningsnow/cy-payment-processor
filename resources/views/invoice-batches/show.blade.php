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
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" title="Export Text File" @click="exportTextFile"
                                    class="btn btn-success">Generate</button>
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
    var isEdit = false;
</script>
<script src="{{ mix('js/invoice-batch.js') }}"></script>
@endpush