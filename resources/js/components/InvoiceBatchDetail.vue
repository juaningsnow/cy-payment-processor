<template>
    <tr>
        <td>
            <select3
                class="custom-select"
                required
                :selected="defaultSupplier"
                data-msg-required="Enter Supplier"
                v-model.number="detail.supplierId"
                search="name"
                url="/api/suppliers"
                :disabled="isShow"
            ></select3>
        </td>
        <td>
            <datepicker
                input-class="form-control"
                :disabled="isShow"
                v-model="detail.date"
            ></datepicker>
        </td>
        <td>
            <input
                type="text"
                :disabled="isShow"
                class="form-control"
                placeholder="Invoice Number"
                v-model="detail.invoiceNumber"
            />
        </td>
        <td>
            <money
                class="form-control"
                v-model="detail.amount"
                v-bind="money"
            ></money>
        </td>
    </tr>
</template>

<script>
import Select3 from "./Select3";
import Datepicker from "vuejs-datepicker";
import { Money } from "v-money";

export default {
    components: { Select3, Datepicker },

    props: {
        detail: {
            type: Object,
        },
        index: {
            type: Number,
        },
        isShow: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            money: {
                decimal: ",",
                thousands: ".",
                prefix: "$ ",
                precision: 2,
                masked: false,
            },
            defaultSupplier: {},
        };
    },

    watch: {},

    methods: {
        remove() {
            this.$emit("remove");
        },
    },

    created() {
        if (this.detail.supplierId) {
            this.defaultSupplier = this.detail.supplier;
        }
    },
};
</script>