@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div id="invoice" v-cloak>
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
                            <button type="button" class="btn btn-info"><i class="fas fa-edit"></i></button></a>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <form>
                        <div class="card-body">
                            @include('invoices._form')
                            <div class="row">
                                <table class="table table-simple">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Attachments</th>
                                        </tr>
                                        <tr>
                                            <th>FileName</th>
                                            <th class="text-right">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in form.media.data">
                                            <td>
                                                <a :href="item.downloadUrl">
                                                    @{{item.fileName}}
                                                </a>
                                            </td>
                                            <td class="text-right">
                                                <button type="button" @click="removeFile(item.id)"
                                                    class="btn btn-danger btn-sm"><i
                                                        class="fas fa-times"></i></button></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <file-pond name="file" ref="pond" label-idle="Drop files here..."
                                v-bind:allow-multiple="true" :server="{
                                    url:  `/api/invoices/${form.id}/attachment`,
                                    process: {
                                      headers: {
                                        'X-CSRF-TOKEN': csrfToken
                                      }
                                    }
                                  }" v-bind:files="myFiles" v-on:init="handleFilePondInit"
                                @processfile="reloadData(form.id)" />
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
<script src="{{ mix('js/invoice.js') }}"></script>
@endpush