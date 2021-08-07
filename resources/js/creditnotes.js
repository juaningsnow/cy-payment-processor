import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import Datepicker from 'vuejs-datepicker';
import moment from 'moment';
import AllocationModal from "./components/AllocationModal.vue";


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
    el: "#credit-note",

    components: {
        SaveButton,
        DeleteButton,
        Datepicker,
        AllocationModal,
    },

    data: {
        form: new Form({
            id: "",
            date: moment(),
            supplierId: null,
            currencyId: null,
            companyId: null,
            status: null,
            appliedAmount: null,
            total: null,
            creditNoteAllocations: {
                data: [],
            },
        }),
        dataInitialized: true,
        showAllocationModal: false,
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
            return this.dataInitialized;
        },
    },

    methods: {
        update() {
            this.form.patch("/api/credit-notes/" + this.form.id).then(response => {
                this.$swal({
                    title: "Credit note updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => this.reloadData(id));
            });
        },

        syncXeroData() {
            this.form.patch(`/api/credit-notes/sync/${this.form.id}`).then(response => {
                this.$swal({
                    title: "data has been refreshed!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/credit-notes/" + id));
            });
        },

        loadData(data) {
            this.form = new Form(data);
        },

        addDetail(id, invoiceId, amount) {
            return new Promise((resolve, reject) => {
                this.form.creditNoteAllocations.data.push({
                    id: id,
                    invoiceId: invoiceId,
                    amount: amount
                });
                resolve();
            });
        },

        addCreditNoteAllocation(id, invoiceId, amount) {
            this.addDetail(id, invoiceId, amount).then(() => {
                this.update();
            });
        },

        reloadData(id) {
            this.dataInitialized = false;
            this.form
                .get(
                    "/api/credit-notes/" + id + "?include=supplier,currency,company,creditNoteAllocations.invoice"
                ).then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
        },
    },

    created() {

        if (id != null) {
            this.isEdit = true;
            this.reloadData(id);
        }
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
    },
});