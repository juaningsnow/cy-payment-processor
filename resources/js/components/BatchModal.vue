<template>
    <modal-window
        :title="title"
        size="xl"
        :form="form"
        @close="close"
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
            >
                <small
                    class="form-text form-control-feedback"
                    v-for="(error, index) in form.errors.errors"
                    :key="index"
                    >{{ error[0] }}</small
                >
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="batchNumber">Batch No.</label>
                        <input
                            id="batchNumber"
                            type="text"
                            class="form-control"
                            v-model="form.batchName"
                            disabled
                            placeholder="Auto Generated"
                        />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <datepicker
                            id="date"
                            input-class="form-control"
                            :disabled="isShow"
                            :typeable="true"
                            v-model="form.date"
                        ></datepicker>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <label for="name">Name</label>
                    <input
                        id="name"
                        type="text"
                        class="form-control"
                        v-model="form.name"
                        placeholder="Enter Name"
                    />
                </div>
                <div class="col-6">
                    <label for="supplier">Redirect payment to</label>
                    <select
                        class="form-control select2"
                        v-model="form.supplierId"
                        style="width: 100%"
                        :disabled="isShow"
                    >
                        <option selected="selected" :value="nullValue">
                            -Select Supplier-
                        </option>
                        <option
                            v-for="(item, index) in supplierSelections"
                            :key="index"
                            :value="item.id"
                        >
                            {{ item.text }}
                        </option>
                    </select>
                </div>
            </div>
            <br />
            <table class="table table-responsive">
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
                        <td class="text-right">{{ totalAmount | numeric }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
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
import InvoiceBatchDetail from "./InvoiceBatchDetail2";
import { Form } from "./Form";
import moment from "moment";
import Datepicker from "vuejs-datepicker";

export default {
    components: { ModalWindow, InvoiceBatchDetail, Datepicker },

    props: {
        hasEnforcedFocus: {
            type: Boolean,
            default: false,
        },
        isShow: {
            type: Boolean,
            default: false,
        },
        selectedInvoices: {
            type: Array,
            default: [],
        },
    },

    data() {
        return {
            title: "Create Batch",
            form: new Form({
                id: null,
                batchName: null,
                date: moment(),
                total: 0,
                name: null,
                supplierId: null,
                invoiceBatchDetails: { data: [] },
            }),
            nullValue: null,
            dataInitialized: true,
            counter: -1,
            supplierSelections: [],
            suppliersInitialized: false,
        };
    },

    created() {
        this.dataInitialized = false;
        this.setDetails().then(() => {
            this.form
                .get(`/api/suppliers?limit=${Number.MAX_SAFE_INTEGER}`)
                .then((response) => {
                    this.supplierSelections = response.data;
                    this.suppliersInitialized = true;
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
                .post(`/api/invoice-batches`)
                .then((response) => {
                    this.$swal({
                        title: "Batch Created!",
                        text: "Invoice Batch has beend saved to database",
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
            return this.dataInitialized && this.suppliersInitialized;
        },
        totalAmount() {
            return this.form.invoiceBatchDetails.data.reduce((prev, curr) => {
                return prev + curr.amount;
            }, 0.0);
        },
    },
};
</script>