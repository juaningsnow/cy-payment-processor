<div v-if="Object.keys(form.errors.errors).length" class="alert alert-danger" role="alert">
    <small class="form-text form-control-feedback" v-for="error in form.errors.errors">
        @{{ error[0] }}
    </small>
</div>
<div class="form-group">
    <label for="name">Name</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Name" v-model="form.name">
</div>

<div class="form-group">
    <label for="purpose">Purpose</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Purpose" v-model="form.purpose">
</div>


<div class="form-group">
    <label for="paymentType">Payment Type</label>
    <select class="form-control select2" :disabled="isShow" v-model="form.paymentType">
        <option selected="selected" disabled :value="null">
            -Select Payment Type-
        </option>
        <option v-for="(item, index) in paymentTypes" :key="index" :value="item">
            @{{ item }}
        </option>
    </select>
    {{-- <input type="text" :disabled="isShow" class="form-control" placeholder="Payment Type" v-model="form.paymentType"> --}}
</div>


<div class="form-group">
    <label for="accountNumber">Account Number</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Account Number"
        v-model="form.accountNumber">
</div>


<div class="form-group">
    <label for="swiftCode">Swift Code</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Swift Code" v-model="form.swiftCode">
</div>