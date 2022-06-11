<template>
    <div class="col-md-8 col-xl-6">
        <div class="card">
            <div class="card-header">Create an Event</div>
            <div class="card-body">
                <form
                    id="validateForm"
                    @submit.prevent="updateEvent"
                    novalidate
                >
                    <div class="alert alert-danger" v-if="errors.length">
                        <ul class="mb-0">
                            <li v-for="(error, index) in errors" :key="index">
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label for="event" class="form-label">Event Name</label>
                        <input
                            placeholder="Type and press Enter.."
                            type="text"
                            v-model="event_name"
                            id="event"
                            class="form-control mb-3"
                        />
                    </div>
                    <button class="btn btn-info me-3">Submit</button>
                    <router-link to="/" class="btn btn-primary">
                        Cancel
                    </router-link>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            url: document.head.querySelector('meta[name="url"]').content,
            event: [],
            event_name: "",
            errors: [],
        };
    },
    methods: {
        loadEvent() {
            let url = this.url + "/api/v1/events/" + this.$route.params.id;
            this.axios.get(url).then((res) => {
                console.log(res.data.data);
                this.event_name = res.data.data.name;
            });
        },
        updateEvent() {
            this.errors = [];
            if (!this.event_name) {
                this.errors.push("Event name is required.");
            }

            if (this.errors.length <= 0) {
                let url = this.url + "/api/v1/events/" + this.$route.params.id;
                let formData = new FormData();
                formData.append("event_name", this.event_name);
                this.axios
                    .put(url, { event_name: this.event_name })
                    .then((res) => {
                        if (res.status) {
                            this.$utils.showSuccess("success", res.message);
                        } else {
                            this.$utils.showError("error", res.message);
                        }
                    })
                    .catch((err) => {
                        this.errors.push(err.response.data.error);
                    });
            }
        },
    },
    mounted() {
        this.loadEvent();
    },
};
</script>
