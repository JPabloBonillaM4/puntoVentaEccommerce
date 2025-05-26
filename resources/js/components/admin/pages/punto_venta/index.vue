<template>
    <div class="pv-bg">
        <div class="pv-container">
            <h2 class="pv-title">Punto de Venta</h2>
            <div class="pv-flex">
                <!-- Lista de productos -->
                <div class="pv-card pv-products">
                    <h3 class="pv-section-title">Productos</h3>
                    <div v-if="cargandoProductos" class="pv-loading">Cargando productos...</div>
                    <div v-else-if="errorProductos" class="pv-error">{{ errorProductos }}</div>
                    <ul v-else class="pv-list">
                        <li v-for="prod in productos" :key="prod.id" class="pv-list-item">
                            <div>
                                <strong class="pv-prod-name">{{ prod.nombre }}</strong>
                                <div class="pv-prod-price">${{ prod.precio.toFixed(2) }}</div>
                            </div>
                            <button @click="agregarAlCarrito(prod)" class="pv-btn pv-btn-add">Agregar</button>
                        </li>
                    </ul>
                </div>
                <!-- Carrito -->
                <div class="pv-card pv-cart">
                    <h3 class="pv-section-title">Carrito</h3>
                    <ul class="pv-list">
                        <li v-if="carrito.length === 0" class="pv-cart-empty">Carrito vacío</li>
                        <li v-for="item in carrito" :key="item.id" class="pv-list-item">
                            <div>
                                <span class="pv-cart-item-name">{{ item.nombre }}</span>
                                <span class="pv-cart-item-qty">x{{ item.cantidad }}</span>
                            </div>
                            <div>
                                <span class="pv-cart-item-total">${{ (item.precio * item.cantidad).toFixed(2) }}</span>
                                <button @click="quitarDelCarrito(item)" class="pv-btn pv-btn-remove">Quitar</button>
                            </div>
                        </li>
                    </ul>
                    <div class="pv-total">
                        <strong>Total: ${{ total.toFixed(2) }}</strong>
                    </div>
                    <div class="pv-actions">
                        <button @click="finalizarVenta" :disabled="carrito.length===0" class="pv-btn pv-btn-finalizar">Finalizar Venta</button>
                    </div>
                </div>
            </div>
            <transition name="pv-fade">
                <div v-if="mensaje" class="pv-msg">
                    <span>{{ mensaje }}</span>
                </div>
            </transition>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            productos: [],
            carrito: [],
            mensaje: '',
            cargandoProductos: true,
            errorProductos: ''
        }
    },
    computed: {
        total() {
            return this.carrito.reduce((sum, item) => sum + item.precio * item.cantidad, 0);
        }
    },
    methods: {
        agregarAlCarrito(prod) {
            const existe = this.carrito.find(item => item.id === prod.id);
            if (existe) {
                existe.cantidad++;
            } else {
                this.carrito.push({ ...prod, cantidad: 1 });
            }
            this.mensaje = '';
        },
        quitarDelCarrito(item) {
            const idx = this.carrito.findIndex(i => i.id === item.id);
            if (idx !== -1) {
                if (this.carrito[idx].cantidad > 1) {
                    this.carrito[idx].cantidad--;
                } else {
                    this.carrito.splice(idx, 1);
                }
            }
        },
        finalizarVenta() {
            this.mensaje = `¡Venta realizada por $${this.total.toFixed(2)}!`;
            this.carrito = [];
            setTimeout(() => { this.mensaje = ''; }, 2500);
        },
        async cargarProductos() {
            this.cargandoProductos = true;
            this.errorProductos = '';
            try {
                const res = await fetch('/productos/all');
                if (!res.ok) throw new Error('Error al obtener productos');
                const data = await res.json();
                this.productos = data.map(p => ({
                    id: p.id,
                    nombre: p.nombre || p.name,
                    precio: p.precio || p.price
                }));
            } catch (e) {
                this.errorProductos = 'No se pudieron cargar los productos.';
            } finally {
                this.cargandoProductos = false;
            }
        }
    },
    mounted() {
        this.cargarProductos();
    }
}
</script>
<style scoped>
.pv-bg {
    min-height: 100vh;
    /* background: linear-gradient(120deg, #e0e7ff 0%, #f0f9ff 100%); */
    display: flex;
    align-items: center;
    justify-content: center;
}
.pv-container {
    max-width: 1100px;
    width: 98%;
    margin: 40px auto;
    padding: 36px 28px 32px 28px;
    /* background: #fff; */
    border-radius: 22px;
    box-shadow: 0 8px 32px #2d8cf033, 0 1.5px 8px #0001;
}
.pv-title {
    text-align: center;
    color: #2d8cf0;
    font-size: 2.3rem;
    font-weight: 700;
    margin-bottom: 32px;
    letter-spacing: 1px;
    text-shadow: 0 2px 8px #2d8cf022;
}
.pv-flex {
    display: flex;
    gap: 36px;
    flex-wrap: wrap;
}
.pv-card {
    flex: 1 1 340px;
    /* background: #f8fafc; */
    border-radius: 16px;
    box-shadow: 0 2px 16px #2d8cf012;
    padding: 24px 20px 20px 20px;
    min-width: 320px;
    min-height: 420px;
    display: flex;
    flex-direction: column;
}
.pv-products {
    margin-right: 0;
}
.pv-cart {
    margin-left: 0;
}
.pv-section-title {
    /* color: #444; */
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 18px;
    letter-spacing: 0.5px;
}
.pv-list {
    list-style: none;
    padding: 0;
    margin: 0;
    flex: 1;
}
.pv-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid #e5e7eb;
    transition: background 0.18s;
}
.pv-list-item:hover {
    background: #e0f2fe44;
}
.pv-prod-name {
    font-size: 1.08rem;
    /* color: #222; */
}
.pv-prod-price {
    font-size: 0.98rem;
    color: #2d8cf0;
    font-weight: 500;
    margin-top: 2px;
}
.pv-btn {
    border: none;
    outline: none;
    border-radius: 8px;
    padding: 8px 18px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.18s, transform 0.12s;
    box-shadow: 0 2px 8px #2d8cf022;
}
.pv-btn-add {
    background: linear-gradient(90deg, #2d8cf0 60%, #5ad1e6 100%);
    color: #fff;
}
.pv-btn-add:hover {
    background: linear-gradient(90deg, #2563eb 60%, #38bdf8 100%);
    transform: scale(1.07);
}
.pv-btn-remove {
    background: #ff4d4f;
    color: #fff;
    margin-left: 12px;
    padding: 6px 14px;
    font-size: 0.98rem;
}
.pv-btn-remove:hover {
    background: #d90429;
    transform: scale(1.08);
}
.pv-btn-finalizar {
    background: linear-gradient(90deg, #19be6b 60%, #43e97b 100%);
    color: #fff;
    padding: 12px 36px;
    font-size: 1.1rem;
    border-radius: 12px;
    margin-top: 10px;
    box-shadow: 0 2px 12px #19be6b33;
}
.pv-btn-finalizar:disabled {
    /* background: #b2f2dd; */
    color: #fff;
    cursor: not-allowed;
    box-shadow: none;
}
.pv-btn-finalizar:not(:disabled):hover {
    background: linear-gradient(90deg, #0ca678 60%, #38e54d 100%);
    transform: scale(1.04);
}
.pv-loading, .pv-error, .pv-cart-empty {
    /* color: #888; */
    text-align: center;
    padding: 24px 0;
    font-size: 1.1rem;
}
.pv-error {
    color: #ff4d4f;
}
.pv-cart-empty {
    color: #aaa;
}
.pv-cart-item-name {
    font-weight: 500;
    /* color: #222; */
}
.pv-cart-item-qty {
    color: #2d8cf0;
    font-weight: 600;
    margin-left: 6px;
}
.pv-cart-item-total {
    font-weight: 500;
    color: #19be6b;
    margin-right: 8px;
}
.pv-total {
    margin-top: 18px;
    font-size: 1.22rem;
    text-align: right;
    /* color: #222; */
    font-weight: 700;
    letter-spacing: 0.5px;
}
.pv-actions {
    margin-top: 24px;
    text-align: right;
}
.pv-msg {
    margin-top: 32px;
    text-align: center;
    background: linear-gradient(90deg, #f0f9eb 60%, #e0f7fa 100%);
    color: #19be6b;
    padding: 14px 32px;
    border-radius: 10px;
    font-size: 1.13rem;
    font-weight: 600;
    box-shadow: 0 2px 12px #19be6b22;
    display: inline-block;
    animation: pv-pop 0.4s;
}
@keyframes pv-pop {
    0% { transform: scale(0.8); opacity: 0.2; }
    80% { transform: scale(1.08); opacity: 1; }
    100% { transform: scale(1); }
}
.pv-fade-enter-active, .pv-fade-leave-active {
    transition: opacity 0.4s;
}
.pv-fade-enter, .pv-fade-leave-to {
    opacity: 0;
}
@media (max-width: 900px) {
    .pv-flex {
        flex-direction: column;
        gap: 24px;
    }
    .pv-card {
        min-width: unset;
    }
}
</style>