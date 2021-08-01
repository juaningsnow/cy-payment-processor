import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import AddBankModal from "./components/AddBankModal.vue";
import CompanyBankRow from "./components/CompanyBankRow.vue";
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
    el: "#company",

    components: {
        SaveButton,
        DeleteButton,
        AddBankModal,
        CompanyBankRow,
    },

    data: {
        form: new Form({
            id: null,
            name: "",
            companyOwners: { data: [] },
            cashAccountId: null,
            bankAccountId: null,
        }),
        nullValue: null,
        showBankModal: false,
        dataInitialized: true,
        accountSelections: [],
        accountsInitialized: false,
        counter: -1,
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

        addOwner() {
            this.form.companyOwners.data.push({
                id: this.counter--,
                name: "",
                accountId: null,
            });
        },

        refreshCurrencies() {
            this.form.patch(`/api/companies/refresh-currencies/${this.form.id}`).then(response => {
                this.$swal({
                    title: "Currencies refreshed!",
                    text: "",
                    type: "success"
                }).then(() => window.location = `/companies/${this.form.id}`);
            }).catch(error => {
                console.log(error);
            });
        },

        store() {
            this.form.post("/api/companies").then(response => {
                this.$swal({
                    title: "Company created!",
                    text: "Company was saved.",
                    type: "success"
                }).then(() => window.location = "/companies");
            }).catch(error => {
                console.log(error);
            });
        },

        update() {
            this.form.patch("/api/companies/" + this.form.id).then(response => {
                this.$swal({
                    title: "Company updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/companies/" + id));
            });
        },
        makeDefault(companyBank) {
            this.form
                .get(`/api/companies/make-default/${this.form.id}/${companyBank.bankId}`)
                .then((response) => {
                    this.$swal({
                        title: "Bank was saved!",
                        text: "Default Bank Changed.",
                        type: "success",
                    }).then(() => {
                        this.load();
                    });
                })
                .catch((error) => {
                    this.$swal({
                        title: "Error",
                        text: error.message,
                        type: "danger",
                    });
                });
        },
        removeBank(companyBank) {
            if (companyBank.default) {
                this.$swal({
                    title: "Warning",
                    text: "Cannot Remove Default Bank!",
                    type: 'warning'
                });
            } else {
                this.form
                    .get(`/api/companies/detach-bank/${this.form.id}/${companyBank.bankId}`)
                    .then((response) => {
                        this.$swal({
                            title: "Bank Removed!",
                            text: "Bank was saved.",
                            type: "success",
                        }).then(() => {
                            this.load();
                        });
                    })
                    .catch((error) => {
                        this.$swal({
                            title: "Error",
                            text: error.message,
                            type: "danger",
                        });
                    });
            }
        },
        loadData(data) {
            this.form = new Form(data);
        },

        load() {
            this.dataInitialized = false;
            this.isEdit = true;
            this.form
                .get(
                    "/api/companies/" + this.form.id + "?include=currencies,companyOwners,companyBanks.bank,companyBanks.account,banks"
                ).then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
        },
    },

    created() {

        if (id != null) {
            this.dataInitialized = false;
            this.isEdit = true;
            this.form.get(`/api/accounts?limit=${Number.MAX_SAFE_INTEGER}`).then((response) => {
                this.accountSelections = response.data;
                this.accountsInitialized = true;
                this.form.get("/api/companies/" + id + "?include=currencies,companyOwners,companyBanks.bank,companyBanks.account,banks").then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
            });

        }
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
    },
});