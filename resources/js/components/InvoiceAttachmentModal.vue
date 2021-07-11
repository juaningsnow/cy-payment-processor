<template>
    <modal-window title="Upload Artwork" @close="close" @save="save">
        <div class="form-row">
            <div class="form-group col-12">
                <label for="attendanceId">
                    File
                    <span class="text-danger">*</span>
                </label>
                <input
                    type="file"
                    accept="text/*,application/pdf,application/doc,application/docx,application/xlsx,application/xls,application/ppt,application/pptx"
                    ref="file"
                    @change="handleFileChange"
                />
            </div>
        </div>
        <br />
        <div class="progress" v-show="form.uploadPercentage">
            <div
                class="progress-bar"
                role="progressbar"
                :style="progressBarStyle"
                :aria-valuenow="form.uploadPercentage"
                aria-valuemin="0"
                aria-valuemax="100"
            >
                {{ form.uploadPercentage }}%
            </div>
        </div>

        <template slot="footer">
            <button
                type="button"
                class="btn btn-primary"
                @click="save"
                :disabled="form.uploadPercentage > 0"
            >
                <i class="fa fa-upload"></i> Upload
            </button>
            <button
                type="button"
                class="btn btn-secondary"
                @click="close"
                :disabled="form.uploadPercentage > 0"
            >
                <i class="fa fa-times"></i> Close
            </button>
        </template>
    </modal-window>
</template>

<script>
import ModalWindow from "./ModalWindow";
import { Form } from "./Form";

export default {
    components: {
        ModalWindow,
    },

    props: ["invoice-id"],

    data() {
        return {
            form: new Form({
                file: null,
            }),
        };
    },

    computed: {
        progressBarStyle() {
            return `width: ${this.form.uploadPercentage}%`;
        },
    },

    mounted() {
        console.log("Invoice Attachment Modal Mounted...");
    },

    created() {},

    methods: {
        handleFileChange(e) {
            this.form.file = this.$refs.file.files[0];
        },

        close() {
            this.$emit("close");
            this.form.reset();
        },

        save() {
            this.store();
        },

        store() {
            let formData = new FormData();
            formData.append("file", this.form.file);
            this.form
                .postImage(
                    "/api/invoices/" + this.invoiceId + "/upload",
                    formData
                )
                .then((response) => {
                    this.form
                        .successModal("File Uploaded")
                        .then(
                            () =>
                                (window.location =
                                    "/invoices/" + this.invoiceId)
                        );
                    this.form.file = null;
                    this.$emit("close");
                })
                .catch((error) => {});
        },
    },
};
</script>