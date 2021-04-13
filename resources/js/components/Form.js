import { Errors } from './Error';
import swal from 'sweetalert2';
import axios from "axios";


export class Form {

    constructor(data) {
        this.uploadPercentage = 0;
        this.isInitializing = true; // Loading edit object and list options (customers, etc.)
        this.isSaving = false; // Saving to DB
        this.isDeleting = false; // Deleting from DB
        this.isLoading = false; // Loading table data
        this.isBusy = false; // Saving or Deleting or Loading
        this.originalData = {};
        for (let field in data) {
            this[field] = typeof data[field] === 'object' ? JSON.parse(JSON.stringify(data[field])) : data[field];
            this.originalData[field] = typeof data[field] === 'object' ? JSON.parse(JSON.stringify(data[field])) : data[field];
        }
        this.errors = new Errors();
    }

    data() {
        let data = {};
        for (let property in this.originalData) {
            // TODO check this?
            // data[property] = Array.isArray(this[property]) ? JSON.stringify(this[property]) : this[property];
            data[property] = this[property];
        }
        return data;
    }

    reset() {
        console.log('Resetting...');
        for (let field in this.originalData) {
            this[field] = typeof this.originalData[field] === 'object' ? JSON.parse(JSON.stringify(this.originalData[field])) : this.originalData[field];
        }
        this.errors.clear();
    }

    showAlert(text = '', title = 'Are you sure?', type = 'warning') {
        return swal({
            title: title,
            text: text,
            type: type,
            showCancelButton: true,
        });
    }

    check(url) {
        return new Promise((resolve, reject) => {
            axios.get(url)
                .then(response => {

                    // Process the check results here
                    let message = '';
                    let data = response.data;
                    //
                    //     if (Object.keys(data).length > 0) {
                    //         // Send confirmation
                    //         let message = '';
                    //         for (let field in data) {
                    //             message = field;
                    //         }
                    //     }

                    let result = true;

                    if (result) {
                        resolve(this.showAlert(message));
                    }
                    resolve(true);
                }).catch(error => {
                    reject(error);
                });
        });

    }

    get(url) {
        this.isLoading = true;
        return this.submit('get', url);
    }

    post(url) {
        this.isSaving = true;
        return this.submit('post', url);
    }

    postWithModal(url, redirect, text, title) {
        return this.post(url).then(response => {
            this.reset();
            this.successModal(text, title).then(() => {
                if (redirect) {
                    window.location = redirect;
                }
            });
        });
    }

    postImage(url, formData) {
        return this.submitImage(url, formData)
    }

    put(url) {
        this.isSaving = true;
        return this.submit('put', url);
    }

    patch(url) {
        this.isSaving = true;
        return this.submit('patch', url);
    }

    delete(url) {
        this.isDeleting = true;
        return this.submit('delete', url);
    }

    deleteWithConfirmation(uri) {
        return new Promise((resolve, reject) => {
            this.confirm().then(result => {
                if (result.value) {
                    resolve(this.delete(uri));
                }
            });
        });
    }

    confirm(text = "Are you sure?") {
        return swal({
            title: text,
            text: "You will not be able to revert this.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: 'Yes, delete this.',
            confirmButtonColor: '#f86c6b',
        });
    }

    successModal(text = 'Action Completed', title = 'Success!') {
        return swal({
            title: title,
            text: text,
            type: 'success',
            confirmButtonText: 'OK',
        });
    }

    errorModal(error) {
        return swal({
            title: error.getTitle() || error.getStatusCode(),
            text: error.getMessage(),
            type: 'error'
        });
    }

    errorGenericModal(title, text)
    {
        return swal({
            title: title,
            text: text,
            type: 'error'
        });
    }

    affirm(text = "Are you sure?") {
        return swal({
            title: text,
            text: "You will not be able to undo this.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: 'OK',
            confirmButtonColor: '#f86c6b',
        });
    }

    submit(requestType, url) {
        this.isBusy = true;
        return new Promise((resolve, reject) => {
            axios[requestType](url, this.data())
                .then(response => {
                    this.onSuccess(response.data);
                    resolve(response.data);
                }).catch(error => {
                    this.onFail(error.response.data);
                    reject(error.response.data);
                });
        });
    }

    submitImage(url, formData) {
        return new Promise((resolve, reject) => {
            axios["post"](url, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                    onUploadProgress: function(progressEvent) {
                        this.uploadPercentage = parseInt(
                            Math.round(
                                (progressEvent.loaded * 100) / progressEvent.total
                            )
                        );
                    }.bind(this)
                })
                .then(response => {
                    this.onSuccess(response.data);
                    resolve(response.data);
                })
                .catch(error => {
                    this.onFail(error.response.data);
                    reject(error.response.data);
                });
        });
    }

    onSuccess(data) {
        this.isBusy = false;
        this.isDeleting = false;
        this.isSaving = false;
        this.isLoading = false;
    }

    onFail(errors) {
        this.errors.record(errors);
        if (this.errors.isModal() || this.errors.isPageError()) {
            swal({
                title: this.errors.getTitle() || this.errors.getStatusCode(),
                text: this.errors.getMessage(),
                type: 'error'
            }).then(
                () => this.errors.clearAll(),
                (dismiss) => this.errors.clearAll());
        }
        this.isBusy = false;
        this.isDeleting = false;
        this.isSaving = false;
        this.isLoading = false;
        this.isInitializing = false;
    }
}