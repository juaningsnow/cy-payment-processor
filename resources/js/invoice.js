import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import Datepicker from 'vuejs-datepicker';
import vueFilePond, { setOptions } from "vue-filepond";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import InvoiceAttachmentModal from "./components/InvoiceAttachmentModal.vue";

const FilePond = vueFilePond(
    FilePondPluginImagePreview
);

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
    el: "#invoice",

    components: {
        SaveButton,
        DeleteButton,
        Datepicker,
        FilePond,
        InvoiceAttachmentModal
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
        dataInitialized: true,
        isShow: false,
        showInvoiceAttachmentModal: false,
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
                this.$swal({
                    title: "Invoice updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/invoices/" + id));
            });
        },

        refreshAttachments() {
            this.form.patch(`/api/invoices/refresh-attachments/${this.form.id}`).then(response => {
                this.$swal({
                    title: "attachments refreshed!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/invoices/" + id));
            });
        },

        loadData(data) {
            this.form = new Form(data);
        },
        reloadData(id) {
            this.dataInitialized = false;
            this.form
                .get(
                    "/api/invoices/" + id + "?include=invoicePayments,supplier,media,invoiceXeroAttachments"
                ).then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
        },

        removeFile(id) {
            this.form.patch(`/api/invoices/remove-attachment/${id}`).then(response => {
                this.$swal({
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