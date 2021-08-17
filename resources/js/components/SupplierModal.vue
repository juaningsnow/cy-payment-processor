<template>
    <modal-window
        :title="title"
        size="lg"
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
                >
                    {{ error[0] }}
                </small>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input
                    type="text"
                    :disabled="isShow"
                    class="form-control"
                    placeholder="Name"
                    v-model="form.name"
                />
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="text"
                    :disabled="isShow"
                    class="form-control"
                    placeholder="Email"
                    v-model="form.email"
                />
            </div>

            <div class="form-group">
                <label for="paymentType">Purpose</label>
                <select
                    class="form-control select2"
                    :disabled="isShow"
                    v-model="form.purposeId"
                >
                    <option selected="selected" disabled :value="null">
                        -Select Purpose-
                    </option>
                    <option
                        v-for="(item, index) in purposeSelections"
                        :key="index"
                        :value="item.id"
                    >
                        {{ item.description }} ({{ item.name }})
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="paymentType">Payment Type</label>
                <select
                    class="form-control select2"
                    :disabled="isShow"
                    v-model="form.paymentType"
                >
                    <option selected="selected" disabled :value="null">
                        -Select Payment Type-
                    </option>
                    <option
                        v-for="(item, index) in paymentTypes"
                        :key="index"
                        :value="item"
                    >
                        {{ item }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="account">Xero Account</label>
                <select
                    class="form-control select2"
                    :disabled="isShow"
                    v-model="form.accountId"
                >
                    <option selected="selected" disabled :value="null">
                        -Select Account-
                    </option>
                    <option
                        v-for="(item, index) in accountSelections"
                        :key="index"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="accountNumber">Account Number</label>
                <input
                    type="text"
                    :disabled="isShow"
                    class="form-control"
                    placeholder="Account Number"
                    v-model="form.accountNumber"
                />
            </div>

            <div class="form-group">
                <label for="paymentType">Bank</label>
                <select
                    class="form-control select2"
                    :disabled="isShow"
                    v-model="form.bankId"
                >
                    <option selected="selected" disabled :value="null">
                        -Select Bank-
                    </option>
                    <option
                        v-for="(item, index) in bankSelections"
                        :key="index"
                        :value="item.id"
                    >
                        {{ item.name }} ({{ item.swift }})
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
        supplierId: Number,
    },

    data() {
        return {
            title: "Need to Update this Supplier",
            form: new Form({
                id: null,
                name: "",
                email: "",
                purposeId: "",
                paymentType: "",
                accountNumber: "",
                bankId: "",
                accountId: "",
            }),
            dataInitialized: true,
            purposeSelections: [],
            purposeInitialized: false,
            bankSelections: [],
            banksInitialized: false,
            paymentTypes: ["FAST", "GIRO"],
            accountSelections: [],
            accountsInitialized: false,
        };
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
    },

    computed: {
        initializationComplete() {
            return (
                this.dataInitialized &&
                this.purposeInitialized &&
                this.banksInitialized &&
                this.accountsInitialized
            );
        },
    },

    methods: {
        save() {
            this.form
                .patch("/api/suppliers/" + this.form.id)
                .then((response) => {
                    this.$swal({
                        title: "Supplier updated!",
                        text: "Changes saved to database.",
                        type: "success",
                    }).then(() => {
                        this.$emit("reload-data");
                        this.close();
                    });
                });
        },
        close() {
            this.$emit("close");
            this.form.reset();
        },

        loadData(data) {
            this.form = new Form(data);
        },
    },

    created() {
        this.dataInitialized = false;
        this.form
            .get(`/api/banks?limit=${Number.MAX_SAFE_INTEGER}`)
            .then((banksResponse) => {
                this.bankSelections = banksResponse.data;
                this.banksInitialized = true;
                this.form
                    .get(`/api/accounts?limit=${Number.MAX_SAFE_INTEGER}`)
                    .then((response) => {
                        this.accountSelections = response.data;
                        this.accountsInitialized = true;
                        this.form
                            .get(
                                `/api/purposes?limit=${Number.MAX_SAFE_INTEGER}`
                            )
                            .then((response) => {
                                this.purposeSelections = response.data;
                                this.purposeInitialized = true;
                                this.isEdit = true;
                                this.form
                                    .get("/api/suppliers/" + this.supplierId)
                                    .then((response) => {
                                        this.loadData(response.data);
                                        this.dataInitialized = true;
                                    });
                            });
                    });
            });
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
    },
};
</script>
