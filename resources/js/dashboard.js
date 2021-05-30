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
    el: "#dashboard",

    components: {
        SaveButton,
        DeleteButton,
    },

    data: {
        form: new Form({
            id: '',
            accountNumber: '',
            name: '',
            username: '',
            email: '',
            bankId: '',
            bank: '',
        }),
        dataInitialized: true,
        banksSelections: [],
        isShow: true,
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
        update() {
            this.form.patch("/api/users/" + this.form.id).then(response => {
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
        }
    },

    created() {
        this.dataInitialized = false;
        this.form.get(`/api/banks?limit=${Number.MAX_SAFE_INTEGER}`).then(response => {
            this.banksSelections = response.data;
            this.form.get(`/api/users/logged-in?include=bank`).then(response => {
                this.loadData(response.data);
                this.dataInitialized = true;
            });
        });
    },
});