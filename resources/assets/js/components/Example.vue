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

                            <a class="action-link" @click="getBusStops">
                                Refresh
                            </a>
                        </div>
                    </div>

                    <div class="panel-body">
                        <li class="list-group-item" v-for="busStop in busStops">
                            <p>{{ busStop.BusStopCode }}</p>
                        </li>
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
            };
        },

        mounted() {
            console.log('Component mounted.')
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
            },

            /**
             * Get all the bus stops in SG.
             */
            getBusStops() {
                const self = this;
                const apiUrl = 'http://datamall2.mytransport.sg/ltaodataservice/BusStops';
                const xhr = this.createCORSRequest('GET', apiUrl);
                xhr.setRequestHeader('AccountKey', 'mFif1WYVRouNYFiWTZKqZQ==');
                xhr.setRequestHeader('accept', 'application/json')
                if (!xhr) {
                    return;
                }

                xhr.onreadystatechange = function() {
                    if(xhr.readyState == 4) {
                        if(xhr.status == 200) {
                            var response = JSON.parse(xhr.responseText);
                            if (typeof response.data === 'object') {
                                self.busStops = response.data.value;
                            } else {
                            }
                        } else {
                            var response = JSON.parse(xhr.responseText);
                            // show error message
                            Console.log(response.status_code + ": " + response.status_txt);
                        }
                    }
                }
                xhr.send();
            },

            /**
             * Create CORS request
             */
            createCORSRequest(method, url) {
                var xhr = new XMLHttpRequest();

                if ('withCredentials' in xhr) {
                    // XHR for Chrome/Firefox/Opera/Safari.
                    xhr.open(method, url, true);
                } else if (typeof XDomainRequest != 'undefined') {
                    // XDomainRequest for IE.
                    xhr = new XDomainRequest();
                    xhr.open(method, url);
                } else {
                    // CORS not supported.
                    xhr = null;
                }
                return xhr;
            },

            /**
             * Show the form for creating new clients.
             */
            showCreateClientForm() {
                $('#modal-create-client').modal('show');
            },

            /**
             * Create a new OAuth client for the user.
             */
            store() {
                this.persistClient(
                    'post', '/oauth/clients',
                    this.createForm, '#modal-create-client'
                );
            },

            /**
             * Edit the given client.
             */
            edit(client) {
                this.editForm.id = client.id;
                this.editForm.name = client.name;
                this.editForm.redirect = client.redirect;

                $('#modal-edit-client').modal('show');
            },

            /**
             * Update the client being edited.
             */
            update() {
                this.persistClient(
                    'put', '/oauth/clients/' + this.editForm.id,
                    this.editForm, '#modal-edit-client'
                );
            },

            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.getClients();

                        form.name = '';
                        form.redirect = '';
                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Destroy the given client.
             */
            destroy(client) {
                axios.delete('/oauth/clients/' + client.id)
                        .then(response => {
                            this.getClients();
                        });
            }
        }
    }
</script>
