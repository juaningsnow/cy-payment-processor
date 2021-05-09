<template>
    <div>
        <filters2
            :init-filters="filters"
            :filterable="filterable"
            @set-condition="setCondition"
            @clear="clear"
        ></filters2>

        <!-- <filters
			:conditions="conditions"
			:filterable="filterable"
			v-on:add="addCondition"
			v-on:filter="filter"
			v-on:clear="clear"
		></filters>-->

        <br />

        <h3>
            <slot name="header"></slot>
        </h3>
        <slot name="create_button"></slot>
        <br />

        <slot></slot>

        <p class="text-center" v-if="isLoading">
            <i class="fas fa-circle-notch fa-spin"></i> Loading...
        </p>

        <p class="text-center" v-if="!isLoading && noItems">No items.</p>
        <br />
        <hr />
        <pagination-links
            :status="paginationStatus"
            :prev-disabled="isFirstPage || isLoading"
            :next-disabled="isLastPage || isLoading"
            :page="page"
            @to-page="toPage"
            @next-page="nextPage"
            @prev-page="prevPage"
            @first-page="firstPage"
            @last-page="lastPage"
        ></pagination-links>

        <!--<pagination-links-->
        <!--:total-pages="pagination.total_pages"-->
        <!--:current-page="pagination.current_page"-->
        <!--:status="paginationStatus"-->
        <!--:prev-disabled="isFirstPage || isLoading"-->
        <!--:next-disabled="isLastPage || isLoading"-->
        <!--v-on:next-page="nextPage"-->
        <!--v-on:prev-page="prevPage"-->
        <!--v-on:to-page="toPage">-->
        <!--</pagination-links>-->
    </div>
</template>

<script>
// import Filters from "./Filters";
import Filters2 from "./Filters2";
import axios from "axios";
//    import PaginationLinks from './NumberedPaginationLinks'
import PaginationLinks from "./PaginationLinks";

export default {
    components: {
        Filters2,
        PaginationLinks,
    },

    // Can not use percentage (%) in api uri because certain combinations create a special character in URI
    //

    props: {
        filters: {
            type: Array,
            default: () => [],
        },
        filterable: Array,
        baseUrl: String,
        sorter: String,
        sortAscending: Boolean,
        toLastPage: {
            type: Boolean,
            default: false,
        },
        exportBaseUrl: {
            type: String,
            default: "",
        },
    },

    data() {
        return {
            isLoading: false,
            conditions: JSON.parse(JSON.stringify(this.filters)),
            items: [],
            totals: {},
            pagination: {},
            page: 1,
            loadedLastPage: false,
            meta: null,
        };
    },

    watch: {
        isLoading(val) {
            console.log("Emitting is-loading...", val);
            this.$emit("update-loading", val);
        },

        items(val) {
            console.log("Updating items...");
            this.$emit("update-items", val);
        },

        totals(val) {
            console.log("Updating totals...");
            this.$emit("update-totals", val);
        },

        sorter(val) {
            this.load(this.assembledUrl);
        },

        sortAscending(val) {
            this.load(this.assembledUrl);
        },
    },

    methods: {
        setCondition(field, value) {
            console.log("Receiving condition: ", field, value);
            let condition = this.conditions.find(
                (condition) => condition.field == field
            );
            if (condition) {
                condition.value = value;
            } else {
                this.conditions.push({ field: field, value: value });
            }
            this.removeEmptyConditions();
            this.filter();
        },

        removeEmptyConditions() {
            this.conditions = this.conditions.filter(
                (condition) => condition.value != ""
            );
        },

        addCondition() {
            this.conditions.push({
                field: "",
                value: "",
            });
        },

        filter() {
            this.page = 1;
            this.load(this.assembledUrl);
        },

        clear() {
            this.conditions = [];
            this.page = 1;
            this.load(this.assembledUrl);
        },

        load(url) {
            this.isLoading = true;
            axios
                .get(url)
                .then((response) => {
                    this.onLoadSuccess(response);
                })
                .catch(this.onLoadError);
        },

        onLoadSuccess(response) {
            this.meta = response.data.meta;
            this.items = response.data.data;
            this.totals = response.data.data.meta.hasOwnProperty("totals")
                ? response.data.meta.totals
                : {};
            this.pagination = response.data.meta;
            if (this.toLastPage && !this.loadedLastPage) {
                this.loadedLastPage = true;
                this.page = this.pagination.total_pages;
                this.load(this.assembledUrl);
            }
            if (this.noItems) this.page = 1;
            this.isLoading = false;
        },

        onLoadError(error) {
            console.log(error);
            this.isLoading = false;
        },

        prevPage() {
            if (this.isFirstPage || this.isLoading) return;
            this.page--;
            this.load(this.assembledUrl);
        },

        nextPage() {
            if (this.isLastPage || this.isLoading) return;
            this.page++;
            this.load(this.assembledUrl);
        },

        firstPage() {
            if (this.isFirstPage || this.isLoading) return;
            this.page = 1;
            this.load(this.assembledUrl);
        },

        lastPage() {
            if (this.isLastPage || this.isLoading) return;
            this.page = this.pagination.total_pages;
            this.load(this.assembledUrl);
        },

        toPage(page) {
            page = Math.max(1, page);
            page = Math.min(page, this.pagination.total_pages);
            console.log("Loading page", page);
            this.page = page;
            this.load(this.assembledUrl);
        },
    },

    mounted() {
        console.log("Index component mounted.");
        this.load(this.assembledUrl);
    },

    computed: {
        conditionString() {
            return this.conditions.reduce((prev, curr) => {
                return prev.concat("&" + curr.field + "=" + curr.value);
            }, "");
        },

        sorterString() {
            return "&sort=" + (this.sortAscending ? "" : "-") + this.sorter;
        },

        assembledUrl() {
            let leader = "?";
            if (this.baseUrl.indexOf("?") > -1) leader = "&";
            return (
                this.baseUrl +
                leader +
                "page=" +
                this.page +
                this.conditionString +
                this.sorterString
            );
        },

        noItems() {
            return this.items.length == 0;
        },

        isLastPage() {
            if (this.noItems) return true;
            return this.page == this.pagination.total_pages;
        },

        isFirstPage() {
            return this.page == 1;
        },

        paginationFrom() {
            if (this.noItems) return 0;
            return (
                (this.pagination.current_page - 1) * this.pagination.per_page +
                1
            );
        },

        paginationTo() {
            if (this.noItems) return 0;
            return Math.min(
                this.pagination.current_page * this.pagination.per_page,
                this.pagination.total
            );
        },

        paginationStatus() {
            if (this.isLoading) {
                return "";
            } else if (!this.meta.from) {
                return "No Records Yet";
            } else {
                return (
                    "Displaying items " +
                    this.meta.from +
                    " to " +
                    this.meta.to +
                    " of " +
                    this.meta.total
                );
            }
        },
    },
};
</script>
