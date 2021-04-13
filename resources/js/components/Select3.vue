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
		selected: {
			type: Object,
			default: function() {
				return {};
			}
		},
		allowCustom: { type: Boolean, default: false },
		allowClear: { type: Boolean, default: true },
		placeholder: { type: String, default: "" },
		url: { type: String, default: "" },
		search: { type: String, default: "search" },
		minimumInputLength: { type: Number, default: 0 }
	},

	computed: {
		options() {
			let options = {
				placeholder: this.placeholder,
				minimumInputLength: this.minimumInputLength,
				allowClear: this.allowClear,
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

			if (this.url) {
				options.ajax = {
					delay: 350,
					url: this.url,
					dataType: "json",
					data: function(params) {
						var query = {
							[this.search]: params.term ? "*" + params.term + "*" : "",
							page: params.page || 1
						};
						// Query parameters will be ?[search]=*[term]*&page=[page]
						return query;
					}.bind(this),
					transport: function(params, success, failure) {
						// console.log("Params in transport", params);
						axios
							.get(params.url, {
								params: params.data
							})
							.then(response => {
								// console.log(response.data);
								success(response.data);
							})
							.catch(error => {
								// console.log(error.response);
								failure(error.data);
							});
					},

					processResults: function(response, params) {
						let pagination = response.meta.pagination;
						let more = pagination.current_page < pagination.total_pages;
						response.meta.pagination.more = more;

						// Add slot options
						// if (pagination.current_page == 1) {
						// 	this.$slots.default.forEach((slotOption, index) => {
						// 		response.data.splice(index, 0, {
						// 			text: slotOption.elm.text,
						// 			id: slotOption.elm.value
						// 		});
						// 	});
						// }

						return {
							results: response.data,
							pagination: {
								more: more
							}
						};
					}.bind(this)
				};
			}

			return options;
		}
	},

	mounted() {
		// console.log("Select3 Component mounted.");
		let self = this;
		let select2 = $(this.$el);
		select2
			.val(this.value)
			// init select2
			.select2(this.options)
			// emit event on change.
			.on("change", function(event, args) {
				if (!(args && "ignore" in args && args.ignore == true)) {
					self.$emit("input", $(self.$el).val(), $(self.$el).select2("data"));
					self.$emit("change", $(self.$el).val(), $(self.$el).select2("data"));
				}
			});

		if (!_.isEmpty(this.selected)) {
			console.log("Loading pre-selected value...");
			var option = new Option(this.selected.text, this.selected.id, true, true);
			select2.append(option).trigger("change");
			select2.trigger({
				type: "select2:select",
				params: {
					data: this.selected
				}
			});
		}
	},

	watch: {
		url(value) {
			console.log("XXX", value);
			console.log(this.options.ajax);
			$(this.$el)
				.select2({ data: this.options })
				.trigger("change");
		},

		value(value) {
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

		options(options) {
			// update options
			$(this.$el).select2({ data: options });
		}
	},

	destroyed() {
		$(this.$el)
			.off()
			.select2("destroy");
	}
};
</script>