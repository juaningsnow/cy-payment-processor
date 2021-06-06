@extends('layouts.app')
@section('content')
@include('layouts.section-header')
<div class="card" id="dashboard" v-cloak>
  <div class="card-header">
    <h3 class="card-title">CY Industries</h3>

    <div class="card-tools">
      <button type="button" @click="toggleEdit" class="btn btn-info"><i class="fas fa-edit"></i></button>
      <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
        <i class="fas fa-minus"></i>
      </button>
    </div>
  </div>
  <div class="card-body">
    <div class="row align-items-center" v-if="!initializationComplete" style="height: 100px">
      <div class="col-12 text-center h4">
        <i class="fas fa-circle-notch fa-spin"></i> Initializing...
      </div>
    </div>
    <div v-else>
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" :disabled="isShow" class="form-control" placeholder="Name" v-model="form.name">
          </div>
        </div>
        <div class="col-6">
          <div class="form-group">
            <label for="name">Email</label>
            <input type="text" :disabled="isShow" class="form-control" placeholder="Email" v-model="form.email">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-6">
          <div class="form-group">
            <label for="name">Username</label>
            <input type="text" :disabled="isShow" class="form-control" placeholder="Username" v-model="form.username">
          </div>
        </div>
      </div>
      <hr>
      <h5>Banks</h5>
      <div class="row">
        <table class="table table-simple">
          <thead>
            <tr>
              <th>Bank</th>
              <th>Account Number</th>
              <th>Default</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="userBank in form.userBanks.data">
              <td>@{{userBank.bank.name}}</td>
              <td>@{{userBank.accountNumber}}</td>
              <td>
                <span v-if="userBank.default" class="badge badge-info">default</span>
                <button v-else type="button" @click="makeDefault(userBank)" class="btn btn-success btn-sm">Make
                  Default</button>
              </td>
              <td>
                <button type="button" @click="removeBank(userBank)" class="btn btn-danger btn-sm"><i
                    class="fas fa-trash"></i></button>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4">
                <add-bank-modal :user-id="form.id" v-if="showBankModal" @close="showBankModal = false"
                  @reload-data="load">
                </add-bank-modal>
                <button type="button" @click="showBankModal = true" class="btn btn-success btn-sm">Add Bank</button>
              </td>
            </tr>
          </tfoot>
        </table>

      </div>
    </div>

  </div>
  <div class="card-footer" v-if="!isShow">
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
  </div>
</div>
<!-- /.card -->
@endsection
@push('scripts')
<script type="text/javascript">
</script>
<script src="{{ mix('js/dashboard.js') }}"></script>
@endpush