<template>
    <button class="btn btn-success" type="submit" :disabled="isBusy">
        <div v-if="isSaving">
            <i class="fas fa-circle-notch fa-spin"></i> Saving...
        </div>
        <div v-if="!isSaving">
            <i class="fa fa-save"></i>
        </div>
    </button>
</template>

<script>
export default {
    props: ["is-busy", "is-saving"],

    mounted() {
        console.log("Save button mounted.");
    },
};
</script>
