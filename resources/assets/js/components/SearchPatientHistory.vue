<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-search" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Busqueda de servicios
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-12">
                                <a
                                        data-toggle="modal"
                                        data-target="#patientModal"
                                        @click="searchPatients()"
                                        >
                                    <i class="glyphicon glyphicon-search"></i>

                                    <span v-if="! patient">
                                        Seleccionar paciente
                                    </span>
                                    <span v-if="patient">
                                        Cambiar paciente
                                    </span>
                                </a>
                                <hr>
                            </div>
                        </div>

                        <section v-if="patient">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <p>
                                            {{ patient.name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Telefono</label>
                                        <p>
                                            {{ patient.phone }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <p>
                                            {{ patient.email }}
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Desde</label>
                                        <datepicker
                                                name = "start"
                                                id = "start"
                                                language="es"
                                                input-class = "form-control"
                                                format = "MM/dd/yyyy"
                                                @input="changeStart($event)"
                                                v-model="initStart"
                                                ></datepicker>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Hasta</label>
                                        <datepicker
                                                name = "end"
                                                id = "end"
                                                language="es"
                                                input-class = "form-control"
                                                format = "MM/dd/yyyy"
                                                @input="changeEnd($event)"
                                                v-model="initEnd"
                                                ></datepicker>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <button
                                                class="btn btn-primary"
                                                @click="searchPatientHistory()"
                                                v-if="!loading"
                                                >
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar
                                        </button>

                                        <button
                                                class="btn btn-success"
                                                data-toggle="modal"
                                                data-target="#registerServiceModal"
                                                >
                                            Registrar servicio
                                        </button>
                                        <register-service-modal
                                            modal-id = "registerServiceModal"
                                            :patient-id = "patient.id"
                                            @register-patient-history="searchPatientHistory()"
                                        ></register-service-modal>

                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="data.servicesAndNotes">
                                <div class="col-xs-12" v-for="dataPerDate in data.servicesAndNotes">

                                    <!-- Services -->
                                    <table class="table table-responsive" v-if="dataPerDate.services.length">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th v-if="authUser.level == 1">Código</th>
                                                <th>Servicio</th>
                                                <th>Diente</th>
                                                <th>Doctor</th>
                                                <th>Asistente</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="service in dataPerDate.services">
                                                <td>{{ dateFormat(service.created_at) }}</td>
                                                <td v-if="authUser.level == 1">{{ service.public_id }}</td>
                                                <td>{{ service.product.name }}</td>
                                                <td>{{ service.tooth }}</td>
                                                <td>{{ service.doctor.name }}</td>
                                                <td>{{ service.assistant.name }}</td>
                                                <td>
                                                    <button
                                                            type="button"
                                                            class="btn btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#deleteModal"
                                                            v-if="authUser.level === 1"
                                                            @click="deletePatientHistory = service.id"
                                                            >
                                                        <i class="glyphicon glyphicon-remove"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Notes -->
                                    <div class="row" v-if="dataPerDate.notes.length">
                                        <div class="col-xs-12">
                                            <hr>
                                            <div class="alert alert-info">
                                                <p>
                                                    <strong>
                                                        Notas {{ dateFormat(dataPerDate.date) }}
                                                    </strong>
                                                </p>

                                                <p v-for="(note,id) in dataPerDate.notes">
                                                    {{ (id + 1) + '. ' + note.user.username + ' - ' + note.content }}
                                                    <button
                                                            type="button"
                                                            class="btn btn-danger btn-xs"
                                                            data-toggle="modal"
                                                            data-target="#deleteNoteModal"
                                                            v-if="authUser.level === 1"
                                                            @click="deleteNote = note.id"
                                                            >
                                                        <i class="glyphicon glyphicon-remove"></i>
                                                    </button>
                                                </p>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="data.images.length">
                                <div class="col-xs-12">
                                    <h3>Imagenes y radiografias</h3>
                                </div>
                            </div>

                            <div class="row" v-if="! showImages && data.images.length">
                                <div class="col-xs-12">
                                    <button class="btn btn-success" @click="showImages = true">
                                        <i class="glyphicon glyphicon-picture"></i>
                                        Mostrar imagenes
                                    </button>
                                </div>
                            </div>

                            <div class="row" v-if="showImages">

                                <div class="col-sm-4 space-image" v-for="image in data.images">
                                    <a @click="fullScreenUrl = '/' + image.url; showFullScreen = true">
                                        <img :src="'/' + image.url" class="img-responsive images">
                                    </a>

                                    <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            data-toggle="modal"
                                            data-target="#deleteImageModal"
                                            v-if="authUser.level === 1"
                                            @click="deleteImage = image.id"
                                            >
                                        <i class="glyphicon glyphicon-remove"></i>
                                    </button>
                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>
                                    <strong>
                                        Selecciona al paciente
                                    </strong>
                                </h3>
                            </div>
                            <div class="col-sm-6">
                                <h3 class="text-right">
                                    <a href="/user/patient/create">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        Registrar paciente
                                    </a>
                                </h3>
                            </div>
                            <div class="col-xs-12">
                                <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Buscador"
                                        v-model="modal.search"
                                        @keyup="searchPatients()"
                                        >
                            </div>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-xs-12">

                                <table class="table table-responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th width="50%">Telefono</th>
                                        <th width="50%">Nombre</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="! modal.loading">
                                    <tr v-for="p in modal.data" v-if="!patient || patient.id !== p.id">
                                        <td>{{ p.phone }}</td>
                                        <td>{{ p.name }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectPatient(p)"
                                                    data-dismiss="modal"
                                                    >
                                                <i class="glyphicon glyphicon-ok"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>¿Esta seguro de eliminar este servicio?</h4>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                v-show="! loading"
                                @click="deletePatientHistory = null"
                                id="closeDeleteModal">
                            NO
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                @click="sendDelete()"
                                v-show="! loading">
                            SI
                        </button>

                        <img src="/img/loading.gif" v-if="loading">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteNoteModal" tabindex="-1" role="dialog" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>¿Esta seguro de eliminar esta nota?</h4>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                v-show="! loading"
                                @click="deleteNote = null"
                                id="closeDeleteNoteModal">
                            NO
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                @click="sendDeleteNote()"
                                v-show="! loading">
                            SI
                        </button>

                        <img src="/img/loading.gif" v-if="loading">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteImageModal" tabindex="-1" role="dialog" aria-labelledby="deleteImageModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>¿Esta seguro de eliminar esta imagen?</h4>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                v-show="! loading"
                                @click="deleteImage = null"
                                id="closeDeleteImageModal">
                            NO
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                @click="sendDeleteImage()"
                                v-show="! loading">
                            SI
                        </button>

                        <img src="/img/loading.gif" v-if="loading">
                    </div>
                </div>
            </div>
        </div>

        <!-- FullScreen -->
        <div class="image__full-screen" v-if="showFullScreen" @click="showFullScreen = false">
            <button class="btn btn-default" @click="showFullScreen = false">
                <i class="glyphicon glyphicon-remove"></i>
            </button>
            <img :src="fullScreenUrl">
        </div>
    </section>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import RegisterServiceModal from './RegisterPatientHistoryModal.vue';

    export default {
        components: {
            Datepicker,
            RegisterServiceModal
        },
        props: ['user'],
        data: function () {
          return {
              loading: false,
              patient: null,
              initStart: new Date(),
              initEnd: new Date(),
              showImages: false,
              data: {
                  start: '',
                  end: '',
                  servicesAndNotes: [],
                  images: []
              },
              modal: {
                  data: [],
                  loading: false,
                  search: ''
              },
              authUser: JSON.parse(this.user),
              deletePatientHistory: null,
              deleteNote: null,
              deleteImage: null,

              showFullScreen: false,
              fullScreenUrl: ''
          }
        },
        mounted: function () {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.data.start = year + '-' + month + '-' + day;
            this.data.end = year + '-' + month + '-' + day;
        },
        methods: {
            searchPatients: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/user/patient/budget/search?search=' + this.modal.search)
                    .then((res) => {
                        this.modal.loading = false;

                        this.modal.data = res.data.patients;
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.modal.loading = false;
                    })
                ;
            },

            selectPatient: function (patient) {
                this.patient = patient;
                this.data.servicesAndNotes = [];
                this.data.images = [];
            },

            changeStart: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.start = year + '-' + month + '-' + day;
            },

            changeEnd: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.end = year + '-' + month + '-' + day;
            },

            searchPatientHistory: function () {
                this.loading = true;

                axios.get('/user/service/' + this.patient.public_id + '/search?start=' + this.data.start + '&end=' + this.data.end)
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.servicesAndNotes = res.data.data;
                            this.data.images = res.data.images;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.servicesAndNotes = [];
                        this.data.images = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            sendDelete: function () {
                this.loading = true;

                axios.delete('/user/service/' + this.deletePatientHistory)
                    .then((res) => {

                        if (res.data.success) {
                            $('#closeDeleteModal').click();
                            this.searchPatientHistory();
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.loading = false;
                        console.log(err);
                    })
            },

            sendDeleteNote: function () {
                this.loading = true;

                axios.delete('/user/service/note/' + this.deleteNote)
                    .then((res) => {

                        if (res.data.success) {
                            $('#closeDeleteNoteModal').click();
                            this.searchPatientHistory();
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.loading = false;
                        console.log(err);
                    })
            },

            sendDeleteImage: function () {
                this.loading = true;

                axios.delete('/user/service/image/' + this.deleteImage)
                        .then((res) => {

                    if (res.data.success) {
                        $('#closeDeleteImageModal').click();
                        this.searchPatientHistory();
                    }
                })
                .catch((err) => {
                    if (err.response.status === 403 || err.response.status === 405) {
                        location.href = '/';
                    }
                    this.loading = false;
                    console.log(err);
                })
            }
        }
    }
</script>