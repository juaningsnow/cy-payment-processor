@extends('layouts.app')

@section('content')
<div id="xero" v-cloak>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><b>{{auth()->user()->getActiveCompany()->name}} API Connection Status</b></h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($connected)
            <div class="row">
                <span class="badge badge-success">Connected!</span>
            </div>
            <br>
            <div class="row">
                <button type="button" class="btn btn-danger btn-xs" @click="revokeApiConnection">
                    Revoke Connection
                </button>
            </div>
            @else
            <div class="row">
                <span class="badge badge-danger">Disconnected</span>
            </div>
            <div class="row">
                <a href="{{$authUrl}}">Click Here to Connect</a>
            </div>
            @endif

        </div>

    </div>
</div>
@endsection
@push('scripts')
<script src="{{ mix('js/xero.js') }}"></script>
@endpush