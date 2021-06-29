<div v-if="Object.keys(form.errors.errors).length" class="alert alert-danger" role="alert">
    <small class="form-text form-control-feedback" v-for="error in form.errors.errors">
        @{{ error[0] }}
    </small>
</div>
<div class="form-group">
    <label for="name">Name</label>
    <input type="text" :disabled="isShow" class="form-control" placeholder="Name" v-model="form.name">
</div>

<div v-if="isShow">
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
                <tr v-for="companyBank in form.companyBanks.data">
                    <td>@{{companyBank.bank.name}}</td>
                    <td>@{{companyBank.accountNumber}}</td>
                    <td>
                        <span v-if="companyBank.default" class="badge badge-info">default</span>
                        <button v-else type="button" @click="makeDefault(companyBank)"
                            class="btn btn-success btn-sm">Make
                            Default</button>
                    </td>
                    <td>
                        <button type="button" @click="removeBank(companyBank)" class="btn btn-danger btn-sm"><i
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
                        <button type="button" @click="showBankModal = true" class="btn btn-success btn-sm">Add
                            Bank</button>
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>