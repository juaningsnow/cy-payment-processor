<template>
    <modal-window
        :title="title"
        size="xl"
        :form="form"
        @close="close"
        @save="save"
    >
        <index
            ref="index"
            :filterable="filterable"
            :export-base-url="exportBaseUrl"
            :base-url="baseUrl"
            :sorter="sorter"
            :sort-ascending="sortAscending"
            v-on:update-loading="(val) => (isLoading = val)"
            v-on:update-items="(val) => (items = val)"
        >
            <table class="table">
                <thead>
                    <tr>
                        <td @click="selectAll" class="text-center">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    @click="selectAll"
                                    v-model="allSelected"
                                    type="checkbox"
                                    id="flexCheckChecked"
                                />
                            </div>
                        </td>
                        <th>Invoice Number</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody v-if="!isLoading">
                    <tr
                        v-for="(item, index) in items"
                        @click="addOrRemoveToSelected(item)"
                        :key="index"
                    >
                        <td class="text-center">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    v-model="selected"
                                    :value="item"
                                    :id="item.id"
                                />
                            </div>
                        </td>
                        <td>{{ item.invoiceNumber }}</td>
                        <td>{{ item.supplier.name }}</td>
                        <td>{{ item.date }}</td>
                        <td class="text-right">{{ item.amount | numeric }}</td>
                    </tr>
                </tbody>
            </table>
        </index>
    </modal-window>
</template>
<script>
import Index from "./Index.vue";
import { Form } from "./Form";
import ModalWindow from "./ModalWindow";
export default {
    components: { Index, ModalWindow },

    props: {
        exportBaseUrl: {
            type: String,
            default: "",
        },
        toLastPage: {
            type: Boolean,
            default: false,
        },
        sortAscending: {
            type: Boolean,
            default: true,
        },
        filters: {
            type: Array,
            default: [],
        },
        baseUrl: {
            type: String,
            default: "",
        },
        filterable: {
            type: Array,
            default: [],
        },
        invoiceBatchId,
    },

    computed: {
        allIsSelected() {
            if (this.items.length > 0) {
                if (this.items.length == this.selected.length) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        },
    },

    data() {
        return {
            form: new Form({
                selected: [],
            }),
            items: [],
            totals: {},
            isLoading: true,
            sorter: "id",
            showInvoiceModal: false,
            showBatchModal: false,
            selected: [],
            allSelected: false,
            title: "Add Invoice",
        };
    },

    watch: {
        allIsSelected(val) {
            this.allSelected = val;
        },
    },

    methods: {
        close() {
            this.$emit("close");
            this.form.reset();
        },
        save() {
            this.form.selected = this.selected;
            this.form
                .patch(
                    `/api/invoice-batches/add-invoices/${this.invoiceBatchId}`
                )
                .then((response) => {
                    this.$swal({
                        title: "Invoice Batch updated!",
                        text: "Invoices has been added to this batch.",
                        type: "success",
                    }).then(() => {
                        this.selected = [];
                        this.$emit("reload-data", this.invoiceBatchId);
                        this.close();
                    });
                });
        },

        selectAll() {
            this.selected = [];
            if (!this.allSelected) {
                this.items.forEach((item) => {
                    this.selected.push(item);
                });
            }
            this.allSelected = !this.allSelected;
        },

        addOrRemoveToSelected(item) {
            let exists = this.selected.find((select) => select.id == item.id);
            if (!exists) {
                this.selected.push(item);
            } else {
                let index = this.selected.findIndex(
                    (select) => select.id == item.id
                );
                this.selected.splice(index, 1);
            }
        },

        setSorter(sorter) {
            if (sorter == this.sorter) this.sortAscending = !this.sortAscending;
            else this.sortAscending = true;
            this.sorter = sorter;
        },

        getSortIcon(column) {
            return {
                "fa-sort-up": column == this.sorter && this.sortAscending,
                "fa-sort-down": column == this.sorter && !this.sortAscending,
                "fa-sort": column != this.sorter,
            };
        },
    },

    created() {},
};
</script>