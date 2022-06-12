<template>
    <div class="col-md-8 col-xl-6">
        <div class="card">
            <div class="card-header fw-bold h4">Edit an Event</div>
            <div class="card-body">
                <div class="text-center" v-if="loading">
                    <div role="status" class="spinner-border text-secondary">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <form
                    novalidate
                    id="validateForm"
                    v-if="loading === false && Object.keys(event).length"
                    @submit.prevent="updateEvent"
                >
                    <div class="alert alert-danger" v-if="errors.length">
                        <ul class="mb-0">
                            <li v-for="(error, index) in errors" :key="index">
                                {{ error }}
                            </li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label for="event_id" class="form-label">ID</label>
                        <input
                            readonly
                            type="text"
                            id="event_id"
                            v-model="event.id"
                            class="form-control mb-3"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="event_name" class="form-label"
                            >Event Name</label
                        >
                        <input
                            type="text"
                            id="event_name"
                            v-model="event_name"
                            class="form-control mb-3"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input
                            readonly
                            id="slug"
                            type="text"
                            v-model="event.slug"
                            class="form-control mb-3"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="created_at" class="form-label"
                            >Created at</label
                        >
                        <input
                            readonly
                            type="text"
                            id="created_at"
                            class="form-control mb-3"
                            v-model="new Date(event.createdAt).toLocaleString()"
                        />
                    </div>
                    <div class="mb-3">
                        <label for="updated_at" class="form-label"
                            >Updated at</label
                        >
                        <input
                            readonly
                            type="text"
                            id="updated_at"
                            class="form-control mb-3"
                            v-model="new Date(event.updatedAt).toLocaleString()"
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
            event: {},
            errors: [],
            event_name: "",
            loading: false,
        };
    },
    methods: {
        loadEvent() {
            let url = this.url + "/api/v1/events/" + this.$route.params.id;
            this.loading = true;
            this.axios
                .get(url, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                })
                .then((res) => {
                    this.loading = false;
                    this.event = res.data.data;
                    this.event_name = res.data.data.name;
                })
                .catch((err) => (this.loading = false));
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
                    .put(
                        url,
                        { event_name: this.event_name },
                        {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem(
                                    "token"
                                )}`,
                            },
                        }
                    )
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
    mounted() {
        this.loadEvent();
    },
};
</script>
