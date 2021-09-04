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

<div class="row">
    <table class="table table-responsive">
        <thead>
            <tr>
                <th colspan="3" class="text-center">Companies</th>
            </tr>
            <tr>
                <th>Name</th>
                <th>Active</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(userCompany,index) in form.userCompanies.data">
                <td>
                    <select class="form-control select2" :disabled="isShow" v-model="userCompany.companyId">
                        <option selected="selected" disabled :value="null">
                            -Select Company-
                        </option>
                        <option v-for="(item, index) in companySelections" :key="index" :value="item.id">
                            @{{item.name}}
                        </option>
                    </select>
                </td>
                <td>
                    <i class="fas fa-check" v-if="userCompany.isActive"></i>
                </td>
                <td>
                    <button v-if="!userCompany.isActive" :disabled="isShow" @click="removeCompany(index)" type="button"
                        class="btn btn-danger btn-sm">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-left">
                    <button :disabled="isShow" @click="addCompany" type="button" class="btn btn-success btn-sm">
                        Add Company
                    </button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>