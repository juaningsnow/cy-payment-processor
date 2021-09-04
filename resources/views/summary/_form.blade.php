<div v-if="Object.keys(form.errors.errors).length" class="alert alert-danger" role="alert">
    <small class="form-text form-control-feedback" v-for="error in form.errors.errors">
        @{{ error[0] }}
    </small>
</div>
<div class="row">
    <div class="col-12">
        <div class="float-right">
            <div class="btn-group">
                <button type="button" title="Preview" @click="preview" class="btn btn-success"><i
                        class="fas fa-search"></i></button>
                <button type="button" title="Export CSV" @click="exportCsv" class="btn btn-success"><i
                        class="fas fa-file-csv"></i></button>
                <button type="button" title="Export Excel" @click="exportExcel" class="btn btn-success"><i
                        class="fas fa-file-excel"></i></button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label for="dateFrom">Date From:</label>
            <datepicker @input="formatDateFrom" input-class="form-control" :typeable="true" v-model="trackedDateFrom">
            </datepicker>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label for="dateTo">Date To:</label>
            <datepicker @input="formateDateTo" input-class="form-control" :typeable="true" v-model="trackedDateTo">
            </datepicker>
        </div>
    </div>
</div>

<div v-if="displayData.length > 0" class="row">
    <table class="table table-responsive">
        <tr>
            <th>Supplier</th>
            <th>Account Number</th>
            <th>Swift Code</th>
            <th>Invoice Number</th>
            <th>Payment Type</th>
            <th class="text-right">Amount</th>
        </tr>
        <tr v-for="(item,index) in displayData">
            <td>@{{item.supplier.name}}</td>
            <td>@{{item.supplier.accountNumber}}</td>
            <td>@{{item.supplier.swiftCode}}</td>
            <td>@{{item.invoiceNumber}}</td>
            <td>@{{item.supplier.paymentType}}</td>
            <td class="text-right">@{{item.amount | numeric}}</td>
        </tr>
        <tr>
            <th colspan="5" class="text-right">TOTAL</th>
            <td class="text-right">@{{totalDisplay | numeric}}</td>
        </tr>
    </table>
</div>