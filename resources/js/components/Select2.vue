<template>
	<select data-width="100%">
		<slot></slot>
	</select>
</template>

<script>
$.fn.select2.defaults.set("theme", "bootstrap");
import select2 from "select2";
export default {
	props: {
		value: null,
		allowCustom: { type: Boolean, default: false },
		placeholder: { type: String, default: "" }
	},
	computed: {
		options() {
			let options = {
				placeholder: this.placeholder,

				// What is displayed in selection drop down
				templateResult: function(data) {
					let $result = $("<span></span>");
					$result.text(data.text);
					if (data.title) {
						let $description = $("<br><small class='text-muted'></small><br>");
						$description.text(data.title);
						$result.append($description);
					}
					return $result;
				}
			};

			if (this.allowCustom) {
				options.tags = true;
				options.createTag = function(params) {
					return {
						id: params.term,
						text: params.term,
						newOption: true
					};
				};
				options.templateResult = function(data) {
					let $result = $("<span></span>");
					$result.text(data.text);
					if (data.newOption) {
						$result.append("<em class='text-muted'>(new)</em> ");
					}
					return $result;
				};
			}

			return options;
		}
	},

	mounted() {
		console.log("Select2 Component mounted.");
		let vm = this;
		$(this.$el)
			.val(this.value)
			// init select2
			.select2(this.options)
			// emit event on change.
			.on("change", function(event, args) {
				console.log("JS Change event");
				if (!(args && "ignore" in args && args.ignore == true)) {
					console.log("JS Change event emitting...");
					//                        vm.$emit('input', this.value);
					//                        vm.$emit('change', this.value);
					vm.$emit("input", $(vm.$el).val());
					vm.$emit("change", $(vm.$el).val());
				}
			});
	},

	watch: {
		value: function(value) {
			let newValue = Array.isArray(value) ? JSON.stringify(value) : value;
			let oldValue = Array.isArray($(this.$el).val())
				? JSON.stringify($(this.$el).val())
				: $(this.$el).val();

			// update value and notify any JS components that the value changed
			let same = newValue == oldValue;

			$(this.$el)
				.val(value)
				.trigger("change", { ignore: same });
		},
		options: function(options) {
			// update options
			$(this.$el).select2({ data: options });
		}
	},
	destroyed: function() {
		$(this.$el)
			.off()
			.select2("destroy");
	}
};
</script>