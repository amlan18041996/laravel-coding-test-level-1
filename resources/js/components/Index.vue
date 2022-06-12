<template>
    <div class="col-md-8">
        <h2>All Events</h2>
        <div class="d-flex justify-content-between">
            <form>
                <input
                    type="text"
                    class="form-control"
                    v-model="search_value"
                    placeholder="Type and press Enter.."
                    v-on:keydown.enter.prevent="searchValue"
                />
            </form>
            <router-link to="/create" class="btn btn-info">
                Create an Event
            </router-link>
        </div>
        <table class="table table-hover">
            <thead>
                <tr class="text-center">
                    <th scope="col">S. No.</th>
                    <th scope="col">Event Name</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Created At</th>
                    <th scope="col text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text-center" v-if="loading">
                    <td colspan="5" class="table-active">
                        <div
                            role="status"
                            class="spinner-border text-secondary"
                        >
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
                <tr
                    class="text-center"
                    v-if="loading === false && events.data.length <= 0"
                >
                    <td colspan="5" class="table-active">
                        <span>No Events are created yet!</span>
                    </td>
                </tr>
                <tr
                    :key="event.id"
                    class="text-center"
                    v-for="(event, index) in events.data"
                    v-if="loading === false && events.data.length > 0"
                >
                    <th scope="row">{{ index + 1 }}</th>
                    <td>{{ event.name }}</td>
                    <td>{{ event.slug }}</td>
                    <td>
                        {{ new Date(event.createdAt).toLocaleString() }}
                    </td>
                    <td class="d-flex justify-content-center">
                        <router-link
                            :to="{ path: '/edit/' + event.id }"
                            class="btn btn-warning btn-sm me-3"
                        >
                            Edit
                        </router-link>

                        <router-link
                            :to="{ path: '/delete/' + event.id }"
                            class="btn btn-danger btn-sm me-3"
                        >
                            Delete
                        </router-link>
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
            events: {
                data: [],
                prev_page_url: "",
                next_page_url: "",
            },
            search_value: "",
            loading: false,
        };
    },
    methods: {
        loadData(pageUrl) {
            let url = pageUrl ? pageUrl : this.url + "/api/v1/events";
            if (this.search_value !== "") {
                url += pageUrl
                    ? `&search_value=${this.search_value}`
                    : `?search_value=${this.search_value}`;
            }
            this.loading = true;
            this.axios
                .get(url)
                .then((res) => {
                    this.events.data = res.data.data.data;
                    this.events.prev_page_url = res.data.data.prev_page_url;
                    this.events.next_page_url = res.data.data.next_page_url;
                    this.loading = false;
                })
                .catch((err) => (this.loading = false));
        },
        searchValue() {
            this.loadData();
        },
    },
    mounted() {
        this.loadData();
    },
};
</script>
