<template>
    <div class="mt-5">
        <!-- ACTIONS -->
        <actions-component ref="actions_component" @refreshFiltersAndData="refreshFiltersAndData" @openFormModal="openFormModal"></actions-component>
        <!-- TABLE -->
        <table-component ref="table_component" @refreshData="refreshTableData" @openFormModal="openFormModal"></table-component>
        <!-- MODALS -->
        <form-component ref="form_component" @refreshData="refreshTableData"></form-component>
    </div>
</template>
<script>
import tableComponent from './components/table.vue';
import actionsComponent from './components/actions.vue';
import formComponent from './components/modals/form.vue';
export default {
    components : {
        "table-component"     : tableComponent,
        "actions-component"   : actionsComponent,
        "form-component"      : formComponent
    },
    methods: {
        refreshTableData() {
            this.$refs.table_component.getData();
        },
        refreshFiltersAndData(filters){
            this.$refs.table_component.filters = filters;
            this.refreshTableData();
        },
        openFormModal(data = null){
            this.$refs.form_component.openModal(data);
        }
    },
}
</script>