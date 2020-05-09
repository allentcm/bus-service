<style scoped>
    .action-link {
        cursor: pointer;
    }

    .m-b-none {
        margin-bottom: 0;
    }
</style>

<template>
    <li class="list-group-item">
        <p class="text-center">{{ bus.bus_stop.road_name }}, {{ bus.bus_stop.description }}</p>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <dl class="dl-horizontal">
                <dt>Name:</dt>
                <dd>{{ bus.name }}</dd>
                <dt>Bus StopCode:</dt>
                <dd>{{ bus.bus_stop_code }}</dd>
                <dt>Service No:</dt>
                <dd>{{ bus.service_no }}</dd>
            </dl>
            <p class="text-center">
                Arrival Time:<br/>
                <a class="action-link" @click="getArrivalTime()">
                    {{ arrival === '' ? 'Refresh' : arrival }}
                </a>
            </p>
            <dl class="dl-horizontal">
                <dt>Operator:</dt>
                <dd>{{ bus.operator }}</dd>
                <dt>Origin Code:</dt>
                <dd>{{ bus.origin_code }}</dd>
                <dt>Destination Code:</dt>
                <dd>{{ bus.destination_code }}</dd>
            </dl>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a class="action-link" @click="showEditBus(index)">
                Edit
            </a>
            <a class="action-link" @click="showDeleteBus()">
                Delete
            </a>
        </div>

        <!-- Edit bus service modal -->
        <div class="modal fade" :id="'modal-edit-' + bus.id" tabindex="-1" role="dialog">
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

        <!-- Warning delete bus modal -->
        <div class="modal fade" :id="'modal-delete-' + bus.id" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" aria-hidden="true" @click="closeDeleteBus()">&times;</button>
                        <h4 class="modal-title">Delete {{ bus.name }}</h4>
                    </div>
                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="deleteForm.errors.length > 0">
                            <p><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in deleteForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>
                        <div class="alert alert-danger" role="alert">Are you sure you want to {{ bus.name }}?</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeDeleteBus">Cancel</button>
                        <button type="button" class="btn btn-primary" :disabled="deleteForm.busy" @click="destroy"><i class="fa fa-btn fa-spinner fa-spin" aria-hidden="true" v-if="deleteForm.busy"></i>Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
    export default {
        props: [
            'bus',
        ],

        data() {
            return {
                editForm: {
                    name: '',
                    busy: false,
                    errors: []
                },
                deleteForm: {
                    name: '',
                    busy: false,
                    errors: []
                },
                arrival: ''
            };
        },

        mounted() {
            console.log('Component mounted.');

            this.getArrivalTime();
            this.autoRefresh();
        },

        methods: {

            /**
             * Auto refresh arrival time every minute
             */
            autoRefresh() {
                setInterval(() => {this.getArrivalTime()}, 60000);
            },

            /**
             * Show edit form
             */
            showEditBus() {
                this.editForm.name = this.bus.name;
                $('#modal-edit-' + this.bus.id).modal('show');
            },

            /**
             * Hide edit form
             */
            closeEditBus() {
                $('#modal-edit-' + this.bus.id).modal('hide');
                this.editForm.name = '';
                this.editForm.busy = false;
                this.editForm.errors = [];
            },

            /**
             * Show delete form
             */
            showDeleteBus() {
                this.editForm.name = this.bus.name;
                $('#modal-delete-' + this.bus.id).modal('show');
            },

            /**
             * Hide delete form
             */
            closeDeleteBus() {
                $('#modal-delete-' + this.bus.id).modal('hide');
                this.editForm.name = '';
                this.editForm.busy = false;
                this.editForm.errors = [];
            },

            /**
             * Update the bus service name
             */
            updateBusService() {
                // prepare data for storing
                let data = {};
                data.name = this.editForm.name;
                this.editForm.busy = true;
                axios['post']('/api/buses/' + this.bus.id, data)
                    .then(response => {
                        this.editForm.name = '';
                        this.editForm.busy = false;
                        this.editForm.errors = [];

                        this.closeEditBus();

                        this.$emit('updated');
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
             * Delete this bus
             */
            destroy() {
                this.deleteForm.busy = true;
                axios.delete('/api/buses/' + this.bus.id)
                    .then(response => {
                        this.closeDeleteBus();

                        this.$emit('updated');
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            this.deleteForm.errors = _.flatten(_.toArray(error.response.data));
                        } else {
                            this.deleteForm.errors = ['Something went wrong. Please try again.'];
                        }
                        this.deleteForm.busy = false;
                    });
            },

            /**
             * Get latest arrival time
             */
            getArrivalTime() {
                axios.get('/api/buses/' + this.bus.id + '/arrival')
                    .then(response => {
                        this.updateArrival(response.data);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },

            /**
             * Update arrival time
             */
            updateArrival(dateString) {
                let now = new Date();
                let arrival = new Date(dateString);
                if (arrival instanceof Date && !isNaN(arrival)) {
                    this.arrival = this.inMinutes(now, arrival);
                } else {
                    this.arrival = 'Unavailable';
                }
            },

            /**
             * Get human readable estimated time of arrival
             *
             * @param date1 Current date
             * @param date2 ETA date
             * @returns {string}
             */
            inMinutes(date1, date2) {
                let seconds =(date2.getTime() - date1.getTime()) / 1000;
                let minutes = seconds / 60;

                if (minutes > 60) {
                    let hours = minutes / 60;
                    return hours.toFixed(1) + ' hours';
                } else if (minutes < 1) {
                    return 'less than 1 minute';
                } else {
                    return minutes.toFixed(1) + ' minutes';
                }
            }
        }
    }
</script>
