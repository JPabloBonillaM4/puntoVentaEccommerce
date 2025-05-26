<template>
    <div class="row actions_filter_content">
        <div class="col-md-6 mb-5">
            <input type="text" v-model="filters.text" placeholder="Buscar..." class="form-control bg-transparent" @input="searchData">
        </div>
        <div class="col-md-6 mb-5 text-center">
            <button class="btn btn-success w-100" @click="openFormModal">Crear nuevo</button>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            filters : {
                text : null
            },
            // user_roles : window.Laravel.user_roles,
            // user_permissions : window.Laravel.user_permissions
        }
    },
    methods: {
        searchData() {
            this.$emit('refreshFiltersAndData',this.filters);
        },
        openFormModal(){
            this.$emit('openFormModal');
        }
    },
}
</script>
<style>
@media only screen and (max-width: 768px) {
    .actions_filter_content{
        display: flex;
        flex-direction: column-reverse;
    }
}
</style>