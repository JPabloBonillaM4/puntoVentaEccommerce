<template>
    <div>
        <!-- Navbar -->
        <nav class="landing-navbar">
            <div class="landing-navbar-container">
                <span class="landing-navbar-brand">Ermy's</span>
                <ul class="landing-navbar-links">
                    <li :class="{ active: vista === 'inicio' }" @click="vista = 'inicio'">Inicio</li>
                    <li :class="{ active: vista === 'acerca' }" @click="vista = 'acerca'">Acerca de nosotros</li>
                </ul>
            </div>
        </nav>
        <div v-if="vista === 'inicio'" class="landing-bg">
            <div class="landing-container">
                <!-- Hero -->
                <section class="landing-hero">
                    <div>
                        <h1>Bienvenido a <span class="landing-brand">Ermy's</span></h1>
                        <p class="landing-lead">
                            Confecciones y bordados personalizados para tu empresa, colegio o proyecto especial.<br>
                            Descubre nuestros productos y lleva tu imagen al siguiente nivel.
                        </p>
                        <!-- <a href="/punto-venta" class="landing-btn">Ir al Punto de Venta</a> -->
                    </div>
                    <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80" alt="Ermy's" class="landing-hero-img">
                </section>

                <!-- Productos Carousel -->
                <section class="landing-section">
                    <h2 class="landing-section-title">Nuestros Productos</h2>
                    <div v-if="cargando" class="landing-loading">Cargando productos...</div>
                    <div v-else-if="error" class="landing-error">{{ error }}</div>
                    <div v-else class="landing-slider-wrapper">
                        <button class="landing-slider-arrow left" @click="prevSlide" :disabled="!canPrev">&#8592;</button>
                        <div class="landing-slider">
                            <div
                                v-for="(prod, idx) in productos"
                                v-show="isVisible(idx)"
                                :key="prod.id"
                                class="landing-product-card"
                            >
                                <img v-if="prod.image_url" :src="prod.image_url" :alt="prod.nombre" class="landing-product-img">
                                <div v-else class="landing-product-img landing-product-img-placeholder">
                                    <span>Sin imagen</span>
                                </div>
                                <div class="landing-product-info">
                                    <h3>{{ prod.nombre }}</h3>
                                    <p class="landing-product-desc">{{ prod.descripcion || 'Producto de alta calidad.' }}</p>
                                    <div class="landing-product-price">${{ prod.precio.toFixed(2) }}</div>
                                </div>
                            </div>
                        </div>
                        <button class="landing-slider-arrow right" @click="nextSlide" :disabled="!canNext">&#8594;</button>
                    </div>
                    <div v-if="productos.length > productosPorVista" class="landing-slider-dots">
                        <span
                            v-for="i in totalSlides"
                            :key="i"
                            :class="{ active: i-1 === sliderIndex }"
                            @click="goToSlide(i-1)"
                        ></span>
                    </div>
                </section>

                <!-- CTA final -->
                <section class="landing-section landing-cta">
                    <h2>¿Tienes un proyecto en mente?</h2>
                    <p>Contáctanos y recibe asesoría personalizada para confecciones y bordados únicos.</p>
                    <a href="mailto:contacto@ermys.com" class="landing-btn landing-btn-cta">Contáctanos</a>
                </section>
            </div>
        </div>
        <div v-else>
            <acercaNosotros />
        </div>
    </div>
