<div class="row">
    <table class="table table-responsive">
        <thead>
            <tr>
                <th>Bank</th>
                <th>Account Number</th>
                <th>Account Code</th>
                <th>Default</th>
                <th>Remove</>
            </tr>
        </thead>
        <tbody>
            <tr v-for="companyBank in form.companyBanks.data" is="company-bank-row" :company-id="form.id"
                :company-bank="companyBank" @remove-bank="removeBank" @make-default="makeDefault" @reload-data="load">
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