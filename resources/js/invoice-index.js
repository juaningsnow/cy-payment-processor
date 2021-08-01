import Vue from 'vue';
import Index from "./components/Index.vue";
import { Form } from "./components/Form";
import InvoiceModal from "./components/InvoiceModal.vue";
import BatchModal from "./components/BatchModal.vue";
import MarkAsPaidModal from "./components/MarkAsPaidModal.vue";
import VueSweetalert2 from "vue-sweetalert2";
Vue.use(VueSweetalert2);
Vue.filter("numeric", function (value, decimals = 2) {
    if (isNaN(Number(value))) {
        return value;
    }
    var formatter = new Intl.NumberFormat("en-US", {
        style: "decimal",
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
    return formatter.format(value);
});

Vue.config.devtools = true;
new Vue({
    el: "#index",
    components: { Index, InvoiceModal, BatchModal, MarkAsPaidModal },
    data: {
        form: new Form({
            selected: [],
            paidBy: null,
        }),
        filterable: filterable,
        baseUrl: baseUrl,
        exportBaseUrl: typeof exportBaseUrl !== "undefined" ? exportBaseUrl : '',
        items: [],
        totals: {},
        isLoading: true,
        toLastPage: typeof toLastPage !== "undefined" ? toLastPage : false,
        sorter: 'id',
        sortAscending: typeof sortAscending !== "undefined" ? sortAscending : true,
        filters: typeof filters !== "undefined" ? filters : [],
        companyId: typeof companyId !== "undefined" ? companyId : null,
        showInvoiceModal: false,
        showBatchModal: false,
        selected: [],
        allSelected: false,
        showMarkAsPaidModal: false,
    },

    watch: {
        allIsSelected(val) {
            this.allSelected = val;
        }
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

    methods: {
        selectAll() {
            this.selected = [];
            if (!this.allSelected) {
                this.items.forEach(item => {
                    this.selected.push(item);
                })
            }
            this.allSelected = !this.allSelected;
        },

        refreshInvoices() {
            this.form.confirm('This will delete all the invoices in your active company and will retrieve outstanding invoices from Xero').then(response => {
                if (response.value) {
                    this.form.post(`/api/invoices/refresh-invoices`).then(response => {
                        this.form.successModal('Invoices has been refreshed').then(() =>
                            window.location = `/invoices`
                        );
                    })
                }
            });
        },

        addOrRemoveToSelected(item) {
            let exists = this.selected.find(select => select.id == item.id);
            if (!exists) {
                this.selected.push(item);
            } else {
                let index = this.selected.findIndex(select => select.id == item.id);
                this.selected.splice(index, 1);
            }
        },

        reloadData() {
            this.selected = [];
            this.$refs.index.reloadIndex();
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
                "fa-sort": column != this.sorter
            };
        },

        destroy(url, redirectUrl) {
            this.form.deleteWithConfirmation(url).then(response => {
                this.form.successModal('Item has been removed').then(() =>
                    window.location = redirectUrl
                );
            });
        },

        deleteAll() {
            this.form.selected = this.selected;
            this.form.confirm().then(response => {
                if (response.value) {
                    this.form.post(`/api/invoices/destroy-multiple`).then(response => {
                        this.form.successModal('Items has been removed').then(() =>
                            window.location = `/invoices`
                        );
                    })
                }
            });
        },
    }
});