</template>
<script>
import acercaNosotros from '../admin/pages/acerca_nosotros/index.vue';
export default {
    data() {
        return {
            productos: [],
            cargando: true,
            error: '',
            vista: 'inicio',
            sliderIndex: 0,
            productosPorVista: 3
        }
    },
    components: {
        acercaNosotros
    },
    computed: {
        totalSlides() {
            return Math.ceil(this.productos.length / this.productosPorVista);
        },
        canPrev() {
            return this.sliderIndex > 0;
        },
        canNext() {
            return this.sliderIndex < this.totalSlides - 1;
        }
    },
    methods: {
        async cargarProductos() {
            this.cargando = true;
            this.error = '';
            try {
                const res = await fetch('/productos/all');
                if (!res.ok) throw new Error('Error al obtener productos');
                const data = await res.json();
                this.productos = data.map(p => ({
                    id: p.id,
                    nombre: p.nombre || p.name,
                    precio: p.precio || p.price,
                    descripcion: p.descripcion || p.description,
                    image_url: p.image_url
                }));
            } catch (e) {
                this.error = 'No se pudieron cargar los productos.';
            } finally {
                this.cargando = false;
            }
        },
        updateProductosPorVista() {
            if (window.innerWidth < 600) {
                this.productosPorVista = 1;
            } else if (window.innerWidth < 900) {
                this.productosPorVista = 2;
            } else {
                this.productosPorVista = 3;
            }
            if (this.sliderIndex > this.totalSlides - 1) {
                this.sliderIndex = Math.max(0, this.totalSlides - 1);
            }
        },
        prevSlide() {
            if (this.canPrev) this.sliderIndex--;
        },
        nextSlide() {
            if (this.canNext) this.sliderIndex++;
        },
        goToSlide(idx) {
            this.sliderIndex = idx;
        },
        isVisible(idx) {
            const start = this.sliderIndex * this.productosPorVista;
            const end = start + this.productosPorVista;
            return idx >= start && idx < end;
        }
    },
    mounted() {
        this.cargarProductos();
        this.updateProductosPorVista();
        window.addEventListener('resize', this.updateProductosPorVista);
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.updateProductosPorVista);
    }
}
</script>
<style scoped>
/* Navbar styles */
.landing-navbar {
    width: 100%;
    background: #fff;
    box-shadow: 0 2px 12px #2d8cf022;
    position: sticky;
    top: 0;
    z-index: 100;
}
.landing-navbar-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
}
.landing-navbar-brand {
    font-size: 1.45rem;
    font-weight: 800;
    color: #2d8cf0;
    letter-spacing: 1px;
}
.landing-navbar-links {
    display: flex;
    gap: 24px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.landing-navbar-links li {
    font-size: 1.08rem;
    font-weight: 600;
    color: #444;
    cursor: pointer;
    padding: 8px 18px;
    border-radius: 8px;
    transition: background 0.16s, color 0.16s;
}
.landing-navbar-links li.active,
.landing-navbar-links li:hover {
    background: #2d8cf0;
    color: #fff;
}
@media (max-width: 900px) {
    .landing-navbar-container {
        flex-direction: column;
        height: auto;
        padding: 8px 8px;
        gap: 8px;
    }
    .landing-navbar-links {
        gap: 10px;
    }
}
.landing-bg {
    min-height: 100vh;
    background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
    padding: 0;
}
.landing-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 18px 60px 18px;
}
.landing-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 36px;
    background: linear-gradient(90deg, #e0e7ff 60%, #f0f9ff 100%);
    border-radius: 18px;
    box-shadow: 0 4px 24px #2d8cf022;
    padding: 36px 32px;
    margin-bottom: 38px;
    flex-wrap: wrap;
}
.landing-hero h1 {
    font-size: 2.6rem;
    color: #2d8cf0;
    font-weight: 800;
    margin-bottom: 12px;
    letter-spacing: 1px;
}
.landing-brand {
    color: #19be6b;
    text-shadow: 0 2px 8px #19be6b22;
}
.landing-lead {
    font-size: 1.25rem;
    color: #444;
    margin-bottom: 18px;
    font-weight: 500;
}
.landing-hero-img {
    width: 220px;
    height: 220px;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 4px 24px #2d8cf033;
    border: 6px solid #fff;
}
.landing-btn {
    display: inline-block;
    background: linear-gradient(90deg, #2d8cf0 60%, #19be6b 100%);
    color: #fff;
    padding: 14px 38px;
    border-radius: 10px;
    font-size: 1.15rem;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 2px 12px #2d8cf022;
    transition: background 0.18s, transform 0.14s;
    margin-top: 10px;
}
.landing-btn:hover {
    background: linear-gradient(90deg, #2563eb 60%, #0ca678 100%);
    transform: scale(1.05);
}
.landing-section {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px #2d8cf012;
    padding: 32px 24px 28px 24px;
    margin-bottom: 32px;
}
.landing-section-title {
    color: #2d8cf0;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 28px;
    text-align: center;
}
.landing-loading, .landing-error {
    color: #888;
    text-align: center;
    padding: 24px 0;
    font-size: 1.1rem;
}
.landing-error {
    color: #ff4d4f;
}
.landing-slider-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin-bottom: 18px;
}
.landing-slider {
    display: flex;
    overflow: hidden;
    width: 100%;
    max-width: 900px;
    min-height: 360px;
    position: relative;
    justify-content: center;
}
.landing-product-card {
    min-width: 260px;
    max-width: 260px;
    margin: 0 8px;
    transition: transform 0.18s, box-shadow 0.18s;
    background: linear-gradient(120deg, #e0e7ff 60%, #f0f9ff 100%);
    border-radius: 14px;
    box-shadow: 0 2px 12px #2d8cf012;
    padding: 18px 16px 16px 16px;
    width: 260px;
    min-height: 340px;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 12px;
    position: relative;
}
.landing-product-card[style*="display: none"] {
    display: none !important;
}
.landing-product-card:hover {
    transform: translateY(-6px) scale(1.04);
    box-shadow: 0 6px 24px #2d8cf033;
}
.landing-product-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 14px;
    border: 4px solid #fff;
    box-shadow: 0 2px 8px #2d8cf022;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}
.landing-product-img-placeholder {
    background: #e0e7ff;
    color: #aaa;
    font-size: 1rem;
    font-weight: 600;
}
.landing-product-info {
    text-align: center;
    flex: 1;
    width: 100%;
}
.landing-product-info h3 {
    color: #2d8cf0;
    font-size: 1.18rem;
    font-weight: 700;
    margin-bottom: 6px;
}
.landing-product-desc {
    color: #444;
    font-size: 1.01rem;
    margin-bottom: 10px;
    min-height: 38px;
}
.landing-product-price {
    color: #19be6b;
    font-size: 1.15rem;
    font-weight: 700;
    margin-top: 8px;
}
.landing-cta {
    text-align: center;
    /* Fondo más oscuro y llamativo */
    background: linear-gradient(90deg, #2d8cf0 60%, #19be6b 100%);
    box-shadow: 0 2px 12px #19be6b22;
}
.landing-cta h2,
.landing-cta p {
    color: #fff;
}
.landing-btn-cta {
    margin-top: 18px;
    font-size: 1.18rem;
    padding: 16px 44px;
    background: #fff !important;
    color: #2d8cf0 !important;
    box-shadow: 0 2px 12px #fff4;
    border: none;
}
.landing-btn-cta:hover {
    background: #f0f9ff !important;
    color: #19be6b !important;
}
.landing-slider-arrow {
    background: #2d8cf0;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 38px;
    height: 38px;
    font-size: 1.5rem;
    cursor: pointer;
    margin: 0 8px;
    box-shadow: 0 2px 8px #2d8cf022;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.18s, transform 0.14s;
}
.landing-slider-arrow:disabled {
    background: #b2c7e6;
    color: #fff;
    cursor: not-allowed;
}
.landing-slider-arrow:hover:not(:disabled) {
    background: #2563eb;
    transform: scale(1.08);
}
.landing-slider-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 8px;
}
.landing-slider-dots span {
    display: inline-block;
    width: 12px;
    height: 12px;
    background: #b2c7e6;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.18s, transform 0.14s;
}
.landing-slider-dots span.active,
.landing-slider-dots span:hover {
    background: #2d8cf0;
    transform: scale(1.15);
}
@media (max-width: 900px) {
    .landing-navbar-container {
        flex-direction: column;
        height: auto;
        padding: 8px 8px;
        gap: 8px;
    }
    .landing-navbar-links {
        gap: 10px;
    }
    .landing-hero {
        flex-direction: column;
        gap: 18px;
        text-align: center;
    }
    .landing-hero-img {
        margin: 0 auto;
    }
    .landing-products {
        flex-direction: column;
        gap: 18px;
        align-items: center;
    }
    .landing-slider {
        max-width: 600px;
    }
}
@media (max-width: 600px) {
    .landing-slider {
        max-width: 320px;
    }
    .landing-product-card {
        min-width: 90vw;
        max-width: 95vw;
    }
}
</style>