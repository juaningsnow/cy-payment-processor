import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import moment from 'moment';
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
    el: "#summary",

    components: {
        SaveButton,
        DeleteButton,
        Datepicker
    },

    data: {
        form: new Form({
            dateFrom: "",
            dateTo: "",
        }),
        trackedDateFrom: new Date,
        trackedDateTo: new Date,
        displayData: [],
        totalDisplay: 0,
        displayDataLoaded: true,
        dataInitialized: true,
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
    },

    computed: {
        initializationComplete() {
            return this.dataInitialized;
        },
    },

    methods: {
        formatDateFrom() {
            var d = new Date(this.trackedDateFrom);
            this.form.dateFrom = d.getUTCDate() + "-" + (d.getUTCMonth() + 1) + "-" + d.getUTCFullYear();
        },

        formateDateTo() {
            var d = new Date(this.trackedDateTo);
            this.form.dateTo = d.getUTCDate() + "-" + (d.getUTCMonth() + 1) + "-" + d.getUTCFullYear();
        },

        exportExcel() {
            let path = new URL(`${window.location.origin}/summary/excel/${this.form.dateFrom}/${this.form.dateTo}`);
            window.open(path);
        },
        exportCsv() {
            let path = new URL(`${window.location.origin}/summary/csv/${this.form.dateFrom}/${this.form.dateTo}`);
            window.open(path);
        },
        preview() {
            axios.get(`/api/invoice-batch-details?date_from=${this.form.dateFrom}&date_to=${this.form.dateTo}&include=invoiceBatch,supplier`).then(response => {
                this.displayData = response.data.data;
                this.totalDisplay = response.data.data.reduce((prev, curr) => {
                    return prev + curr.amount;
                }, 0.00)
                this.displayDataLoaded = true;
            });
        }
    },

    created() {
        this.formatDateFrom();
        this.formateDateTo();
    },
});