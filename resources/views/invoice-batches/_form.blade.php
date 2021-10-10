<div v-if="Object.keys(form.errors.errors).length" class="alert alert-danger" role="alert">
    <small class="form-text form-control-feedback" v-for="error in form.errors.errors">
        @{{ error[0] }}
    </small>
</div>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label for="batchNumber">Batch No.</label>
            <input id="batchNumber" type="text" class="form-control" v-model="form.batchName" disabled
                placeholder="Auto Generated" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label for="date">Date</label>
            <datepicker id="date" input-class="form-control" :disabled="isShow" :typeable="true" v-model="form.date">
            </datepicker>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label for="name">Batch Name</label>
            <input id="name" type="text" class="form-control" v-model="form.name" :disabled="isShow"
                placeholder="Enter Name" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label for="supplier">Redirect payment to</label>
            <select class="form-control select2" v-model="form.supplierId" style="width: 100%" :disabled="isShow">
                <option selected="selected" disabled :value="nullValue">
                    -Select Supplier-
                </option>
                <option v-for="(item, index) in supplierSelections" :key="index" :value="item.id">
                    @{{ item.text }}
                </option>
            </select>
        </div>
    </div>
</div>
<table class="table table-responsive">
    <thead>
        <tr>
            <th>Supplier</th>
            <th>Date</th>
            <th>Invoice Number</th>
            <th>Amount</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody v-for="(detail, index) in form.invoiceBatchDetails.data" is="invoice-batch-detail" :key="detail.id"
        :detail="detail" :index="index" :is-edit="isEdit" :is-show="isShow"
        @remove="form.invoiceBatchDetails.data.splice(index, 1)">
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-right">Total</td>
            <td class="text-right">@{{ totalAmount | numeric }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>