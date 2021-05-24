import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import Datepicker from 'vuejs-datepicker';
import moment from 'moment';
import InvoiceBatchDetail from './components/InvoiceBatchDetail';
Vue.use(VueSweetalert2);

Vue.config.devtools = true;

new Vue({
    el: "#invoice",

    components: {
        SaveButton,
        DeleteButton,
        Datepicker,
        InvoiceBatchDetail
    },

    data: {
        form: new Form({
            id: null,
            batchName: "",
            date: moment(),
            total: 0,
            invoiceBatchDetails: {
                data: []
            }
        }),
        counter: -1,
        supplierSelections: [],
        suppliersInitialized: false,
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
        totalAmount() {
            if (this.form.invoiceBatchDetails.length > 0) {
                return this.form.invoiceBatchDetails.data.reduce((prev, curr) => {
                    return prev + curr.amount;
                }, 0.00);
            } else {
                return 0;
            }
        },
        initializationComplete() {
            return this.dataInitialized && this.suppliersInitialized;
        },
    },

    methods: {

        addDetail() {
            this.form.invoiceBatchDetails.data.push({
                id: --this.counter,
                supplierId: null,
                date: moment().toString(),
                invoiceNumber: "",
                amount: 0.00
            });
        },

        store() {
            this.form.post("/api/invoice-batches").then(response => {
                this.$swal({
                    title: "Invoice Batch created!",
                    text: "Invoice Batch was saved.",
                    type: "success"
                }).then(() => window.location = "/invoice-batches");
            }).catch(error => {
                console.log(error);
            });
        },

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
        }
    },

    created() {

        if (id != null) {
            this.dataInitialized = false;
            this.isEdit = true;
            this.form
                .get(
                    "/api/invoice-batches/" + id
                ).then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
        }
        this.form.get(`/api/suppliers?limit=${Number.MAX_SAFE_INTEGER}`).then(response => {
            this.supplierSelections = response.data;
            this.suppliersInitialized = true;
        })
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
    },
});