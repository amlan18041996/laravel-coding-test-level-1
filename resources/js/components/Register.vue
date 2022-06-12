<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header fw-bold h4">REGISTER</div>
                    <div class="card-body">
                        <form
                            novalidate
                            id="validateForm"
                            @submit.prevent="register"
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
                                <label for="name" class="form-label"
                                    >Name</label
                                >
                                <input
                                    id="name"
                                    type="text"
                                    v-model="user.name"
                                    class="form-control mb-3"
                                />
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
                            <div class="mb-3">
                                <label
                                    for="password_confirmation"
                                    class="form-label"
                                    >Confirm Password</label
                                >
                                <input
                                    type="password"
                                    class="form-control mb-3"
                                    id="password_confirmation"
                                    v-model="user.password_confirmation"
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
                            <small>Already have an account?</small>
                            <router-link to="/login" class="ms-1">
                                Login
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
    name: "register",
    data() {
        return {
            url: document.head.querySelector('meta[name="url"]').content,
            errors: [],
            user: {
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
        };
    },
    methods: {
        register() {
            this.errors = [];
            if (!this.user.name) {
                this.errors.push("Name is required.");
            }
            if (!this.user.email) {
                this.errors.push("Email is required.");
            }
            if (!this.user.password) {
                this.errors.push("Password is required.");
            }
            if (!this.user.password_confirmation) {
                this.errors.push("Confirm Password is required.");
            }
            if (this.user.password !== this.user.password_confirmation) {
                this.errors.push("Password and Confirm Password do not match.");
            }

            if (this.errors.length <= 0) {
                let url = this.url + "/api/user/register";
                let formData = new FormData();
                formData.append("name", this.user.name);
                formData.append("email", this.user.email);
                formData.append("password", this.user.password);
                formData.append(
                    "password_confirmation",
                    this.user.password_confirmation
                );
                this.axios
                    .post(url, formData)
                    .then((res) => {
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
                    .catch((err) => {
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
