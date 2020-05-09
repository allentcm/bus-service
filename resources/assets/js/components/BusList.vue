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
                        <bus :bus="bus" v-for="bus in buses" :key="bus.id" @updated="getBuses" />
                    </ul>
                    <p class="text-center" v-if="buses.length === 0">
                        Go to the Bus Stop panel to register a bus.
                    </p>
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
                buses: [],
                editForm: {
                    name: '',
                    busy: false,
                    errors: []
                },
            };
        },

        created() {
            const self = this;
            EventBus.$on(['new-bus', 'bus-registered'], () => self.getBuses());
        },

        mounted() {
            console.log('Component mounted.');
            this.getBuses();
        },

        methods: {
            /**
             * Get all the user's buses
             */
            getBuses() {
                axios.get('/api/buses')
                    .then(response => {
                        this.buses = response.data.entries;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
        }
    }
</script>
