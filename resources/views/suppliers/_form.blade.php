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
    <label for="email">Email</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Email" v-model="form.email">
</div>

<div class="form-group">
    <label for="paymentType">Purpose</label>
    <select class="form-control select2" :disabled="isShow" v-model="form.purposeId">
        <option selected="selected" disabled :value="null">
            -Select Purpose-
        </option>
        <option v-for="(item, index) in purposeSelections" :key="index" :value="item.id">
            @{{item.description}} (@{{item.name}})
        </option>
    </select>
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
</div>


<div class="form-group">
    <label for="accountNumber">Account Number</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Account Number"
        v-model="form.accountNumber">
</div>


<div class="form-group">
    <label for="paymentType">Bank</label>
    <select class="form-control select2" :disabled="isShow" v-model="form.bankId">
        <option selected="selected" disabled :value="null">
            -Select Bank-
        </option>
        <option v-for="(item, index) in bankSelections" :key="index" :value="item.id">
            @{{item.name}} (@{{item.swift}})
        </option>
    </select>
</div>