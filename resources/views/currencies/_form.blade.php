<div v-if="Object.keys(form.errors.errors).length" class="alert alert-danger" role="alert">
    <small class="form-text form-control-feedback" v-for="error in form.errors.errors">
        @{{ error[0] }}
    </small>
</div>
<div class="form-group">
    <label for="name">Code</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="SGD" v-model="form.code">
</div>
<div class="form-group">
    <label for="name">Description</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Singapore Dollar"
        v-model="form.description">
</div>