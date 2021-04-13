<template>
  <button type="button" class="btn btn-danger btn-sm" @click="destroy" :disabled="isBusy">
    <div v-if="isDeleting">
      <i class="fas fa-circle-notch fa-spin"></i> Deleting...
    </div>
    <div v-if="!isDeleting">
      <i class="fa fa-trash"></i>
    </div>
  </button>
</template>

<script>
export default {
  props: ["is-busy", "is-deleting"],

  mounted() {
    console.log("Delete button mounted.");
  },

  methods: {
    destroy() {
      this.$emit("destroy");
    }
  }
};
</script>
