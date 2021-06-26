@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">CY Xero API Connection Status</h3>

        <div class="card-tools">
            <button type="button" @click="toggleEdit" class="btn btn-info"><i class="fas fa-edit"></i></button>
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($connected)
        <div class="row">
            <span>Connected!</span>
        </div>
        @else
        <div class="row">
            <span>Disconnected</span>
        </div>
        <div class="row">
            <a href="{{$authUrl}}">Click Here to Connect</a>
        </div>
        @endif

    </div>

</div>
@endsection