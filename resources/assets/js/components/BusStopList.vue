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
                        <bus-stop :busStop="busStop" v-for="busStop in busStops" :key="busStop.id"></bus-stop>
                    </ul>

                    <div class="panel-footer">
                        <p v-if="busStopPagination.links" class="text-center">
                            <a class="action-link" @click="moreBusStops()">
                                More
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import EventBus from './event-bus';

    export default {
        data() {
            return {
                busStops: [],
                form: {
                    name: '',
                    busy: false,
                    errors: []
                },
                selectedService: [],
                busStopPagination: {}
            };
        },

        created() {
            const self = this;
            EventBus.$on('location-updated', (latitude, longitude) => self.getBusStops(latitude, longitude));
        },

        mounted() {
            console.log('Component mounted.')
        },

        methods: {
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
            getBusStops(latitude, longitude) {
                axios.get('/api/bus-stops?latitude=' + latitude + '&longitude=' + longitude)
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
            }
        }
    }
</script>
