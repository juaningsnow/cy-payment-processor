import axios from 'axios';
import Vue from 'vue';

Vue.config.devtools = true;

new Vue({
    el: "#nav",
    data: {
        invoiceCount: 0,
        creditNoteCount: 0,
        invoiceBatchCount: 0,
    },


    created() {
        axios.get(`/api/invoices?no_invoice_batch_detail_or_cancelled=1&paid=0`).then(response => {
            this.invoiceCount = response.data.meta.total;
        });
        axios.get(`/api/credit-notes?status='AUTHORISED'`).then(response => {
            this.creditNoteCount = response.data.meta.total;
        });
        axios.get(`/api/invoice-batches?not-yet-generated=1`).then(response => {
            this.invoiceBatchCount = response.data.meta.total;
        })
    },
});