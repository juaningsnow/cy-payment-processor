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
		<form class="dropdown-menu" aria-labelledby="navbarDropdown">
			<select v-model="value" class="custom-select" @change="setCondition(); clickLink()">
				<option value selected>--</option>
				<option v-for="option in options" :key="option">{{ option }}</option>
			</select>
		</form>
	</div>
</template>

<script>
export default {
	props: {
		options: {
			type: Array,
			default: () => []
		},
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
			value: this.initVal
		};
	},

	watch: {
		clearit(val) {
			if (val) {
				this.value = "";
				this.$emit("cleared");
			}
		}
	},

	created() {
		this.setCondition();
	},

	methods: {
		setCondition() {
			this.$emit("set-condition", this.field, this.value);
		},

		clickLink() {
			this.$refs.dropdownLink.click();
		}
	}
};
</script>
