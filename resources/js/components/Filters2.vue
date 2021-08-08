<template>
    <form @submit.prevent="filter">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <span class="navbar-brand">
                <i class="fas fa-search fa-sm"></i>
            </span>
            <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li
                        class="nav-item dropdown"
                        v-for="filter in filterable"
                        :key="filter.id"
                    >
                        <select-filter
                            v-if="filter.type == 'Select'"
                            :options="filter.options"
                            :clearit="clearit"
                            :label="filter.text"
                            :field="filter.id"
                            :init-val="getFilterValue(filter.id)"
                            @set-condition="setCondition"
                            @cleared="cleared"
                        ></select-filter>
                        <date-filter
                            v-else-if="filter.type == 'Date'"
                            :clearit="clearit"
                            :label="filter.text"
                            :field="filter.id"
                            :init-val="getFilterValue(filter.id)"
                            @set-condition="setCondition"
                            @cleared="cleared"
                        ></date-filter>
                        <boolean-filter
                            v-else-if="filter.type == 'Boolean'"
                            :clearit="clearit"
                            :label="filter.text"
                            :field="filter.id"
                            :init-val="!!getFilterValue(filter.id)"
                            @set-condition="setCondition"
                            @cleared="cleared"
                        ></boolean-filter>
                        <text-filter
                            v-else
                            :clearit="clearit"
                            :label="filter.text"
                            :field="filter.id"
                            :init-val="getFilterValue(filter.id)"
                            @set-condition="setCondition"
                            @cleared="cleared"
                        ></text-filter>
                    </li>
                </ul>
                <!-- <form class="my-2 my-lg-0">
					<button class="btn btn-outline-info my-2 my-sm-0" type="button" @click="clear">
						<i class="fas fa-times"></i>
					</button>
				</form>-->
            </div>
        </nav>
    </form>
</template>

<script>
import TextFilter from "./TextFilter";
import BooleanFilter from "./BooleanFilter";
import SelectFilter from "./SelectFilter";
import DateFilter from "./DateFilter";

export default {
    components: { TextFilter, BooleanFilter, SelectFilter, DateFilter },

    props: {
        initFilters: {
            type: Array,
            default: () => [],
        },
        filterable: Array,
    },

    data() {
        return {
            filters: JSON.parse(JSON.stringify(this.initFilters)),
            clearit: false,
            clearedCounter: 0,
        };
    },

    methods: {
        getFilterValue(field) {
            let filter = this.filters.find((filter) => filter.field == field);
            if (filter) {
                return filter.value;
            }
            return "";
        },

        setCondition(field, value, operation = "=") {
            if (value && operation == "LIKE") {
                value = "*" + value + "*";
            }
            this.$emit("set-condition", field, value);
        },

        clear() {
            this.clearit = true;
            this.$emit("clear");
        },

        filter() {
            // TODO change in onprevent
        },

        cleared() {
            this.clearedCounter++;
            if (this.clearedCounter == this.filterable.length) {
                this.clearedCounter = 0;
                this.clearit = false;
            }
        },
    },
};
</script>
