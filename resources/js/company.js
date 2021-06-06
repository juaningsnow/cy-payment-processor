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
    el: "#company",

    components: {
        SaveButton,
        DeleteButton,
    },

    data: {
        form: new Form({
            id: null,
            name: "",
        }),
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
                    "/api/companies/" + id
                ).then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
        }
        this.isShow = typeof isShow !== "undefined" ? isShow : false;
    },
});