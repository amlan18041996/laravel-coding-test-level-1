<template>
    <div class="col-md-8 col-xl-6">
        <div class="card">
            <div class="card-header fw-bold h4">Create an Event</div>
            <div class="card-body">
                <form id="validateForm" @submit.prevent="saveEvent" novalidate>
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
                            id="event"
                            type="text"
                            v-model="event_name"
                            class="form-control mb-3"
                            placeholder="Type and press Enter.."
                        />
                    </div>
                    <div class="d-flex justify-content-between">
                        <router-link to="/" class="btn btn-primary me-3">
                            Cancel
                        </router-link>
                        <button class="btn btn-info">Submit</button>
                    </div>
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
            event_name: "",
            errors: [],
        };
    },
    methods: {
        saveEvent() {
            this.errors = [];
            if (!this.event_name) {
                this.errors.push("Event name is required.");
            }

            if (this.errors.length <= 0) {
                let url = this.url + "/api/v1/events";
                let formData = new FormData();
                formData.append("event_name", this.event_name);
                this.axios
                    .post(url, formData)
                    .then((res) => {
                        if (res.status) {
                            this.$utils.showSuccess("success", res.message);
                            setTimeout(() => {
                                this.$router.push("/");
                            }, 2000);
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
};
</script>
