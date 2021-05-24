<div v-if="Object.keys(form.errors.errors).length" class="alert alert-danger" role="alert">
    <small class="form-text form-control-feedback" v-for="error in form.errors.errors">
        @{{ error[0] }}
    </small>
</div>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label for="batchName">Batch Name</label>
            <input type="text" :disabled="isShow" class="form-control" placeholder="Name" v-model="form.batchName">
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label for="date">Date</label>
            <datepicker input-class="form-control" :disabled="isShow" :typeable="true" v-model="form.date"></datepicker>
        </div>
    </div>
</div>
<div class="row">
    <table class="table">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Date</th>
                <th>Invoice Number</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(detail,index) in form.invoiceBatchDetails.data" is="invoice-batch-detail" :key="detail.id"
                :supplier-selections="supplierSelections" :detail="detail" :is-show="isShow" :index="index"
                @remove="form.invoiceBatchDetail.data.splice(index,1)"></tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">@{{form.total}}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5">
                    <button type="button" class="btn btn-success btn-sm" @click="addDetail">Add Detail</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>