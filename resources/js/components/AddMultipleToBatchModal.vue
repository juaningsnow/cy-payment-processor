<template>
    <modal-window
        :title="title"
        :form="form"
        @close="close"
        size="xl"
        @save="save"
    >
        <div
            class="row align-items-center"
            v-if="!initializationComplete"
            style="height: 100px"
        >
            <div class="col-12 text-center h4">
                <i class="fas fa-circle-notch fa-spin"></i> Initializing...
            </div>
        </div>
        <div v-else>
            <div
                v-if="Object.keys(form.errors.errors).length"
                class="alert alert-danger"
                role="alert"
            ></div>
            <small
                class="form-text form-control-feedback"
                v-for="(error, index) in form.errors.errors"
                :key="index"
                >{{ error[0] }}</small
            >
            <div class="row">
                <label for="owner">Batch</label>
                <select
                    class="form-control select2"
                    v-model="form.invoiceBatchId"
                    style="width: 100%"
                >
                    <option selected="selected" disabled :value="nullValue">
                        -Select Batch-
                    </option>
                    <option
                        v-for="(item, index) in invoiceBatchSelection"
                        :key="index"
                        :value="item.id"
                    >
                        {{ item.batchName }}({{ item.name }})
                    </option>
                </select>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Invoice Number</th>
                            <th>Amount</th>
                            <th v-if="!isShow">Actions</th>
                        </tr>
                    </thead>
                    <tbody
                        v-for="(detail, index) in form.invoiceBatchDetails.data"
                        is="invoice-batch-detail"
                        :key="detail.id"
                        :detail="detail"
                        :index="index"
                        @remove="form.invoiceBatchDetails.data.splice(index, 1)"
                    ></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right">Total</td>
                            <td class="text-right">
                                {{ totalAmount | numeric }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <template slot="footer">
            <button
                class="btn btn-success"
                @click="save"
                :disabled="form.isBusy"
            >
                <div v-if="form.isSaving">
                    <i class="fas fa-circle-notch fa-spin"></i> Saving...
                </div>
                <div v-if="!form.isSaving">
                    <i class="fa fa-save"></i>
                </div>
            </button>
            <button
                type="button"
                class="btn btn-secondary"
                :disabled="form.isBusy"
                @click="close"
            >
                Close
            </button>
        </template>
    </modal-window>
</template>
<script>
import ModalWindow from "./ModalWindow";
import { Form } from "./Form";
import InvoiceBatchDetail from "./InvoiceBatchDetail2";

export default {
    components: { ModalWindow, InvoiceBatchDetail },

    props: {
        selectedInvoices: {
            type: Array,
            default: [],
        },
        isShow: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            title: "Add to Batch",
            form: new Form({
                invoiceBatchId: null,
                invoiceBatchDetails: { data: [] },
                total: 0,
            }),
            counter: -1,
            nullValue: null,
            invoiceBatchSelection: [],
            invoiceBatchInitialized: false,
            supplierIdToUpdate: null,
            invoiceBatchDetailIndexForSupplierUpdate: null,
            showSupplierModal: false,
            dataInitialized: true,
        };
    },

    created() {
        this.form.invoiceId = this.invoiceId;
        this.setDetails().then(() => {
            this.form
                .get(`/api/invoice-batches?not-yet-generated=1`)
                .then((response) => {
                    this.invoiceBatchSelection = response.data;
                    this.invoiceBatchInitialized = true;
                    this.dataInitialized = true;
                });
        });
    },

    methods: {
        setDetails() {
            return new Promise((resolve, reject) => {
                this.selectedInvoices.forEach((invoice) => {
                    this.form.invoiceBatchDetails.data.push({
                        id: this.counter--,
                        invoiceId: invoice.id,
                        invoice: invoice,
                        amount:
                            Number(invoice.amountDue) > 0
                                ? Number(invoice.amountDue)
                                : Number(invoice.total),
                    });
                });
                resolve();
            });
        },
        close() {
            this.$emit("close");
            this.form.reset();
        },
        save() {
            this.form
                .post(`/api/invoice-batches/details-add`)
                .then((response) => {
                    this.$swal({
                        title: "Added to Batch!",
                        text: "Invoices has been added to batch",
                        type: "success",
                    }).then(() => {
                        let showUrl = new URL(
                            `${window.location.origin}/invoice-batches/${response.data.id}`
                        );
                        window.location = showUrl;
                        this.close();
                    });
                })
                .catch((error) => {
                    this.$swal({
                        title: "Error!",
                        text: error.message,
                        type: "warning",
                    });
                });
        },
        reloadData() {
            this.dataInitialized = false;
            this.form
                .get(`/api/suppliers/${this.supplierIdToUpdate}`)
                .then((response) => {
                    this.form.invoiceBatchDetails.data[
                        this.invoiceBatchDetailIndexForSupplierUpdate
                    ].invoice.supplier = response.data;
                    this.invoiceBatchDetailIndexForSupplierUpdate = null;
                    this.supplierIdToUpdate = null;
                    this.dataInitialized = true;
                });
        },
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
        totalAmount(val) {
            this.form.total = val;
        },
    },

    computed: {
        initializationComplete() {
            return this.dataInitialized && this.invoiceBatchInitialized;
        },
        totalAmount() {
            return this.form.invoiceBatchDetails.data.reduce((prev, curr) => {
                return prev + curr.amount;
            }, 0.0);
        },
    },
};
</script>