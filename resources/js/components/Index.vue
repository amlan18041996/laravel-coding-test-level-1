<template>
    <div class="col-md-8">
        <h2 class="text-center">All Events</h2>
        <div class="row">
            <div class="col-md-3 py-2">
                <span v-if="!loggedInToken">Hello Guest</span>
                <span v-if="loggedInToken"
                    >Welcome <span class="fw-bold">{{ user.name }}</span></span
                >
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-3 text-end text-secondary pe-3 py-2">
                <button
                    @click.prevent="loginOrNavigate('/logout')"
                    class="btn btn-primary btn-sm shadow-none"
                    v-if="loggedInToken"
                >
                    Logout
                </button>
                <div v-if="!loggedInToken">
                    <router-link
                        to="/login"
                        class="btn btn-primary btn-sm shadow-none me-2"
                    >
                        Login
                    </router-link>
                    <router-link
                        to="/register"
                        class="btn btn-primary btn-sm shadow-none"
                    >
                        Register
                    </router-link>
                </div>
            </div>
        </div>
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
            <button
                @click.prevent="loginOrNavigate('/create')"
                class="btn btn-info"
            >
                Create an Event
            </button>
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
                        <button
                            @click.prevent="
                                loginOrNavigate(`/edit/${event.id}`)
                            "
                            class="btn btn-warning btn-sm me-3"
                        >
                            Edit
                        </button>

                        <button
                            @click.prevent="
                                loginOrNavigate(`/delete/${event.id}`)
                            "
                            class="btn btn-danger btn-sm me-3"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 text-center">
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
            <div class="col-md-3 text-end text-secondary pe-3">
                Page
                <span class="text-dark fw-bold">{{ events.current_page }}</span>
                <small>of</small>
                <span class="text-dark fw-bold">{{ events.total_page }}</span>
            </div>
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
                current_page: 0,
                total_page: 0,
            },
            user: JSON.parse(localStorage.getItem("user")),
            search_value: "",
            loading: false,
            loggedInToken: localStorage.getItem("token"),
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
                    this.events.current_page = res.data.data.current_page;
                    this.events.total_page = res.data.data.last_page;
                    this.loading = false;
                })
                .catch((err) => (this.loading = false));
        },
        searchValue() {
            this.loadData();
        },
        loginOrNavigate(path) {
            if (this.loggedInToken) {
                this.$router.push(path);
            } else {
                this.$router.push("/login");
            }
        },
    },
    mounted() {
        this.loadData();
    },
};
</script>
