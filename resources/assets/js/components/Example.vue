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
                                My Location
                            </span>

                            <a class="action-link" @click="getGeolocation">
                                Refresh
                            </a>
                        </div>
                    </div>

                    <div class="panel-body">
                        <p>latitude: {{ latitude }}</p>
                        <p>Longitude: {{ longitude }}</p>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>
                                Bus Stops
                            </span>
                            <a class="action-link" @click="populateBusStops">
                                Populate
                            </a>
                            <a class="action-link" @click="getBusStops">
                                Refresh
                            </a>
                        </div>
                    </div>

                    <ul class="list-group">
                        <li class="list-group-item" v-for="(busStop, index) in busStops">
                            <p>Code: {{ busStop.bus_stop_code }}</p>
                            <p>Location: {{ busStop.road_name }}, {{ busStop.description }}</p>
                            <p>Coordinate: {{ busStop.latitude }}, {{ busStop.longitude }}</p>
                            <a class="action-link" @click="showBusServices(index)">
                                Sevices
                            </a>
                        </li>
                    </ul>
                    <!-- Create bus service info modal -->
                    <div class="modal fade" id='modal-bus-services' tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <button type="button " class="close" aria-hidden="true" @click="closeBusServices()">&times;</button>
                                <h4 class="modal-title">Bus Services for {{ currentBusStopDesc }}</h4>
                            </div>
                            <ul class="list-group">
                                <li class="list-group-item" v-for="(service, index) in services">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span>
                                            Bus No: {{ service.service_no }}
                                        </span>

                                        <a class="action-link" @click="showRegisterService(index)">
                                            Register
                                        </a>
                                    </div>
                                    <p>Arrival Time: {{ service.next_bus.estimated_arrival }}</p>

                                </li>
                            </ul>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="closeBusServices()">Close</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <!-- Register bus service modal -->
                    <div class="modal fade" id='modal-register-service' tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <button type="button " class="close" aria-hidden="true" @click="closeRegisterService()">&times;</button>
                                <h4 class="modal-title">Register for Bus No {{ selectedService.service_no }}</h4>
                            </div>
                            <div class="modal-body">
                                <!-- Form Errors -->
                                <div class="alert alert-danger" v-if="form.errors.length > 0">
                                    <p><strong>Whoops!</strong> Something went wrong!</p>
                                    <br>
                                    <ul>
                                        <li v-for="error in form.errors">
                                            {{ error }}
                                        </li>
                                    </ul>
                                </div>

                                <form class="form-horizontal" role="form" @submit.prevent>
                                    <div class="form-group">
                                        <label for="name" class="col-sm-4 control-label">Name</label>
                                        <div class="col-sm-8">
                                        <input type="text" class="form-control" id="name" v-model="form.name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-4 col-sm-8">
                                        <button type="button" class="btn btn-primary" :disabled="form.busy"  @click="registerBusService"><i class="fa fa-btn fa-spinner fa-spin" aria-hidden="true" v-if="form.busy"></i>Register</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="closeRegisterService()">Close</button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <a class="action-link" @click="moreBusStops()">
                                More
                            </a>
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
                busStops: [],
                services: [],
                currentBusStop: [],
                currentBusStopDesc: '',
                form: {
                    name: '',
                    busy: false,
                    errors: []
                },
                registerOn: false,
                selectedService: [],
                busStopPagination: {}
            };
        },

        mounted() {
            console.log('Component mounted.')
            this.getGeolocation();
        },

        methods: {
            /**
             * Get user current location.
             */
            getGeolocation() {
                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => 
                    {
                        this.latitude = position.coords.latitude;
                        this.longitude = position.coords.longitude;
                    });
                }

                this.getBusStops();
            },

            /**
             * Get all the bus stops in SG.
             */
            populateBusStops() {
                axios.get('/api/bus-stops/refresh')
                    .then(response => {
                        this.busStops = response.data.entries;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },

            /**
             * Get pagination of the bus stops by proximity.
             */
            getBusStops() {
                axios.get('/api/bus-stops?latitude=' + this.latitude + '&longitude=' + this.longitude)
                    .then(response => {
                        this.busStops = response.data.entries;
                        // get next page link
                        this.busStopPagination.links = {next: response.data.meta.pagination.links.next};
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },

            /**
             * Get more bus stops by proximity.
             */
            moreBusStops() {
                axios.get(this.busStopPagination.links.next)
                    .then(response => {
                        if (this.busStops == null) {
                            this.busStops = [];
                            this.busStops = response.data.entries;
                        } else {
                            this.busStops = this.busStops.concat(response.data.entries);
                        }
                        // get next page link
                        this.busStopPagination.links.next = response.data.meta.pagination.links.next;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },

            /**
             * Get all the bus services for the target bus stop.
             */
            getBusServices(busStopCode) {
                axios.get('/api/services/' + busStopCode)
                    .then(response => {
                        this.services = response.data.entries;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },

            /**
             * Show the current bus stop bus services.
             */
            showBusServices(index) {
                $('#modal-bus-services').modal('show');
                this.currentBusStop = this.busStops[index];             
                this.currentBusStopDesc = this.currentBusStop.bus_stop_code + ' : '
                    + this.currentBusStop.road_name + ', '
                    + this.currentBusStop.description;
                this.getBusServices(this.currentBusStop.bus_stop_code);
            },

            /**
             * Hide the current bus stop bus services.
             */
            closeBusServices() {
                $('#modal-bus-services').modal('hide');
            },

            /**
             * Show the registration form for bus service.
             */
            showRegisterService(index) {
                this.closeBusServices();
                this.selectedService = this.services[index];
                $('#modal-register-service').modal('show');
            },

            /**
             * Hide the registration form for bus service.
             */
            closeRegisterService() {
                $('#modal-register-service').modal('hide');
                this.form.name = '';
                this.form.busy = false;
                this.form.errors = [];
                this.form.bus = [];
            },

            /**
             * Show the form to register bus service.
             */
            registerBusService() {
                // prepare data for storing
                let data = {};
                data.name = this.form.name;
                data.bus_stop_code = this.currentBusStop.bus_stop_code;
                data.service_no = this.selectedService.service_no;
                data.operator = this.selectedService.operator;
                data.origin_code = this.selectedService.next_bus.origin_code;
                data.destination_code = this.selectedService.next_bus.destination_code;
                axios['post']('/api/buses', data)
                    .then(response => {
                        this.form.name = '';
                        this.form.busy = false;
                        this.form.errors = [];
                        this.getBuses();
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            this.form.errors = _.flatten(_.toArray(error.response.data));
                        } else {
                            this.form.errors = ['Something went wrong. Please try again.'];
                        }
                        this.form.busy = false;
                    });
            },
        }
    }
</script>
