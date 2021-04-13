import Vue from 'vue';
import {Form} from "./components/Form";
import SaveButton from "./components/SaveButton";
import DeleteButton from "./components/DeleteButton";
import Datepicker from 'vuejs-datepicker';
import moment from 'moment';
import MultipleFileInput from "./components/MultipleFileInput.vue";
import { VueEditor } from "vue2-editor";
import { FormWizard, TabContent } from 'vue-form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import { Datetime } from 'vue-datetime'
import VueSweetalert2 from "vue-sweetalert2";
Vue.use(VueSweetalert2);

Vue.config.devtools = true;

new Vue({
    el: "#product",

    components: {
        SaveButton,
        DeleteButton,
        Datepicker,
        VueEditor,
        FormWizard,
        TabContent,
        MultipleFileInput,
        Datetime,
    },

    data: {
        form: new Form({
            id: null,
            name: "",
            date_and_time: moment().format('YYYY-MM-DD'),
            category: "",
            description: "",
            files: [],
        }),
        dataInitialized: true,
        categories: [
            'Category A',
            'Category B',
            'Category C'
        ]
    },

    watch: {
        initializationComplete(val) {
            this.form.isInitializing = !val;
        },
    },

    computed: {

        initializationComplete() {
            return this.dataInitialized;
        },
    },

    methods: { 
        validateIfImages(){
            this.form.errors.errors = [];
            let invalidFiles = [];
            if(this.form.files.length == 0){
                return true;
            }
            for( var i = 0; i < this.form.files.length; i++ ){
                let file = this.form.files[i]; 
                if(file.type.slice(0,5) != "image"){
                    invalidFiles.push(file);
                    this.form.errors.errors.push(["Invalid File: " + file.name])
                }
            }
            if(invalidFiles.length > 1){
                return false
            }else{
                return true;
            }
        },
        emptyFiles(){
            return new Promise((resolve, reject) => {
                this.form.files = [];
                resolve();
            });
        },
        handleFileInputs(files){
            this.emptyFiles().then(() => {
                this.form.files = files;
            });
        },
        validateIfNotNull(){
            this.form.errors.errors = {};
            if(this.form.name == "" || this.form.category == "" || this.form.description == ""){
                this.setError();
            }else{
                return true;
            }
        },
        setError() {
            this.form.errors.errors = { selected: ['Please Complete the form!'] };
            return false;
        },
        store() {
            let formData = new FormData();
            for( var i = 0; i < this.form.files.length; i++ ){
                let file = this.form.files[i]; 
                formData.append('files[' + i + ']', file);
            }
            formData.append('name', this.form.name);
            formData.append('date_and_time', this.form.date_and_time);
            formData.append('category', this.form.category);
            formData.append('description', this.form.description);
            this.form.postImage("/api/products", formData).then(response => {
                this.$swal({
                    title: "Product created!",
                    text: "Product was saved.",
                    type: "success"
                }).then(() => window.location = "/products");
            }).catch(error => {
                console.log(error);
            });
        },

        update() {
            this.form.patch("/api/products/" + this.form.id).then(response => {
                this.$swal({
                    title: "Product updated!",
                    text: "Changes saved to sale database.",
                    type: "success"
                }).then(() => (window.location = "/products/" + id));
            });
        },
        loadData(data) {
            this.form = new Form(data);
        }
    },

    created() {

        if (id != null) {
            this.dataInitialized = false;
            this.isEdit = true;
            this.form
                .get(
                    "/api/products/" + id
                ).then(response => {
                    this.loadData(response.data);
                    this.dataInitialized = true;
                });
        }
    },
});