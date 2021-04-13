<template>
	<div class="row">
		<div class="col-8 text-muted">{{ status }}</div>

		<div class="col-4 text-right">
			<div class="input-group input-group-sm">
				<div class="input-group-prepend">
					<button
						type="button"
						class="btn btn-outline-primary btn-sm"
						@click="firstPage"
						:disabled="prevDisabled"
					>
						<i class="fas fa-angle-double-left"></i> First
					</button>
					<button
						type="button"
						class="btn btn-outline-primary"
						@click="prevPage"
						:disabled="prevDisabled"
					>
						<i class="fas fa-angle-left"></i> Prev
					</button>
				</div>
				<input
					type="text"
					class="form-control form-control-sm text-center"
					v-model="paginationPage"
					@keydown.enter="toPage(paginationPage)"
				/>
				<div class="input-group-append">
					<button
						type="button"
						class="btn btn-outline-primary"
						@click="nextPage"
						:disabled="nextDisabled"
					>
						Next
						<i class="fas fa-angle-right"></i>
					</button>

					<button
						type="button"
						class="btn btn-outline-primary btn-sm"
						@click="lastPage"
						:disabled="nextDisabled"
					>
						Last
						<i class="fas fa-angle-double-right"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: ["status", "prev-disabled", "next-disabled", "page"],

	props: {
		status: "",
		prevDisabled: { type: Boolean, default: true },
		nextDisabled: { type: Boolean, default: true },
		page: { type: Number, default: 0 }
	},

	mounted() {
		console.log("Pagination Links components mounted.");
	},

	data() {
		return {
			paginationPage: this.page
		};
	},

	watch: {
		page(val) {
			this.paginationPage = val;
		}
	},

	methods: {
		firstPage() {
			this.$emit("first-page");
		},

		lastPage() {
			this.$emit("last-page");
		},

		toPage(page) {
			this.$emit("to-page", page);
		},

		prevPage() {
			this.$emit("prev-page");
		},

		nextPage() {
			this.$emit("next-page");
		}
	}
};
</script>
