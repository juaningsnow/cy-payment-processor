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
                <div class="form-group">
                    <label>Paid By:</label>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            id="cash"
                            name="radio1"
                            v-model="form.paidBy"
                            :disabled="!company.cashAccountId"
                            value="Cash"
                        />
                        <label for="cash" class="form-check-label">Cash</label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            id="bank"
                            name="radio1"
                            v-model="form.paidBy"
                            :disabled="!company.bankAccountId"
                            value="Bank"
                        />
                        <label for="bank" class="form-check-label">Bank</label>
                    </div>
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="radio1"
                            v-model="form.paidBy"
                            value="Other"
                            id="owner"
                            :disabled="ownerSelection.length < 1"
                        />
                        <label for="owner" class="form-check-label"
                            >Other</label
                        >
                    </div>
                </div>
            </div>
            <div class="row" v-if="form.paidBy == 'Other'">
                <label for="owner">Owner</label>
                <select
                    class="form-control select2"
                    v-model="form.ownerId"
                    style="width: 100%"
                >
                    <option selected="selected" disabled :value="nullValue">
                        -Select Owner-
                    </option>
                    <option
                        v-for="(item, index) in ownerSelection"
                        :key="index"
                        :value="item.id"
                    >
                        {{ item.name }}
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
        selected: {
            type: Array,
            default: [],
        },
        companyId: {
            type: Number,
            default: null,
        },
    },

    data() {
        return {
            title: "Mark as Paid",
            form: new Form({
                selected: [],
                paidBy: "Cash",
                ownerId: null,
            }),
            company: null,
            nullValue: null,
            ownerSelection: [],
            ownersInitialized: false,
            dataInitialized: true,
        };
    },

    created() {
        this.form.selected = this.selected;
        this.form
            .get(`/api/companies/${this.companyId}?include=companyOwners`)
            .then((response) => {
                this.company = response.data;
                this.ownerSelection = response.data.companyOwners.data;
                this.ownersInitialized = true;
                if (this.company.cashAccountId == null) {
                    this.form.paidBy = null;
                }
                this.dataInitialized = true;
            });
    },

    methods: {
        close() {
            this.$emit("close");
            this.form.reset();
        },
        save() {
            this.form.post(`/api/invoices/pay`).then((response) => {
                this.$swal({
                    title: "Invoices Updated!",
                    text: "Invoices has been marked as paid",
                    type: "success",
                }).then(() => {
                    this.$emit("reload-data");
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
            return this.dataInitialized && this.ownersInitialized;
        },
    },
};
</script>