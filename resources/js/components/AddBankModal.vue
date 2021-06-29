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
                    <label for="bank">Bank</label>
                    <select
                        class="form-control select2"
                        v-model="form.bankId"
                        style="width: 100%"
                    >
                        <option selected="selected" disabled :value="nullValue">
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
            <div class="row">
                <div class="form-group">
                    <label for="accountNumber">Account No.</label>
                    <input
                        id="accountNumber"
                        type="text"
                        class="form-control"
                        v-model="form.accountNumber"
                        placeholder="Account Number"
                    />
                </div>
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
        userId: {
            type: Number,
            default: null,
        },
    },

    data() {
        return {
            title: "Add Bank",
            form: new Form({
                bankId: null,
                accountNumber: null,
            }),
            nullValue: null,
            bankSelections: [],
            banksInitialized: false,
            dataInitialized: true,
        };
    },

    created() {
        this.form.get(`/api/banks/user`).then((response) => {
            this.bankSelections = response.data;
            this.banksInitialized = true;
        });
    },

    methods: {
        close() {
            this.$emit("close");
            this.form.reset();
        },
        save() {
            this.form
                .patch(`/api/companies/attach-bank/${this.userId}`)
                .then((response) => {
                    this.$swal({
                        title: "Bank Added!",
                        text: "Bank was saved.",
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
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
    },

    computed: {
        initializationComplete() {
            return this.dataInitialized && this.banksInitialized;
        },
    },
};
</script>