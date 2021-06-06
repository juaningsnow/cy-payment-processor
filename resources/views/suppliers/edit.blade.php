@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="supplier" v-cloak>
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
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form>
                        <div class="card-body">
                            @include('suppliers._form')
                        </div>
                        <div class="card-footer">
                            <div class="text-right">
                                <button class="btn btn-success" @click="update" :disabled="form.isBusy">
                                    <div v-if="form.isSaving">
                                        <i class="fas fa-circle-notch fa-spin"></i> Saving...
                                    </div>
                                    <div v-if="!form.isSaving">
                                        <i class="fa fa-save"></i>
                                    </div>
                                </button>
                            </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    var id = {!! json_encode($id) !!};
    var isShow = false;
</script>
<script src="{{ mix('js/supplier.js') }}"></script>
@endpush