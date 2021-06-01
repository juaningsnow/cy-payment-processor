import Vue from 'vue';
import Index from "./components/Index.vue";
import { Form } from "./components/Form";
import InvoiceModal from "./components/InvoiceModal.vue";
import BatchModal from "./components/BatchModal.vue";
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
    components: { Index, InvoiceModal, BatchModal },
    data: {
        form: new Form({
            selected: [],
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
        showInvoiceModal: false,
        showBatchModal: false,
        selected: [],
        allSelected: false,
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

        markAsPaid() {
            this.form.selected = this.selected;
            this.$swal({
                title: "Mark Invoices as Paid",
                text: "You will not be able to revert this.",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes.',
                confirmButtonColor: '#f86c6b',
            }).then(response => {
                if (response.value) {
                    this.form.post(`/api/invoices/pay`).then(response => {
                        this.$swal({
                            title: "Invoices Updated!",
                            text: "Invoices has been marked as paid",
                            type: "success",
                        }).then(() => {
                            this.reloadData();
                            this.close();
                        });
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
            console.log(url, redirectUrl);
            this.form.deleteWithConfirmation(url).then(response => {
                this.form.successModal('Item has been removed').then(() =>
                    window.location = redirectUrl
                );
            });
        },
    }
});