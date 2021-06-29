<template>
    <tr>
        <td>{{ companyBank.bank.name }}</td>
        <td>{{ companyBank.accountNumber }}</td>
        <td>{{ companyBank.xeroAccountCode }}</td>
        <td>
            <span v-if="companyBank.default" class="badge badge-info"
                >default</span
            >
            <button
                v-else
                type="button"
                @click="makeDefault(companyBank)"
                class="btn btn-success btn-sm"
            >
                Make Default
            </button>
        </td>
        <td>
            <button
                type="button"
                @click="showUpdateBankModal = true"
                class="btn btn-info btn-sm"
            >
                Edit Bank
            </button>
            <button
                type="button"
                @click="removeBank(companyBank)"
                class="btn btn-danger btn-sm"
            >
                <i class="fas fa-trash"></i>
            </button>
            <update-bank-modal
                :company-id="companyId"
                :company-bank="companyBank"
                v-if="showUpdateBankModal"
                @close="showUpdateBankModal = false"
                @reload-data="$emit('reload-data')"
            >
            </update-bank-modal>
        </td>
    </tr>
</template>
<script>
import UpdateBankModal from "./UpdateBankModal";

export default {
    components: {
        UpdateBankModal,
    },

    props: ["companyId", "companyBank"],

    data() {
        return {
            showUpdateBankModal: false,
        };
    },

    mounted() {
        console.log("Company Bank Row Mounted...");
    },

    methods: {
        removeBank(companyBank) {
            this.$emit("remove-bank", companyBank);
        },
    },
};
</script>