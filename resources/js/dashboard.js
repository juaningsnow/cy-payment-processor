import Vue from 'vue';
import { Form } from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import VueSweetalert2 from "vue-sweetalert2";
import AddBankModal from "./components/AddBankModal.vue";
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
    el: "#dashboard",

    components: {
        SaveButton,
        DeleteButton,
        AddBankModal
    },

    data: {
        form: new Form({
            id: '',
            name: '',
            username: '',
            email: '',
        }),
        dataInitialized: true,
        banksSelections: [],
        isShow: true,
        showBankModal: false,
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
        toggleEdit() {
            this.isShow = !this.isShow;
        },
        makeDefault(userBank) {
            this.form
                .get(`/api/user-management/make-default/${this.form.id}/${userBank.bankId}`)
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
        removeBank(userBank) {
            if (userBank.default) {
                this.$swal({
                    title: "Warning",
                    text: "Cannot Remove Default Bank!",
                    type: 'warning'
                });
            } else {
                this.form
                    .get(`/api/user-management/detach-bank/${this.form.id}/${userBank.bankId}`)
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
        update() {
            this.form.patch("/api/user-management/" + this.form.id).then(response => {
                this.$swal({
                    title: "User updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(
                    this.isShow = true
                );
            });
        },
        loadData(data) {
            this.form = new Form(data);
        },
        load() {
            this.dataInitialized = false;
            this.form.get(`/api/user-management/logged-in?include=banks,userBanks.bank`).then(response => {
                this.loadData(response.data);
                this.dataInitialized = true;
            });
        }
    },

    created() {
        this.load()
    },
});