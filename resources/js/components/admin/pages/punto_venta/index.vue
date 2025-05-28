<template>
    <div class="pv-bg">
        <div class="pv-container">
            <h2 class="pv-title">Punto de Venta</h2>
            <div class="pv-flex">
                <!-- Lista de productos como tarjetas -->
                <div class="pv-card pv-products">
                    <h3 class="pv-section-title">Productos</h3>
                    <div v-if="cargandoProductos" class="pv-loading">Cargando productos...</div>
                    <div v-else-if="errorProductos" class="pv-error">{{ errorProductos }}</div>
                    <div v-else class="pv-products-grid">
                        <div v-for="prod in productos" :key="prod.id" class="pv-product-card">
                            <div class="pv-product-img-wrapper">
                                <img v-if="prod.image_url" :src="prod.image_url" :alt="prod.nombre" class="pv-product-img" />
                                <div v-else class="pv-product-img pv-product-img-placeholder">
                                    <span>Sin imagen</span>
                                </div>
                            </div>
                            <div class="pv-product-card-body">
                                <strong class="pv-prod-name">{{ prod.nombre }}</strong>
                                <div class="pv-prod-price">${{ prod.precio.toFixed(2) }}</div>
                            </div>
                            <button @click="agregarAlCarrito(prod)" class="pv-btn pv-btn-add pv-product-card-btn">Agregar</button>
                        </div>
                    </div>
                </div>
                <!-- El carrito ahora es un drawer, así que quitamos el panel fijo -->
            </div>
            <!-- Botón flotante de carrito -->
            <button class="pv-cart-fab" @click="drawerAbierto = true">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24">
                    <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM7.16 14h9.45c.75 0 1.41-.41 1.75-1.03l3.24-6.16a1 1 0 0 0-.89-1.47H6.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 17.37 5.48 19 7 19h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63z" fill="#2d8cf0"/>
                </svg>
                <span v-if="carrito.length" class="pv-cart-fab-badge">{{ totalProductos }}</span>
            </button>
            <!-- Drawer lateral del carrito -->
            <transition name="pv-drawer">
                <div v-if="drawerAbierto" class="pv-cart-drawer">
                    <div class="pv-cart-drawer-header">
                        <h3 class="pv-section-title">Carrito</h3>
                        <button class="pv-cart-drawer-close" @click="drawerAbierto = false">&times;</button>
                    </div>
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
                        <button @click="vaciarCarrito" :disabled="carrito.length===0" class="pv-btn pv-btn-remove" style="margin-right:10px;">Vaciar Carrito</button>
                        <button @click="finalizarVenta" :disabled="carrito.length===0" class="pv-btn pv-btn-finalizar">Finalizar Compra</button>
                    </div>
                </div>
            </transition>
            <transition name="pv-fade">
                <div v-if="mensaje" class="pv-msg pv-msg-topright">
                    <span>{{ mensaje }}</span>
                </div>
            </transition>
            <!-- Fondo oscuro al abrir drawer -->
            <div v-if="drawerAbierto" class="pv-cart-drawer-backdrop" @click="drawerAbierto = false"></div>
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
            errorProductos: '',
            drawerAbierto: false
        }
    },
    computed: {
        total() {
            return this.carrito.reduce((sum, item) => sum + item.precio * item.cantidad, 0);
        },
        totalProductos() {
            return this.carrito.reduce((sum, item) => sum + item.cantidad, 0);
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
            this.mensaje = `Producto agregado al carrito`;
            this.guardarCarrito();
            setTimeout(() => { this.mensaje = ''; }, 1200);
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
            this.guardarCarrito();
        },
        vaciarCarrito() {
            this.carrito = [];
            this.guardarCarrito();
            this.mensaje = 'Carrito vaciado';
            setTimeout(() => { this.mensaje = ''; }, 1200);
        },
        finalizarVenta() {
            this.mensaje = `¡Compra realizada por $${this.total.toFixed(2)}!`;
            this.carrito = [];
            this.guardarCarrito();
            this.drawerAbierto = false;
            setTimeout(() => { this.mensaje = ''; }, 2500);
        },
        guardarCarrito() {
            localStorage.setItem('pv-carrito', JSON.stringify(this.carrito));
        },
        cargarCarrito() {
            const data = localStorage.getItem('pv-carrito');
            if (data) {
                try {
                    this.carrito = JSON.parse(data);
                } catch (e) {
                    this.carrito = [];
                }
            }
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
                    precio: p.precio || p.price,
                    image_url: p.image_url // <-- Asegúrate que el backend envía image_url
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
        this.cargarCarrito();
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
    color: #222;
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
/* Notificación fija arriba a la derecha */
.pv-msg-topright {
    position: fixed;
    top: 32px;
    right: 38px;
    z-index: 1300;
    margin-top: 0;
    text-align: left;
    min-width: 220px;
    max-width: 90vw;
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

/* FAB carrito */
.pv-cart-fab {
    position: fixed;
    bottom: 38px;
    right: 38px;
    z-index: 1202;
    background: #fff;
    border: none;
    border-radius: 50%;
    box-shadow: 0 4px 18px #2d8cf044;
    width: 62px;
    height: 62px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: box-shadow 0.18s, transform 0.12s;
}
.pv-cart-fab:hover {
    box-shadow: 0 8px 32px #2d8cf066;
    transform: scale(1.08);
}
.pv-cart-fab-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff4d4f;
    color: #fff;
    border-radius: 50%;
    font-size: 0.98rem;
    font-weight: 700;
    padding: 2px 8px;
    min-width: 24px;
    text-align: center;
    box-shadow: 0 2px 8px #ff4d4f44;
}

/* Drawer lateral */
.pv-cart-drawer {
    position: fixed;
    top: 0;
    right: 0;
    width: 370px;
    max-width: 96vw;
    height: 100vh;
    background: #fff;
    box-shadow: -2px 0 24px #2d8cf044;
    z-index: 1203;
    display: flex;
    flex-direction: column;
    padding: 28px 22px 22px 22px;
    animation: pv-drawer-in 0.28s;
}
@keyframes pv-drawer-in {
    0% { transform: translateX(100%); opacity: 0.2; }
    100% { transform: translateX(0); opacity: 1; }
}
.pv-cart-drawer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.pv-cart-drawer-close {
    background: none;
    border: none;
    font-size: 2.1rem;
    color: #888;
    cursor: pointer;
    line-height: 1;
    padding: 0 8px;
    transition: color 0.18s;
}
.pv-cart-drawer-close:hover {
    color: #ff4d4f;
}
.pv-cart-drawer-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #0005;
    z-index: 1201;
    animation: pv-backdrop-in 0.28s;
}
@keyframes pv-backdrop-in {
    0% { opacity: 0; }
    100% { opacity: 1; }
}
.pv-drawer-enter-active, .pv-drawer-leave-active {
    transition: opacity 0.28s;
}
.pv-drawer-enter, .pv-drawer-leave-to {
    opacity: 0;
    pointer-events: none;
}

/* Grid de tarjetas de productos */
.pv-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 22px 18px;
    margin-top: 8px;
    margin-bottom: 8px;
}
.pv-product-card {
    background: linear-gradient(120deg, #e0e7ff 60%, #f0f9ff 100%);
    border-radius: 14px;
    box-shadow: 0 2px 12px #2d8cf012;
    padding: 18px 14px 14px 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: box-shadow 0.18s, transform 0.14s;
    min-height: 180px;
}
.pv-product-card:hover {
    box-shadow: 0 6px 24px #2d8cf033;
    transform: translateY(-4px) scale(1.03);
}
.pv-product-img-wrapper {
    width: 90px;
    height: 90px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pv-product-img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px #2d8cf022;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pv-product-img-placeholder {
    background: #e0e7ff;
    color: #aaa;
    font-size: 0.98rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pv-product-card-body {
    flex: 1;
    width: 100%;
    text-align: center;
    margin-bottom: 12px;
}
.pv-product-card-btn {
    width: 100%;
    margin: 0;
    padding: 8px 0;
    font-size: 1.01rem;
}
</style>