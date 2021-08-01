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
    <label for="cashAccount">Xero Cash Account</label>
    <select class="form-control select2" :disabled="isShow" id="cashAccountId" v-model="form.cashAccountId"
        style="width: 100%">
        <option selected="selected" disabled :value="nullValue">
            -Select Account-
        </option>
        <option v-for="(item, index) in accountSelections" :key="index" :value="item.id">
            @{{ item.name }} (@{{ item.code }})
        </option>
    </select>
</div>

<div class="form-group">
    <label for="cashAccount">Xero Bank Account</label>
    <select class="form-control select2" :disabled="isShow" id="bankAccountId" v-model="form.bankAccountId"
        style="width: 100%">
        <option selected="selected" disabled :value="nullValue">
            -Select Account-
        </option>
        <option v-for="(item, index) in accountSelections" :key="index" :value="item.id">
            @{{ item.name }} (@{{ item.code }})
        </option>
    </select>
</div>