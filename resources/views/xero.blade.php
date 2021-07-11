@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><b>{{auth()->user()->company->name}} API Connection Status</b></h3>

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
        @else
        <div class="row">
            <span class="badge badge-danger">Disconnected</span>
        </div>
        <div class="row">
            <a target="_blank" href="{{$authUrl}}">Click Here to Connect</a>
        </div>
        @endif

    </div>

</div>
@endsection