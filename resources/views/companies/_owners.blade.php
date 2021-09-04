<div class="row">
    <table class="table table-responsive">
        <thead>
            <tr>
                <th>Name</th>
                <th>Account</th>
                <th class="text-right">Remove</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(companyOwner,index) in form.companyOwners.data">
                <td>
                    <input type="text" :disabled="isShow" class="form-control" placeholder="Name"
                        v-model="companyOwner.name">
                </td>
                <td>
                    <select class="form-control select2" :disabled="isShow" v-model="companyOwner.accountId"
                        style="width: 100%">
                        <option selected="selected" disabled :value="nullValue">
                            -Select Account-
                        </option>
                        <option v-for="(item, index) in accountSelections" :key="index" :value="item.id">
                            @{{ item.name }} (@{{ item.code }})
                        </option>
                    </select>
                </td>
                <td class="text-right">
                    <button type="button" :disabled="isShow" @click="form.companyOwners.data.splice(index, 1)"
                        class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        </tbody>
        <tfoot v-if="!isShow">
            <tr>
                <td colspan="5">
                    <button type="button" @click="addOwner" class="btn btn-success btn-sm">Add Owner</button>
                </td>
            </tr>
        </tfoot>
    </table>
</div>