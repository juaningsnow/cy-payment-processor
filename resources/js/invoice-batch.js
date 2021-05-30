import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import InvoiceBatchDetail from "./components/InvoiceBatchDetail.vue";
import Datepicker from "vuejs-datepicker";
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
        Datepicker
    },

    data: {
        form: new Form({
            id: null,
            batchName: null,
            date: null,
            invoiceBatchDetails: { data: [] },
        }),
        dataInitialized: true,
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
            return this.dataInitialized;
        },
        totalAmount() {
            return this.form.invoiceBatchDetails.data.reduce((prev, curr) => {
                return prev + curr.invoice.amount;
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
        loadData(data) {
            this.form = new Form(data);
        },

        exportTextFile() {
            let path = new URL(`${window.location.origin}/invoice-batches/${id}/generate`);
            window.open(path);
        },
    },

    created() {

        if (id != null) {
            this.dataInitialized = false;
            this.isEdit = true;
            this.form
                .get(
                    `/api/invoice-batches/${id}?include=invoiceBatchDetails.invoice.supplier`
                ).then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
        }
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
        this.isEdit = typeof isEdit !== "undefined" ? isEdit : false;
    },
});