<style scoped>
    .action-link {
        cursor: pointer;
    }

    .m-b-none {
        margin-bottom: 0;
    }
</style>

<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>
                                My Buses
                            </span>

                            <a class="action-link" @click="getBuses">
                                Refresh
                            </a>
                        </div>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item" v-for="(bus, index) in buses">
                            <p>Name: {{ bus.name }}</p>
                            <p>Bus No.: {{ bus.service_no }}</p>
                            <p>Bus Stop Code: {{ bus.bus_stop_code }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <a class="action-link" @click="showEditBus(index)">
                                    Edit
                                </a>
                                <a class="action-link" @click="destroy(bus)">
                                    Delete
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- Edit bus service modal -->
                <div class="modal fade" id='modal-edit-service' tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button " class="close" aria-hidden="true" @click="closeEditBus()">&times;</button>
                            <h4 class="modal-title">Edit bus</h4>
                        </div>
                        <div class="modal-body">
                            <!-- Form Errors -->
                            <div class="alert alert-danger" v-if="editForm.errors.length > 0">
                                <p><strong>Whoops!</strong> Something went wrong!</p>
                                <br>
                                <ul>
                                    <li v-for="error in editForm.errors">
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>

                            <form class="form-horizontal" role="form" @submit.prevent>
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">Name</label>
                                    <div class="col-sm-8">
                                    <input type="text" class="form-control" id="name" v-model="editForm.name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-8">
                                    <button type="button" class="btn btn-primary" :disabled="editForm.busy"  @click="updateBusService"><i class="fa fa-btn fa-spinner fa-spin" aria-hidden="true" v-if="editForm.busy"></i>Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeEditBus()">Close</button>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                latitude: 0.00,
                longitude: 0.00,
                buses: [],
                editForm: {
                    bus: [],
                    errors: [],
                    name: '',
                    busy: false
                },
            };
        },

        mounted() {
            console.log('Component mounted.')
            this.getBuses();
        },

        methods: {
            /**
             * Get all the bus stops in SG.
             */
            getBuses() {
                axios.get('/api/buses')
                    .then(response => {
                        this.buses = response.data;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },

            /**
             * Show the current bus stop bus services.
             */
            showEditBus(index) {
                this.selectedBus = this.buses[index];
                this.editForm.name = this.selectedBus.name;
                $('#modal-edit-service').modal('show');
            },

            /**
             * Hide the current bus stop bus services.
             */
            closeEditBus() {
                $('#modal-edit-service').modal('hide');
                this.editForm.name = '';
                this.editForm.busy = false;
                this.editForm.errors = [];
                this.editForm.bus = [];
            },

            /**
             * Show the form to register bus service.
             */
            updateBusService() {
                this.editForm.bus = this.selectedBus;
                this.editForm.errors = [];
                this.editForm.busy = true;
                axios['post']('/api/buses/' + this.selectedBus.id, this.editForm)
                    .then(response => {
                        this.editForm.name = '';
                        this.editForm.busy = false;
                        this.editForm.errors = [];
                        this.editForm.bus = [];
                        this.getBuses();
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            this.editForm.errors = _.flatten(_.toArray(error.response.data));
                        } else {
                            this.editForm.errors = ['Something went wrong. Please try again.'];
                        }
                        this.editForm.busy = false;
                    });
            },

            /**
             * Destroy the given client.
             */
            destroy(bus) {
                axios.delete('/api/buses/' + bus.id)
                        .then(response => {
                            this.getBuses();
                        });
            }
        }
    }
</script>
