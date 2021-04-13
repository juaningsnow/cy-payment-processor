

<form-wizard color="#20a8d8" title="Routine" @on-complete="store" subtitle="">
    <div v-if="Object.keys(form.errors.errors).length" class="alert alert-danger" role="alert">
        <small class="form-text form-control-feedback" v-for="error in form.errors.errors">
            @{{ error[0] }}
        </small>
    </div>
    <tab-content title="Basic Details" icon="fas fa-user" :before-change="validateIfNotNull">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" placeholder="Name" v-model="form.name">
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
                <select v-model="form.category" class="form-control">
                  <option v-for="category in categories" :value="category">@{{category}}</option>
                </select>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <vue-editor v-model="form.description"></vue-editor>
        </div>
    </tab-content>
    <tab-content title="Photos" icon="fas fa-image" :before-change="validateIfImages">
        <multiple-file-input @handle-files="handleFileInputs" ></multiple-file-input>
        <ul>
            <li v-for="file in form.files">
                @{{file.name}}
                <img class="img-fluid pad" v-if="file.publicUrl" :src="file.publicUrl" alt="Photo">
            </li>
        </ul>
    </tab-content>
    <tab-content title="Date and Time" icon="fas fa-calendar-alt">
        <div class="form-group">
            <label for="name">Date and Time</label>
            <datetime v-model="form.date_and_time" type="datetime" input-class="form-control" :use12-hour="true"></datetime>
        </div>
    </tab-content>
</form-wizard>