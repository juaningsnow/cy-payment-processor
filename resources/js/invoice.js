import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import Datepicker from 'vuejs-datepicker';
import vueFilePond, { setOptions } from "vue-filepond";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import InvoiceAttachmentModal from "./components/InvoiceAttachmentModal.vue";
import MarkAsPaidModal from "./components/MarkSingleInvoiceAsPaidModal.vue";
import AddToBatchModal from "./components/AddToBatchModal.vue";
import BatchModal from "./components/BatchModal.vue";
import VueSweetalert2 from "vue-sweetalert2";
Vue.use(VueSweetalert2);

const FilePond = vueFilePond(
    FilePondPluginImagePreview
);


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
    el: "#invoice",

    components: {
        SaveButton,
        DeleteButton,
        Datepicker,
        FilePond,
        MarkAsPaidModal,
        InvoiceAttachmentModal,
        AddToBatchModal,
        BatchModal
    },

    data: {
        form: new Form({
            id: "",
            supplierId: null,
            date: null,
            invoiceNumber: "",
            amount: 0.0,
            description: "",
            currencyId: null,
        }),
        csrfToken: $('input[name="_token"]').val(),
        myFiles: [],
        supplierSelections: [],
        suppliersInitialized: false,
        currencySelections: [],
        currencyInitialized: false,
        selected: [],
        dataInitialized: true,
        isShow: false,
        showInvoiceAttachmentModal: false,
        showMarkAsPaidModal: false,
        showAddToBatchModal: false,
        showBatchModal: false,
        selected: [],
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
            return this.dataInitialized && this.suppliersInitialized && this.currencyInitialized;
        },
    },

    methods: {
        handleFilePondInit: function () {
            console.log("FilePond has initialized");

        },
        update() {
            this.form.patch("/api/invoices/" + this.form.id).then(response => {
                Swal.fire({
                    title: "Invoice updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/invoices/" + id));
            });
        },

        destroy() {
            this.form.deleteWithConfirmation(`/api/invoices/${this.form.id}`).then(response => {
                this.form.successModal('Invoice has been deleted').then(() =>
                    window.location = `/invoices`
                );
            }).catch(error => {
                this.$swal({
                    title: "Warning!",
                    text: error.message,
                    type: "warning"
                })
            });
        },

        addToBatch() {
            Swal.fire({
                title: 'Do you want to create a new Batch?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    this.showBatchModal = true;
                } else if (result.isDenied) {
                    this.showAddToBatchModal = true;
                }
            })
        },

        refreshAttachments() {
            this.form.patch(`/api/invoices/refresh-attachments/${this.form.id}`).then(response => {
                Swal.fire({
                    title: "Payments and attachments refreshed!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/invoices/" + id));
            });
        },

        loadData(data) {
            this.form = new Form(data);
            this.selected = [
                this.form
            ];
        },
        reloadData(id) {
            this.dataInitialized = false;
            this.form
                .get(
                    "/api/invoices/" + id + "?include=invoiceCredits,invoicePayments,supplier,media,invoiceXeroAttachments"
                ).then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
        },

        removeFile(id) {
            this.form.patch(`/api/invoices/remove-attachment/${id}`).then(response => {
                Swal.fire({
                    title: "File Deleted!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => {
                    this.reloadData(this.form.id);
                });
            });
        },
    },

    created() {

        if (id != null) {
            this.isEdit = true;
            this.reloadData(id);
        }
        this.form.get(`/api/suppliers?limit=${Number.MAX_SAFE_INTEGER}`).then(response => {
            this.supplierSelections = response.data;
            this.suppliersInitialized = true;
            this.form.get(`/api/currencies?limit=${Number.MAX_SAFE_INTEGER}`).then(response => {
                this.currencySelections = response.data;
                this.currencyInitialized = true;
            });
        })
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
    },
});