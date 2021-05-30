import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import Datepicker from 'vuejs-datepicker';
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
        Datepicker
    },

    data: {
        form: new Form({
            id: "",
            supplierId: null,
            date: null,
            invoiceNumber: "",
            amount: 0.0,
            description: "",
        }),
        supplierSelections: [],
        suppliersInitialized: false,
        dataInitialized: true,
        isShow: false,
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
    },

    methods: {
        update() {
            this.form.patch("/api/invoices/" + this.form.id).then(response => {
                this.$swal({
                    title: "Invoice updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/invoices/" + id));
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
                    "/api/invoices/" + id + "?include=supplier"
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