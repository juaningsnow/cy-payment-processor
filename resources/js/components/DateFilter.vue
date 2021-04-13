<template>
	<div class="collapse navbar-collapse">
		<a
			class="btn btn btn-outline-secondary dropdown-toggle"
			href="#"
			id="navbarDropdown"
			role="button"
			data-toggle="dropdown"
			aria-haspopup="true"
			aria-expanded="false"
			ref="dropdownLink"
		>
			{{ label }}
			<small>{{ value }}</small>
		</a>
		&nbsp;
		<form class="dropdown-menu" aria-labelledby="navbarDropdown">
			<datepicker v-model="dateValue" :inline="true" @selected="clickLink" :clear-button="true" :clear-button-icon="'far fa-times-circle'" />
		</form>
	</div>
</template>

<script>
import Datepicker from "vuejs-datepicker";

export default {
	components: { Datepicker },
	props: {
		label: String,
		field: String,
		clearit: Boolean,
		initVal: {
			type: String,
			default: ""
		}
	},

	data() {
		return {
			value: this.initVal,
			dateValue: this.initVal ? new Date(this.initVal) : null
		};
	},

	watch: {
		dateValue(val) {
			this.value = val ? this.dateToString(val) : "";
			this.setCondition();
		},

		clearit(val) {
			if (val) {
				this.value = "";
				this.$emit("cleared");
			}
		}
	},

	methods: {
		dateToString(val) {
			let monthString = (val.getMonth() + 1).toString().padStart(2, 0);
			let dayString = val
				.getDate()
				.toString()
				.padStart(2, 0);
			return `${val.getFullYear()}-${monthString}-${dayString}`;
		},

		setCondition() {
			console.log("Setting date condition", this.field, this.value);
			this.$emit("set-condition", this.field, this.value);
		},

		clickLink() {
			this.$refs.dropdownLink.click();
		}
	}
};
</script>
