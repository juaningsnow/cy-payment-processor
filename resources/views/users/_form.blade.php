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
    <label for="username">Username</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Username" v-model="form.username">
</div>

<div class="form-group">
    <label for="email">E-mail</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="E-mail" v-model="form.email">
</div>

<div class="form-group">
    <label for="password">Password</label>
    <input type="password" :disabled="isShow" class="form-control" placeholder="Password" v-model="form.password">
</div>

<div class="form-group">
    <label for="email">Is Admin?</label>
    <select class="form-control select2" :disabled="isShow" v-model="form.isAdmin">
        <option selected="selected" :value="falseValue">
            No
        </option>
        <option selected="selected" :value="trueValue">
            Yes
        </option>
    </select>
</div>

<div class="form-group">
    <label for="paymentType">Company</label>
    <select class="form-control select2" :disabled="isShow" v-model="form.companyId">
        <option selected="selected" disabled :value="null">
            -Select Company-
        </option>
        <option v-for="(item, index) in companySelections" :key="index" :value="item.id">
            @{{item.name}}
        </option>
    </select>
</div>

</div>