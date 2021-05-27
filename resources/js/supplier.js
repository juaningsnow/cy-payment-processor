import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
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
    el: "#supplier",

    components: {
        SaveButton,
        DeleteButton,
    },

    data: {
        form: new Form({
            id: null,
            name: "",
            purposeId: "",
            paymentType: "",
            accountNumber: "",
            bankId: "",
        }),
        dataInitialized: true,
        purposeSelections: [],
        purposeInitialized: false,
        bankSelections: [],
        banksInitialized: false,
        paymentTypes: [
            'FAST',
            'GIRO'
        ],
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
    },

    computed: {

        initializationComplete() {
            return this.dataInitialized && this.purposeInitialized && this.banksInitialized;
        },
    },

    methods: {

        store() {
            this.form.post("/api/suppliers").then(response => {
                this.$swal({
                    title: "Supplier created!",
                    text: "Supplier was saved.",
                    type: "success"
                }).then(() => window.location = "/suppliers");
            }).catch(error => {
                console.log(error);
            });
        },

        update() {
            this.form.patch("/api/suppliers/" + this.form.id).then(response => {
                this.$swal({
                    title: "Supplier updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/suppliers/" + id));
            });
        },
        loadData(data) {
            this.form = new Form(data);
        }
    },

    created() {

        this.form.get(`/api/banks?limit=${Number.MAX_SAFE_INTEGER}`).then(banksResponse => {
            this.bankSelections = banksResponse.data;
            this.banksInitialized = true;
            this.form.get(`/api/purposes?limit=${Number.MAX_SAFE_INTEGER}`).then(response => {
                this.purposeSelections = response.data;
                this.purposeInitialized = true;
                if (id != null) {
                    this.dataInitialized = false;
                    this.isEdit = true;
                    this.form
                        .get(
                            "/api/suppliers/" + id
                        ).then(response => {
                            this.loadData(response.data);
                            this.dataInitialized = true;
                        });
                }
            })
        })
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
    },
});