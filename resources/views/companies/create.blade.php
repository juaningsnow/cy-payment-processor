@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="company" v-cloak>
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
                            <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-content-below-home-tab" data-toggle="pill"
                                        href="#custom-content-below-home" role="tab"
                                        aria-controls="custom-content-below-home" aria-selected="true">Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-below-profile-tab" data-toggle="pill"
                                        href="#custom-content-below-profile" role="tab"
                                        aria-controls="custom-content-below-profile" aria-selected="false">Owners</a>
                                </li>

                            </ul>

                            <div class="tab-content" id="custom-content-below-tabContent">
                                <div class="tab-pane fade active show" id="custom-content-below-home" role="tabpanel"
                                    aria-labelledby="custom-content-below-home-tab">
                                    <br>
                                    @include('companies._form')
                                </div>
                                <div class="tab-pane fade" id="custom-content-below-profile" role="tabpanel"
                                    aria-labelledby="custom-content-below-profile-tab">
                                    @include('companies._owners')
                                </div>

                            </div>
                        </div>
                    </form>
                    <div class="card-footer">
                        <div class="text-right">
                            <button class="btn btn-success" @click="store" :disabled="form.isBusy">
                                <div v-if="form.isSaving">
                                    <i class="fas fa-circle-notch fa-spin"></i> Saving...
                                </div>
                                <div v-if="!form.isSaving">
                                    <i class="fa fa-save"></i>
                                </div>
                            </button>
                        </div>
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
</script>
<script src="{{ mix('js/company.js') }}"></script>
@endpush