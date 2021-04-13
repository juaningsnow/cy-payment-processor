<template>
	<div class="form-inline my-2 my-lg-0">
		<div class="form-check nav-link">
			<input
				:id="domId"
				type="checkbox"
				class="form-check-input"
				v-model="value"
				@change="setCondition"
			/>
			<label class="form-check-label" :for="domId">{{ label }}</label>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		label: String,
		field: String,
		clearit: Boolean,
		initVal: {
			type: Boolean,
			default: false
		}
	},

	data() {
		return {
			value: this.initVal
		};
	},

	computed: {
		domId() {
			return "chk_" + this.field;
		}
	},

	watch: {
		clearit(val) {
			if (val) {
				this.value = "";
				this.$emit("cleared");
			}
		}
	},

	methods: {
		setCondition() {
			this.$emit("set-condition", this.field, this.value);
		}
	}
};
</script>
