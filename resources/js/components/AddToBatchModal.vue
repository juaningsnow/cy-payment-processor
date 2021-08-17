<template>
    <modal-window :title="title" :form="form" @close="close" @save="save">
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
                <label for="owner">Amount</label>
                <input
                    type="number"
                    class="form-control text-right"
                    placeholder="Amount"
                    v-model.number="form.amount"
                />
            </div>
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

export default {
    components: { ModalWindow },

    props: {
        invoiceId: {
            type: Number,
            default: 0,
        },
    },

    data() {
        return {
            title: "Add to Batch",
            form: new Form({
                invoiceId: null,
                invoiceBatchId: null,
                amount: 0,
            }),
            nullValue: null,
            invoiceBatchSelection: [],
            invoiceBatchInitialized: false,
            dataInitialized: true,
        };
    },

    created() {
        this.form.invoiceId = this.invoiceId;
        this.form.get(`/api/invoices/${this.invoiceId}`).then((response) => {
            console.log(response);
            this.form.amount = response.data.amountDue;
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
        close() {
            this.$emit("close");
            this.form.reset();
        },
        save() {
            this.form.post(`/api/invoice-batches/add`).then((response) => {
                this.$swal({
                    title: "Invoice Added!",
                    text: "Invoices has been added to batch",
                    type: "success",
                }).then(() => {
                    this.$emit("reload-data", this.invoiceId);
                    this.close();
                });
            });
        },
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
    },

    computed: {
        initializationComplete() {
            return this.dataInitialized && this.invoiceBatchInitialized;
        },
    },
};
</script>