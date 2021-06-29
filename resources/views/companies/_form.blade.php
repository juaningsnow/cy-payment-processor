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
                    <th>Xero Account Code</th>
                    <th>Default</th>
                    <th>Remove</>
                </tr>
            </thead>
            <tbody>
                <tr v-for="companyBank in form.companyBanks.data" is="company-bank-row" :company-id="form.id"
                    :company-bank="companyBank" @remove-bank="removeBank" @reload-data="load">
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <add-bank-modal :company-id="form.id" v-if="showBankModal" @close="showBankModal = false"
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