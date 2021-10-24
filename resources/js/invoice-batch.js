import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import InvoiceBatchDetail from "./components/InvoiceBatchDetail.vue";
import Datepicker from "vuejs-datepicker";
import InvoiceListModal from "./components/InvoiceListModal.vue";
import moment from 'moment';
import SupplierModal from "./components/SupplierModal.vue";
import { defaultsDeep } from 'lodash';
Vue.use(VueSweetalert2);

Vue.config.devtools = true;
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

new Vue({
    el: "#invoice-batch",

    components: {
        SaveButton,
        DeleteButton,
        InvoiceBatchDetail,
        Datepicker,
        InvoiceListModal,
        SupplierModal,
    },

    data: {
        form: new Form({
            id: null,
            supplierId: null,
            batchName: null,
            date: moment(),
            name: null,
            invoiceBatchDetails: { data: [] },
        }),
        nullValue: null,
        counter: -1,
        dataInitialized: true,
        showInvoiceListModal: false,
        filterable: typeof filterable !== "undefined" ? filterable : "",
        baseUrl: typeof baseUrl !== "undefined" ? baseUrl : "",
        exportBaseUrl:
            typeof exportBaseUrl !== "undefined"
                ? exportBaseUrl
                : "",
        toLastPage:
            typeof toLastPage !== "undefined"
                ? toLastPage
                : false,
        sortAscending:
            typeof sortAscending !== "undefined"
                ? sortAscending
                : true,
        filters: typeof filters !== "undefined" ? filters : [],

        supplierSelections: [],
        suppliersInitialized: false,
        supplierIdToUpdate: null,
        invoiceBatchDetailIndexForSupplierUpdate: null,
        showSupplierModal: false,
        giroDate: null,
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
        totalAmount(val) {
            this.form.total = val;
        },
    },

    computed: {
        initializationComplete() {
            return this.dataInitialized && this.suppliersInitialized;
        },
        totalAmount() {
            return this.form.invoiceBatchDetails.data.reduce((prev, curr) => {
                return prev + curr.amount;
            }, 0.0);
        },
    },

    methods: {
        update() {
            this.form.patch("/api/invoice-batches/" + this.form.id).then(response => {
                this.$swal({
                    title: "Invoice Batch updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/invoice-batches/" + id));
            });
        },

        store() {
            this.form.post("/api/invoice-batches/").then(response => {
                this.$swal({
                    title: "Invoice Batch created!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/invoice-batches/" + response.data.id));
            }).catch(error => {
                this.$swal({
                    title: "Warning!",
                    text: error.message,
                    type: "warning"
                })
            });
        },

        copy() {
            window.location = "/invoice-batches/create?source_invoice_batch_id=" + this.form.id;
        },

        cancel() {
            this.form.confirm("Cancel this batch?").then(response => {
                if (response.value) {
                    this.form.patch("/api/invoice-batches/cancel/" + this.form.id).then(response => {
                        this.$swal({
                            title: "Invoice Batch Cancelled!",
                            text: "Changes saved to database.",
                            type: "success"
                        }).then(() => (window.location = "/invoice-batches/create?source_invoice_batch=" + id));
                    });
                }
            })
        },

        getUrlParams(prop) {
            var params = {};
            if (window.location.href.indexOf('?') < 0) {
                return undefined;
            }
            var search = decodeURIComponent(
                window.location.href.slice(window.location.href.indexOf('?') + 1)
            );
            var definitions = search.split('&');

            definitions.forEach(function (val, key) {
                var parts = val.split('=', 2);
                params[parts[0]] = parts[1];
            });

            return prop && prop in params ? params[prop] : undefined;
        },


        loadData(data) {
            this.form = new Form(data);
        },

        addDetails(details) {
            details.forEach(detail => {
                this.form.invoiceBatchDetails.data.push({
                    id: this.counter--,
                    invoiceId: detail.id,
                    invoice: detail
                });
            });
        },

        load(id) {
            this.form
                .get(
                    `/api/invoice-batches/${id}?include=invoiceBatchDetails.invoice.supplier`
                ).then(response => {
                    this.loadData(response.data);
                    if (response.data.generated) {
                        Swal.fire({
                            title: 'Reminder',
                            width: '800px',
                            html:
                                '<span>Please upload your payment file using file Upload in OCBC Velocity</span><br><img src="/img/instruction1.jpg" height="300" width="700"><br><img src="/img/instruction2.jpg" height="300" width="700">'
                        });
                    }
                    this.dataInitialized = true;
                });
        },

        validateData() {
            return new Promise((resolve, reject) => {
                this.form.post(`/api/invoice-batches/validate-export`).then(response => {
                    resolve();
                }).catch(error => {
                    this.$swal({
                        title: "Notice!",
                        text: error.message,
                        type: "error"
                    });
                    reject();
                });
            })
        },

        openNewPage() {
            return new Promise((resolve, reject) => {
                let path = new URL(`${window.location.origin}/invoice-batches/${id}/generate`);
                window.open(path);
                resolve();
            })
        },

        openNewPageGiro(date) {
            console.log(date);
            return new Promise((resolve, reject) => {
                // let path = new URL(`${window.location.origin}/invoice-batches/giro/${id}/generate/${date}`);
                // window.open(path);
                resolve();
            })
        },

        askFordate() {

        },

        exportGiroFile() {
            this.allSuppliersHasBankDetails().then(() => {
                if (!this.supplierIdToUpdate) {
                    this.validateData().then(() => {
                        Swal.fire({
                            title: 'Pick a date:',
                            type: 'question',
                            html: '<input id="datepicker" readonly class="swal2-input">',
                            customClass: 'swal2-overflow',
                            onOpen: function () {
                                $('#datepicker').datepicker({
                                    dateFormat: 'mm-dd-yy'
                                });
                            }
                        }).then(function (result) {
                            if (result.value) {
                                let date = $('#datepicker').val();
                                let openGiro = new Promise((resolve, reject) => {
                                    let path = new URL(`${window.location.origin}/invoice-batches/giro/${id}/generate/${date}`);
                                    window.open(path);
                                    resolve();
                                })
                                openGiro.then(() => {
                                    window.location = '/invoice-batches/' + this.form.id;
                                });
                            }
                        });
                    })
                }
            });


            // let date = $('#datepicker').val();
            // this.openNewPageGiro().then(() => {
            //     window.location = '/invoice-batches/' + this.form.id;
            // })
        },

        exportTextFile() {
            this.allSuppliersHasBankDetails().then(() => {
                if (!this.supplierIdToUpdate) {
                    this.validateData().then(() => {
                        this.openNewPage().then(() => {
                            window.location = '/invoice-batches/' + this.form.id;
                        })
                    })
                }
            });

        },

        analyzeSuppliers() {
            return new Promise((resolve, reject) => {
                if (!this.form.supplierId) {
                    this.form.invoiceBatchDetails.data.forEach(
                        (detail, index) => {
                            if (
                                !detail.invoice.supplier.bankId ||
                                !detail.invoice.supplier.accountNumber
                            ) {
                                console.log(detail);
                                this.invoiceBatchDetailIndexForSupplierUpdate =
                                    index;
                                this.supplierIdToUpdate =
                                    detail.invoice.supplier.id;
                                reject();
                            }
                        }
                    );
                    resolve();
                } else {
                    this.form
                        .get(`/api/suppliers/${this.form.supplierId}`)
                        .then((response) => {
                            if (
                                !response.data.bankId ||
                                !response.data.accountNumber
                            ) {
                                this.supplierIdToUpdate = response.data.id;
                                reject();
                            } else {
                                resolve();
                            }
                        });
                }
            });
        },
        allSuppliersHasBankDetails() {
            return new Promise((resolve, reject) => {
                this.analyzeSuppliers()
                    .then(() => {
                        resolve();
                    })
                    .catch((error) => {
                        if (this.supplierIdToUpdate) {
                            this.$swal({
                                title: "Missing data!",
                                text: "Need to Update Supplier Bank Details",
                                type: "warning",
                            }).then(() => {
                                this.showSupplierModal = true;
                            });
                        }
                    });
            });
        },

        reloadData() {
            this.load(this.form.id);
        },
    },

    created() {
        this.form.get(`/api/suppliers`).then(response => {
            this.supplierSelections = response.data;
            this.suppliersInitialized = true;
            if (id != null) {
                this.dataInitialized = false;
                this.isEdit = true;
                this.load(id);
            }
        });
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
        this.isEdit = typeof isEdit !== "undefined" ? isEdit : false;
        if (this.getUrlParams("source_invoice_batch_id") != undefined) {
            this.form.get(`/api/invoice-batches/${this.getUrlParams("source_invoice_batch_id")}?include=invoiceBatchDetails.invoice.supplier`).then(response => {
                if (response.data.cancelled) {
                    response.data.invoiceBatchDetails.data.forEach(data => {
                        this.form.invoiceBatchDetails.data.push({
                            id: this.counter--,
                            invoiceId: data.invoice.id,
                            invoice: data.invoice,
                        });
                    });
                } else {
                    this.$swal({
                        title: "Notice!",
                        text: "The Batch you want to copy has not yet been cancelled.",
                        type: "success"
                    });
                }
            });
        };
    },
});