<template>
    <tbody>
        <tr>
            <td>
                <input
                    type="text"
                    :disabled="isShow"
                    class="form-control"
                    placeholder="Supplier"
                    v-model="detail.supplier.name"
                />
            </td>
            <td>
                <datepicker
                    input-class="form-control"
                    :disabled="isShow"
                    :typeable="true"
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
                <input
                    type="number"
                    :disabled="isShow"
                    class="form-control text-right"
                    placeholder="Amount"
                    v-model.number="detail.amount"
                />
            </td>
            <td rowspan="2" class="text-right align-middle">
                <button
                    type="button"
                    :disabled="!isEdit"
                    class="btn btn-danger btn-sm"
                    @click="remove"
                >
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
        <tr>
            <td>Remarks:</td>
            <td colspan="3">
                <input
                    type="text"
                    :disabled="isShow"
                    class="form-control"
                    placeholder="Remarks"
                    v-model="detail.description"
                />
            </td>
        </tr>
    </tbody>
</template>
<script>
import Datepicker from "vuejs-datepicker";

export default {
    components: { Datepicker },

    props: {
        detail: {
            type: Object,
        },
        index: {
            type: Number,
        },
        isShow: {
            type: Boolean,
            default: true,
        },
        isEdit: {
            type: Boolean,
            default: true,
        },
    },

    data() {
        return {};
    },

    watch: {},

    methods: {
        remove() {
            this.$emit("remove");
        },
    },

    created() {},
};
</script>