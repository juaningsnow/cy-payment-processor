<div v-if="Object.keys(form.errors.errors).length" class="alert alert-danger" role="alert">
    <small class="form-text form-control-feedback" v-for="error in form.errors.errors">
        @{{ error[0] }}
    </small>
</div>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label for="invoiceNumber">Invoice Number</label>
            <input type="text" :disabled="isShow" class="form-control" placeholder="Name" v-model="form.invoiceNumber">
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
    <div class="col-6">
        <div class="form-group">
            <label for="supplierId">Supplier</label>
            <select class="form-control select2" v-model="form.supplierId" style="width: 100%" :disabled="isShow">
                <option selected="selected" disabled :value="null">
                    -Select Supplier-
                </option>
                <option v-for="(item, index) in supplierSelections" :key="index" :value="item.id">
                    @{{ item.text }}
                </option>
            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" :disabled="isShow" class="form-control" placeholder="Name" v-model="form.total">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label for="description">Remarks</label>
            <input type="text" :disabled="isShow" class="form-control" placeholder="Remarks" v-model="form.description">
        </div>
    </div>
</div>