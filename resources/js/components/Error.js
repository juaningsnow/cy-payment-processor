export class Errors {
    constructor() {
        this.modal = false;
        this.title = '';
        this.errors = {};
        this.code = '';
        this.message = '';
        this.status_code = '';
    }

    isPageError() {
        return !this.any() && this.status_code != '';
    }

    isModal() {
        return this.modal;
    }

    getTitle() {
        return this.title;
    }

    getMessage() {
        return this.message;
    }

    getStatusCode()
    {
        return this.status_code;
    }

    has(field) {
        return this.errors.hasOwnProperty(field);
    }

    any() {
        return Object.keys(this.errors).length > 0;
    }

    get(field) {
        if (this.errors[field]) {
            // TODO what if this is not an array
            return this.errors[field][0];
        }
    }

    list() {
        return this.errors;
    }

    record(apiError) {
        console.log('Recording error...');
        console.log(apiError.message);
        this.modal = apiError.hasOwnProperty('modal') ? apiError.modal : false;
        this.code = apiError.hasOwnProperty('code') ? apiError.code : '';
        this.errors = apiError.hasOwnProperty('errors') ? apiError.errors : '';
        this.message = apiError.hasOwnProperty('message') ? apiError.message : '';
        this.title = apiError.hasOwnProperty('title') ? apiError.title : '';
        this.status_code = apiError.hasOwnProperty('status_code') ? apiError.status_code : '';
    }

    clearAll() {
        this.modal = false;
        this.title = '';
        this.errors = {};
        this.code = '';
        this.message = '';
        this.status_code = '';
    }

    clear(field) {
        if (field) {
            delete this.errors[field];
            return;
        }
        this.errors = {};
    }
}