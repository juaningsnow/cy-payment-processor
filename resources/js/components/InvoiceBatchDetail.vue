<template>
    <tr>
        <td>
            <select
                class="form-control select2"
                v-model="detail.supplierId"
                style="width: 100%"
            >
                <option selected="selected" disabled :value="null">
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
        <td class="text-right">
            <button type="button" class="btn btn-danger btn-sm" @click="remove">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
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
            default: false,
        },
        supplierSelections: {
            type: Array,
            default: [],
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

    created() {
        if (this.detail.supplierId) {
            this.defaultSupplier = this.detail.supplier;
        }
    },
};
</script>