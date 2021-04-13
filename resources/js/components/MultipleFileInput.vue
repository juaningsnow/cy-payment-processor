<template>
  <div class="container">
    <div class="large-12 medium-12 small-12 cell">
      <label>Files
        <input type="file" id="files" ref="files" multiple @change="fileChange"/>
      </label>
    </div>
  </div>
</template>
<script>

export default({
    data(){
        return {
            fileInputs: null,
        }  
    },
    methods:{
        fileChange(){
            this.fileInputs = this.$refs.files.files;
            console.log(this.fileInputs);
            this.$emit("handle-files", this.fileInputs);
        },
    },
})
</script>