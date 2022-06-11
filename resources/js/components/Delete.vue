<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Confirm your action</div>
                    <div class="card-body">Do you really want to delete?</div>
                    <div class="card-footer">
                        <button class="btn btn-danger btn-sm me-3">Yes</button>
                        <router-link to="/" class="btn btn-primary btn-sm"
                            >No</router-link
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "delete",
    mounted() {
        console.log("Component mounted.");
    },
};
</script>
