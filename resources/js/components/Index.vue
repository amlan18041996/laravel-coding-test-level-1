<template>
    <div class="col-md-8">
        <h2>All Events</h2>
        <div class="d-flex justify-content-between">
            <form>
                <input
                    placeholder="Type and press Enter.."
                    type="text"
                    class="form-control"
                />
            </form>
            <router-link to="/create" class="btn btn-info">
                Create an Event
            </router-link>
        </div>
        <table class="table table-hover">
            <thead>
                <tr class="text-center">
                    <th scope="col">ID</th>
                    <th scope="col">Event Name</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Created At</th>
                    <th scope="col text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="text-center"
                    v-for="event in events.data"
                    :key="event.id"
                >
                    <th scope="row">{{ event.id }}</th>
                    <td>{{ event.name }}</td>
                    <td>{{ event.slug }}</td>
                    <td>{{ event.createdAt }}</td>
                    <td class="d-flex justify-content-center">
                        <router-link
                            :to="{ path: '/edit/' + event.id }"
                            class="btn btn-warning btn-sm me-3"
                        >
                            Edit
                        </router-link>
                        <button
                            @click.prevent="deleteEvent(event.id)"
                            class="btn btn-danger btn-sm"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            <button
                :disabled="events.prev_page_url === null"
                @click.prevent="loadData(events.prev_page_url)"
                class="btn btn-primary btn-sm shadow-none me-2"
            >
                Previous
            </button>
            <button
                :disabled="events.next_page_url === null"
                @click.prevent="loadData(events.next_page_url)"
                class="btn btn-primary btn-sm shadow-none ms-2"
            >
                Next
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            url: document.head.querySelector('meta[name="url"]').content,
            events: [],
        };
    },
    methods: {
        loadData(pageUrl) {
            let url = pageUrl ? pageUrl : this.url + "/api/v1/events";
            this.axios.get(url).then((res) => {
                this.events = res.data.data;
            });
        },
        deleteEvent(id) {
            let url = this.url + "/api/v1/events/" + id;
            this.axios
                .delete(url)
                .then((res) => {
                    if (res.status) {
                        this.loadData();
                        this.$utils.showSuccess("success", res.message);
                    } else {
                        this.$utils.showError("error", res.message);
                    }
                })
                .catch((err) => {
                    this.errors.push(err.response.data.error);
                });
        },
    },
    mounted() {
        console.log("Component mounted.");
        this.loadData();
    },
};
</script>
