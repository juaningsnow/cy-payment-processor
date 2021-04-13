<template>
  <div>
    <a
      class="nav-link dropdown-toggle"
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
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
      <input
        v-model="value"
        @blur="setCondition"
        @keydown.enter="clickLink"
        class="form-control mr-sm-2"
        type="search"
        placeholder="Search"
        aria-label="Search"
      />
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
      this.$emit("set-condition", this.field, this.value, "LIKE");
    },

    clickLink() {
      this.$refs.dropdownLink.click();
    }
  }
};
</script>
