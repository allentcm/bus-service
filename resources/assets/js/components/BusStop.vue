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
        <dl class="dl-horizontal">
            <dt>Bus Stop Code:</dt>
            <dd>{{ busStop.bus_stop_code }}</dd>
            <dt>Location:</dt>
            <dd>{{ description }}</dd>
            <dt>Coordinate:</dt>
            <dd>{{ busStop.latitude }}, {{ busStop.longitude }}</dd>
        </dl>

        <a class="action-link" @click="showBusServices()">
            Sevices
        </a>

        <!-- Show bus services modal -->
        <div class="modal fade" :id="'modal-services-' + busStop.id" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" aria-hidden="true" @click="closeBusServices()">&times;</button>
                        <h4 class="modal-title">Bus Services for {{ busStop.bus_stop_code }}: {{ description }}</h4>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item" v-for="(service, index) in services">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <dl class="dl-horizontal">
                                    <dt>Service No:</dt>
                                    <dd>{{ service.service_no }}</dd>
                                    <dt>Arrival Time:</dt>
                                    <dd>{{ showArrival(service.next_bus.estimated_arrival) }}</dd>
                                </dl>
                                <a class="action-link" @click="showRegisterService(index)">
                                    Register
                                </a>
                            </div>
                        </li>
                    </ul>
                    <p class="text-center" v-if="services.length === 0">
                        No service available at the moment.
                    </p>
                </div>
            </div>
        </div>

        <!-- Register bus service modal -->
        <div class="modal fade" :id="'modal-register-' + busStop.id" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" aria-hidden="true" @click="closeRegisterService()">&times;</button>
                        <h4 class="modal-title">Register for Bus No {{ selectedService.service_no }} at {{ description }}</h4>
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
                        <dl class="dl-horizontal">
                            <dt>Bus Stop Code:</dt>
                            <dd>{{ busStop.bus_stop_code }}</dd>
                            <dt>Service No:</dt>
                            <dd>{{ selectedService.service_no }}</dd>
                            <dt>Operator:</dt>
                            <dd>{{ selectedService.operator }}</dd>
                            <dt>Origin Code:</dt>
                            <dd>{{ selectedService.next_bus.origin_code }}</dd>
                            <dt>Destination Code:</dt>
                            <dd>{{ selectedService.next_bus.destination_code }}</dd>
                        </dl>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeRegisterService()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
    import EventBus from './event-bus';

    export default {
        props: [
            'busStop',
        ],

        data() {
            return {
                selectedService: {
                    next_bus: {}
                },
                services: [],
                form: {
                    name: '',
                    busy: false,
                    errors: []
                },
            };
        },

        mounted() {
            console.log('Component mounted.');
        },

        computed: {
            description() {
                return this.busStop.road_name + ', '
                    + this.busStop.description;
            },
        },

        methods: {
            /**
             * Get all the bus services for the target bus stop.
             */
            getBusServices(busStopCode) {
                // clear services
                this.services = [];

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
            showBusServices() {
                $('#modal-services-' + this.busStop.id).modal('show');

                this.getBusServices(this.busStop.bus_stop_code);
            },

            /**
             * Hide the current bus stop bus services.
             */
            closeBusServices() {
                $('#modal-services-' + this.busStop.id).modal('hide');
            },

            /**
             * Show the registration form for bus service.
             */
            showRegisterService(index) {
                this.closeBusServices();
                this.selectedService = this.services[index];

                $('#modal-register-' + this.busStop.id).modal('show');
            },

            /**
             * Hide the registration form for bus service.
             */
            closeRegisterService() {
                $('#modal-register-' + this.busStop.id).modal('hide');

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
                data.bus_stop_code = this.busStop.bus_stop_code;
                data.service_no = this.selectedService.service_no;
                data.operator = this.selectedService.operator;
                data.origin_code = this.selectedService.next_bus.origin_code;
                data.destination_code = this.selectedService.next_bus.destination_code;
                axios['post']('/api/buses', data)
                    .then(response => {
                        this.form.name = '';
                        this.form.busy = false;
                        this.form.errors = [];

                        this.closeRegisterService();

                        EventBus.$emit('bus-registered');
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

            /**
             * Show arrival time
             */
            showArrival(dateString) {
                let date = new Date(dateString);
                return date.toLocaleTimeString([], { hour12: true});
            }
        }
    }
</script>
