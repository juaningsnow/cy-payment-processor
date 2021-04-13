import Vue from 'vue';
import Index from "./components/Index.vue";
import {Form} from "./components/Form";

Vue.config.devtools = true;
new Vue({
    el: "#index",
    components: { Index },
    data: {
        form: new Form(),
        filterable: [
            {id: 'keyword', text: 'Keyword'},
            {id: 'category', text: 'Category'}
        ],
        baseUrl: 'api/products',
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

        destroy(url) {
            this.form.deleteWithConfirmation(url).then(response => {
                this.form.successModal('Product has been removed').then(() =>
                    window.location = '/products/'
                );
            });
        },
    }
});