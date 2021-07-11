import Vue from 'vue';
import { Form } from "./components/Form";
import VueSweetalert2 from "vue-sweetalert2";
Vue.use(VueSweetalert2);
Vue.config.devtools = true;

new Vue({
    el: "#xero",
    components: {
    },

    data: {
        form: new Form({
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
        revokeApiConnection() {
            this.form.showAlert('Revoke Xero Api Connection?').then(response => {
                if (response.value) {
                    this.form.post('/api/companies/revoke').then(response => {
                        this.$swal({
                            title: "Success",
                            text: "Xero Connection has been revoked!",
                            type: "success"
                        }).then(() => (window.location = "/xero/"))
                    });
                }
            })
        }
    },

    created() {

    },
});