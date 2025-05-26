<template>
    <div class="modal fade" tabindex="-1" id="modal_create_product">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Stepper-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">DATOS DEL PROYECTO</h5>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Imagen llamativa -->
                        <div class="col-lg-12 col-md-12 mb-5">
                            <label for="product-image" class="form-label">Imagen del producto</label>
                            <div class="mb-2">
                                <input type="file" id="product-image" accept="image/*" class="form-control" @change="onImageChange">
                            </div>
                            <div v-if="imagePreview" class="mb-2">
                                <img :src="imagePreview" alt="Previsualización" style="max-width: 100%; max-height: 200px; border: 1px solid #ddd; border-radius: 8px;"/>
                            </div>
                            <small class="text-danger" v-if="errors.image.show">{{ errors.image.message }}</small>
                        </div>
                        <div class="col-lg-12 col-md-12 mb-5">
                            <label for="">Nombre</label>
                            <input type="text" class="form-control bg-transparent" v-model="form.name">
                            <small class="text-danger" v-if="errors.name.show">{{ errors.name.message }}</small>
                        </div>
                        <div class="col-lg-12 col-md-12 mb-5">
                            <label for="">Descripción</label>
                            <textarea class="form-control bg-transparent" v-model="form.description"></textarea>
                            <small class="text-danger" v-if="errors.description.show">{{ errors.description.message }}</small>
                        </div>
                        <div class="col-lg-12 col-md-12 mb-5">
                            <label for="">Precio</label>
                            <input type="number" class="form-control bg-transparent" v-model="form.price">
                            <small class="text-danger" v-if="errors.price.show">{{ errors.price.message }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" :disabled="saving_data" @click="saveData">
                        <span v-if="!saving_data">Guardar</span>
                        <span v-else>Guardando...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
            <!--end::Stepper-->
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            saving_data : false,
            edit_mode   : false,
            form : {
                id : 0,
                name : null,
                description : null,
                price : null,
                image : null
            },
            imagePreview: null,
            errors: {
                "name" :  {
                    'show'    : false,
                    'message' : null
                },
                "description" :  {
                    'show'    : false,
                    'message' : null
                },
                "price" :  {
                    'show'    : false,
                    'message' : null
                },
                "image" : {
                    'show'    : false,
                    'message' : null
                }
            }
        }
    },
    methods: {
        onImageChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.form.image = file;
                this.imagePreview = URL.createObjectURL(file);
                this.errors.image.show = false;
                this.errors.image.message = null;
            } else {
                this.form.image = null;
                this.imagePreview = null;
            }
        },
        openModal(data) {
            this.resetErrors();
            if(data){
                this.edit_mode = true;
                this.setFormData(data);
            } else {
                this.resetFormData();
                this.edit_mode = false;
            }
            $('#modal_create_product').modal('show');
        },
        resetFormData(){
            this.saving_data  = false;
            this.form.name    = null;
            this.form.description = null;
            this.form.price   = null;
            this.form.image   = null;
            this.imagePreview = null;
        },
        async setFormData(data){
            this.resetFormData();
            this.form.id = data.id;
            this.form.name = data.name;
            this.form.description = data.description;
            this.form.price = data.price;
            // No previsualización de imagen en edición (a menos que se tenga la URL)
            this.imagePreview = data.image_url || null;
        },
            
        resetErrors(){
            this.errors.name.show = false;
            this.errors.name.message = null;
            this.errors.description.show = false;
            this.errors.description.message = null;
            this.errors.price.show = false;
            this.errors.price.message = null;
            this.errors.image.show = false;
            this.errors.image.message = null;
        },
        formValidation(){
            let valid = true;
            if(!this.form.name){ // Name
                this.errors.name.show = true;
                this.errors.name.message = 'Ingrese un nombre';
                valid = false;
            } else {
                this.errors.name.show = false;
                this.errors.name.message = null;
            }
            if(!this.form.description){ // Description
                this.errors.description.show = true;
                this.errors.description.message = 'Ingrese una descripción';
                valid = false;
            } else {
                this.errors.description.show = false;
                this.errors.description.message = null;
            }
            if(!this.form.price){ // Price
                this.errors.price.show = true;
                this.errors.price.message = 'Ingrese un precio';
                valid = false;
            } else {
                this.errors.price.show = false;
                this.errors.price.message = null;
            }
            if(!this.form.image && !this.edit_mode){ // Image
                this.errors.image.show = true;
                this.errors.image.message = 'Seleccione una imagen';
                valid = false;
            } else {
                this.errors.image.show = false;
                this.errors.image.message = null;
            }

            return valid;
        },
        saveData(){
            if(!this.formValidation()){ return false; } // Validation form
            this.saving_data = true;
            let _this = this;
            let request_data = null;
            // Si hay imagen, usar FormData
            if(this.form.image instanceof File){
                let formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('description', this.form.description);
                formData.append('price', this.form.price);
                formData.append('image', this.form.image);
                if(this.edit_mode){
                    formData.append('_method', 'PUT');
                    request_data = axios.post(`/productos/${this.form.id}`, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                } else {
                    request_data = axios.post('/productos', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    });
                }
            } else {
                // Sin imagen, enviar como JSON normal
                if(this.edit_mode){
                    request_data = axios.put(`/productos/${this.form.id}`, this.form);
                } else {
                    request_data = axios.post('/productos', this.form);
                }
            }
            if(request_data){
                request_data.then(function(response){
                    response = response.data;
                    if(!response.error){
                        toastr.success(response.message);
                        $('#modal_create_product').modal('hide');
                        _this.$emit('refreshData');
                    } else {
                        toastr.error(response.message);
                    }
                    _this.saving_data = false;
                });
            }
        }
    },
}
</script>