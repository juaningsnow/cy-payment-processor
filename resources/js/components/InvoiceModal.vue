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
                    v-for="(detail, index) in form.invoices.data"
                    is="invoice"
                    :key="detail.id"
                    :supplier-selections="supplierSelections"
                    :detail="detail"
                    :is-show="isShow"
                    :index="index"
                    @remove="form.invoices.data.splice(index, 1)"
                ></tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">Total</td>
                        <td class="text-right">{{ totalAmount | numeric }}</td>
                        <td></td>
                    </tr>
                    <tr v-if="!isShow">
                        <td colspan="5">
                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                @click="addDetail"
                            >
                                Add Detail
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <template slot="footer">
                <button
                    v-if="!isShow"
                    class="btn btn-success"
                    @click="save"
                    :disabled="form.isBusy"
                >
                    <div v-if="form.isSaving">
                        <i class="fas fa-circle-notch fa-spin"></i> Saving...
                    </div>
                    <div v-if="!form.isSaving"></div>
                </button>
                <button type="button" class="btn btn-secondary" @click="close">
                    Close
                </button>
            </template>
        </div>
    </modal-window>
</template>
<script>
import ModalWindow from "./ModalWindow";
import Invoice from "./Invoice";
import { Form } from "./Form";
import moment from "moment";

export default {
    components: { ModalWindow, Invoice },

    props: {
        hasEnforcedFocus: {
            type: Boolean,
            default: false,
        },
        isShow: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            title: "Add Invoices",
            form: new Form({
                invoices: { data: [] },
            }),
            supplierSelections: [],
            suppliersInitialized: false,
            dataInitialized: true,
            counter: -1,
        };
    },

    created() {
        this.form
            .get(`/api/suppliers?limit=${Number.MAX_SAFE_INTEGER}`)
            .then((response) => {
                this.supplierSelections = response.data;
                this.suppliersInitialized = true;
            });
    },

    methods: {
        close() {
            this.$emit("close");
            this.form.reset();
        },
        save() {
            this.form
                .post(`/api/invoices`)
                .then((response) => {
                    this.$swal({
                        title: "Invoices created!",
                        text: "Invoices was saved.",
                        type: "success",
                    }).then(() => {
                        this.close();
                        this.$emit("reload-data");
                    });
                })
                .catch((error) => {
                    this.$swal({
                        title: "Error",
                        text: error.message,
                        type: "danger",
                    });
                });
        },

        addDetail() {
            this.form.invoices.data.push({
                id: --this.counter,
                supplierId: null,
                date: moment().toString(),
                invoiceNumber: "",
                amount: 0.0,
                description: "",
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
            return this.dataInitialized && this.suppliersInitialized;
        },
        totalAmount() {
            return this.form.invoices.data.reduce((prev, curr) => {
                return prev + curr.amount;
            }, 0.0);
        },
    },
};
</script>