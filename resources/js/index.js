import Vue from 'vue';
import Index from "./components/Index.vue";
import { Form } from "./components/Form";
import VueSweetalert2 from "vue-sweetalert2";
Vue.use(VueSweetalert2);
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

Vue.config.devtools = true;
new Vue({
    el: "#index",
    components: { Index },
    data: {
        form: new Form(),
        filterable: filterable,
        baseUrl: baseUrl,
        exportBaseUrl: typeof exportBaseUrl !== "undefined" ? exportBaseUrl : '',
        items: [],
        totals: {},
        isLoading: true,
        toLastPage: typeof toLastPage !== "undefined" ? toLastPage : false,
        sorter: 'id',
        sortAscending: typeof sortAscending !== "undefined" ? sortAscending : true,
        filters: typeof filters !== "undefined" ? filters : [],
    },

    methods: {

        setSorter(sorter) {
            if (sorter == this.sorter) this.sortAscending = !this.sortAscending;
            else this.sortAscending = true;
            this.sorter = sorter;
        },

        getSortIcon(column) {
            return {
                "fa-sort-up": column == this.sorter && this.sortAscending,
                "fa-sort-down": column == this.sorter && !this.sortAscending,
                "fa-sort": column != this.sorter
            };
        },

        destroy(url, redirectUrl) {
            this.form.deleteWithConfirmation(url).then(response => {
                this.form.successModal('Item has been removed').then(() =>
                    window.location = redirectUrl
                );
            }).catch(error => {
                this.$swal({
                    title: "Warning!",
                    text: error.message,
                    type: "warning"
                })
            });
        },

        refreshCreditNotes() {
            this.form.post(`/api/credit-notes/refresh-credit-notes`).then(response => {
                this.form.successModal('Credit Notes has been refreshed').then(() =>
                    window.location = `/credit-notes`
                );
            });
        },
    }
});