<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><b>Supplier</b></label>
            <p>@{{form.supplier ? form.supplier.name : "--"}}</p>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group">
            <label><b>Currency</b></label>
            <p>@{{form.currency ? form.currency.code : "--"}}</p>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><b>Total Applied Amount</b></label>
            <p>@{{form.appliedAmount | numeric}}</p>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><b>Total</b></label>
            <p>@{{form.total | numeric}}</p>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><b>Status</b></label>
            <p>@{{form.status ? form.status : "--"}}</p>
        </div>
    </div>
</div>