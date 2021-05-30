<template>
    <transition name="modal">
        <div class="modal-mask">
            <div
                class="modal"
                style="display: block !important; overflow: auto"
                tabindex="-1"
                role="dialog"
            >
                <div class="modal-dialog" :class="modalSize" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" style="color: black">
                                {{ title }}
                            </h5>
                            <button
                                type="button"
                                class="close"
                                @click="$emit('close')"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <slot></slot>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    v-if="!noSave"
                                    @click="$emit('save')"
                                >
                                    <i class="fa fa-save"></i> Save
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    @click="$emit('close')"
                                >
                                    Close
                                </button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    props: {
        size: {
            type: String,
            default: "",
        },
        noSave: {
            type: Boolean,
            default: false,
        },
        title: String,
    },

    data() {
        return {};
    },

    computed: {
        modalSize() {
            return {
                "modal-lg": this.size == "lg",
                "modal-sm": this.size == "sm",
                "modal-xl": this.size == "xl",
            };
        },
    },
};
</script>