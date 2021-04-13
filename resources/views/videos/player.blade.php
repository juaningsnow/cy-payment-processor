@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="video">
    <div class="card">
        <div class="card-header">
          <h3 class="card-title">Play Vidoes</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary" v-for="item in videoList">
                  <input type="radio" @click="setVideo(item)"> @{{item.name}}
                </label>
            </div>
            <div class="row align-items-center" v-if="videoSelecting" style="height: 100px">
              <div class="col-12 text-center h4">
                  <i class="fas fa-circle-notch fa-spin"></i> Initializing...
              </div>
            </div>
            <video-player v-else :options="videoOptions"/>
        </div>
      </div>
</div>
@endsection
@push('scripts')
<script src="{{ mix('js/video.js') }}"></script>
@endpush