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

            <div class="form-group">
                <label for="bank">Invoice</label>
                <select
                    class="form-control select2"
                    v-model="invoiceId"
                    style="width: 100%"
                >
                    <option selected="selected" disabled :value="nullValue">
                        -Select Invoice-
                    </option>
                    <option
                        v-for="(item, index) in invoiceSelections"
                        :key="index"
                        :value="item.id"
                    >
                        {{ item.invoiceNumber }} (Amount Due:
                        {{ item.amountDue }})
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount.</label>
                <input
                    type="number"
                    class="form-control text-right"
                    placeholder="Amount"
                    v-model.number="amount"
                />
            </div>
        </div>
    </modal-window>
</template>
<script>
import ModalWindow from "./ModalWindow";
import { Form } from "./Form";

export default {
    components: { ModalWindow },

    props: {
        creditNoteAllocation: {
            type: Object,
            default: () => ({
                id: -1,
                invoiceId: null,
                amount: null,
            }),
        },
        supplierId: {
            type: Number,
            default: 0,
        },
    },

    data() {
        return {
            title: "Credit Note Allocation",
            form: new Form(),
            id: this.creditNoteAllocation.id,
            invoiceId: this.creditNoteAllocation.invoiceId,
            amount: this.creditNoteAllocation.amount,
            nullValue: null,
            dataInitialized: true,
            invoiceSelections: [],
            invoicesInitialized: false,
        };
    },

    created() {
        this.form
            .get(
                `/api/invoices?no_invoice_batch_detail_or_cancelled=1&paid=0&supplier_id=${this.supplierId}`
            )
            .then((response) => {
                this.invoiceSelections = response.data;
                this.invoicesInitialized = true;
            });
    },

    methods: {
        close() {
            this.$emit("close");
            this.form.reset();
        },
        save() {
            this.$emit(
                "add-credit-note-allocation",
                this.id,
                this.invoiceId,
                this.amount
            );
            this.close();
        },
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
    },

    computed: {
        initializationComplete() {
            return this.dataInitialized && this.invoicesInitialized;
        },
    },
};
</script>