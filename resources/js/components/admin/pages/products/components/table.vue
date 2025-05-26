<template>
    <div>
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <!--begin::Table head-->
                <thead>
                    <tr class="fw-bold text-muted text-center">
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody v-if="data.length > 0">
                    <tr class="text-center" v-for="item in data">
                        <td>
                            <img :src="item.image_url" style="width: 100px;" alt="">
                        </td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.price }}</td>
                        <td>
                            <button class="btn btn-icon btn-bg-warning btn-color-light btn-sm me-1" @click="editData(item)">
                                <i class="ki-outline ki-pencil fs-2"></i>
                            </button>
                            <button class="btn btn-icon btn-bg-danger btn-color-light btn-sm me-1" @click="deleteItem(item)">
                                <i class="ki-outline ki-trash fs-2"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr class="text-center">
                        <td colspan="5">
                            Sin datos
                        </td>
                    </tr>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!-- begin:Pagination -->
        <table-pagination
            ref="table_pagination"
            :total="total_length_data"
            :page_size="page_size"
            :current_page="current_page"
            @current-page-change="setPage"
        ></table-pagination>
        <!-- end:Pagination -->
    </div>
</template>
<script>
export default {
    data() {
        return {
            data         : [],
            current_page : 1,
            page_size    : 5,
            filters      : null,
            total_length_data : 0
        }
    },
    mounted() {
        this.getData();
    },
    methods: {
        setPage (val) {
            this.current_page = val;
            this.getData();
        },
        getData() {
            let _this = this;
            axios.get('/productos/all',{
                params : {
                    filters : this.filters,
                    paginated : true,
                    page : this.current_page
                }
            }).then(function(response){
                _this.data = response.data.data;
                _this.total_length_data = response.data.total;
            });
        },
        editData(data){
            this.$emit('openFormModal',data);
        },
        deleteItem(data){
            let _this = this;
            Swal.fire({
                text: `¿Está seguro de eliminar el producto ${data.fullname}?`,
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: "Si",
                denyButtonText: `No, cancelar`
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/productos/${data.id}`).then(function(response){
                        response = response.data;
                        if(!response.error){
                            toastr.success(response.message);
                            _this.$emit('refreshData');
                        } else {
                            factoryPrizma.methods.showSweetAlert(response.message,'error');
                        }
                    });
                }
            });
        }
    },
}
</script>