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
                        <a href="{{route('company_edit', $id)}}">
                            <button type="button" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></button></a>
                        <button type="button" @click="refreshCurrencies" class="btn btn-info btn-sm">Refresh
                            Currencies and Accounts</button>
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
                                <li class="nav-item" v-if="isShow">
                                    <a class="nav-link" id="custom-content-below-messages-tab" data-toggle="pill"
                                        href="#custom-content-below-messages" role="tab"
                                        aria-controls="custom-content-below-messages" aria-selected="false">Banks</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-content-below-profile-tab" data-toggle="pill"
                                        href="#custom-content-below-profile" role="tab"
                                        aria-controls="custom-content-below-profile" aria-selected="false">Owners</a>
                                </li>

                                <li class="nav-item" v-if="isShow">
                                    <a class="nav-link" id="custom-content-below-settings-tab" data-toggle="pill"
                                        href="#custom-content-below-settings" role="tab"
                                        aria-controls="custom-content-below-settings"
                                        aria-selected="false">Currencies</a>
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
                                <div class="tab-pane fade" id="custom-content-below-messages" role="tabpanel"
                                    aria-labelledby="custom-content-below-messages-tab">
                                    @include('companies._banks')
                                </div>
                                <div class="tab-pane fade" id="custom-content-below-settings" role="tabpanel"
                                    aria-labelledby="custom-content-below-settings-tab">
                                    @include('companies._currencies')
                                </div>
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
<script src="{{ mix('js/company.js') }}"></script>
@endpush