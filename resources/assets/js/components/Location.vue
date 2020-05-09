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
                        <p>Coordinate: {{ latitude }}, {{ longitude }}</p>
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
                latitude: 0.00,
                longitude: 0.00,
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
                        EventBus.$emit('location-updated', this.latitude, this.longitude);
                    });
                }
            },
        }
    }
</script>
