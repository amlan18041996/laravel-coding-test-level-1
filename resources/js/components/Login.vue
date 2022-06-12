<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header fw-bold h4">LOGIN</div>
                    <div class="card-body">
                        <form
                            novalidate
                            id="validateForm"
                            @submit.prevent="login"
                        >
                            <div
                                class="alert alert-danger"
                                v-if="errors.length"
                            >
                                <ul class="mb-0">
                                    <li
                                        v-for="(error, index) in errors"
                                        :key="index"
                                    >
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"
                                    >Email</label
                                >
                                <input
                                    id="email"
                                    type="email"
                                    v-model="user.email"
                                    class="form-control mb-3"
                                />
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"
                                    >Password</label
                                >
                                <input
                                    id="password"
                                    type="password"
                                    v-model="user.password"
                                    class="form-control mb-3"
                                />
                            </div>
                            <div class="d-flex justify-content-between">
                                <router-link
                                    to="/"
                                    class="btn btn-primary me-3"
                                >
                                    Cancel
                                </router-link>
                                <button class="btn btn-info">Submit</button>
                            </div>
                        </form>
                        <div class="mt-2">
                            <small>Don't have any account?</small>
                            <router-link to="/register" class="ms-1">
                                Register
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "login",
    data() {
        return {
            url: document.head.querySelector('meta[name="url"]').content,
            errors: [],
            user: {
                email: "",
                password: "",
            },
        };
    },
    methods: {
        login() {
            this.errors = [];
            if (!this.user.email) {
                this.errors.push("Email is required.");
            }
            if (!this.user.password) {
                this.errors.push("Password is required.");
            }

            if (this.errors.length <= 0) {
                let url = this.url + "/api/user/login";
                let formData = new FormData();
                formData.append("email", this.user.email);
                formData.append("password", this.user.password);
                this.axios
                    .post(url, formData)
                    .then((res) => {
                        console.log(res);
                        if (res.status) {
                            this.$utils.showSuccess("success", res.message);
                            localStorage.setItem("token", res.data.data.token);
                            localStorage.setItem(
                                "user",
                                JSON.stringify(res.data.data.user)
                            );
                            setTimeout(() => {
                                this.$router.push("/");
                            }, 2000);
                        } else {
                            this.$utils.showError("error", res.message);
                        }
                    })
                    .catch((err, errText) => {
                        if (err.response.status === 404) {
                            this.errors.push(err.response.data.message);
                        } else {
                            Object.values(err.response.data.errors).forEach(
                                (error) => {
                                    error.forEach((er) => {
                                        this.errors.push(er);
                                    });
                                }
                            );
                        }
                    });
            }
        },
    },
};
</script>
