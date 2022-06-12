<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header fw-bold h4">
                        Confirm your action
                    </div>
                    <div class="card-body">Do you really want to logout?</div>
                    <div class="card-footer d-flex justify-content-end">
                        <button
                            class="btn btn-danger btn-sm me-3 px-3"
                            @click.prevent="logout()"
                        >
                            Yes
                        </button>
                        <router-link to="/" class="btn btn-primary btn-sm px-3">
                            No
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "delete",
    data() {
        return {
            url: document.head.querySelector('meta[name="url"]').content,
        };
    },
    methods: {
        logout() {
            let url = `${this.url}/api/user/logout`;
            this.axios
                .post(url, "", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                })
                .then((res) => {
                    if (res.status) {
                        this.$utils.showSuccess("success", res.message);
                        localStorage.clear();
                        setTimeout(() => {
                            this.$router.push("/");
                        }, 2000);
                    } else {
                        this.$utils.showError("error", res.message);
                    }
                })
                .catch((err) => {
                    localStorage.clear();
                    this.errors.push(err.response.data.error);
                });
        },
    },
};
</script>
