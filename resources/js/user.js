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
    el: "#user",

    components: {
        SaveButton,
        DeleteButton,
    },

    data: {
        form: new Form({
            id: null,
            name: null,
            username: null,
            email: null,
            password: null,
            isAdmin: false,
        }),
        nullValue: null,
        trueValue: true,
        falseValue: false,
        dataInitialized: true,
        companySelections: [],
        companyInitialized: false,
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

        store() {
            this.form.post("/api/user-management").then(response => {
                this.$swal({
                    title: "User created!",
                    text: "User was saved.",
                    type: "success"
                }).then(() => window.location = "/user-management");
            }).catch((error) => {
                this.$swal({
                    title: "Error",
                    text: error.message,
                    type: "warning",
                });
            });
        },

        addCompany() {
            this.form.userCompanies.data.push({
                companyId: null,
                isActive: false
            });
        },

        removeCompany(index) {
            this.form.userCompanies.data.splice(index, 1);
        },

        update() {
            this.form.patch("/api/user-management/" + this.form.id).then(response => {
                this.$swal({
                    title: "User updated!",
                    text: "Changes saved to database.",
                    type: "success"
                }).then(() => (window.location = "/user-management/" + id));
            }).catch((error) => {
                this.$swal({
                    title: "Error",
                    text: error.message,
                    type: "warning",
                });
            });
        },
        loadData(data) {
            this.form = new Form(data);
        }
    },

    created() {
        this.form.get(`/api/companies`).then(response => {
            this.companySelections = response.data;
            this.companyInitialized = true;
            if (id != null) {
                this.dataInitialized = false;
                this.isEdit = true;
                this.form
                    .get(
                        "/api/user-management/" + id + "?include=userCompanies.company"
                    ).then(response => {
                        this.loadData(response.data);
                        this.dataInitialized = true;
                    });
            }
        }).catch(error => {
            console.log(error);
            this.companyInitialized = true;
        });
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
    },
});