<script>
import { timelineData } from "./data"
import logo1 from "@/assets/images/logo1.png"
import support1 from "@/assets/images/support1.jpg"
// import accueil from "@/assets/images/accueil.jpg"
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Navigation, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/autoplay";
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { required, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import { useAuthStore } from "../../state/pinia/auth";

import { useNotificationStore } from '@/state/pinia'

import logo from "@/assets/images/logo.jpg"
const notificationStore = useNotificationStore();


/**
 * Crypto ICO-landing page
 */
export default {
    components: { Swiper, SwiperSlide },
    setup() {
        return {
            v$: useVuelidate(),
        };
    },
    data() {
        return {
            tickets: [
                { title: 'Incident', incident: 'Serveur en panne', description: 'Le serveur principal est hors ligne depuis 3 heures.' },
                { title: 'Problème', incident: 'Connexion lente', description: 'Les utilisateurs rapportent une lenteur dans la connexion.' },
                { title: 'Incident', incident: 'Mise à jour système', description: 'La mise à jour du système est prévue pour cette nuit.' },
                { title: 'Demande', incident: 'Mise à jour système', description: 'La mise à jour du système est prévue pour cette nuit.' },
                { title: 'Problème', incident: 'Mise à jour système', description: 'La mise à jour du système est prévue pour cette nuit.' },

            ],
            support1, logo1,
            logo, Navigation, Pagination,
            email: "",
            password: "",
            submitted: false,
            authSucces: false,
            isAuthSucces: false,
            authError: null,
            tryingToLogIn: false,
            isAuthError: false,
            role: null,
            timelineData,
            Autoplay: Autoplay,
            breakpoints: { 576: { slidesPerView: 2 }, 768: { slidesPerView: 3 }, 992: { slidesPerView: 4 } }
        };
    },
    validations: {
        email: {
            required: helpers.withMessage("Email is required", required),
            // email: helpers.withMessage("Please enter valid email", email),
        },
        password: {
            required: helpers.withMessage("Password is required", required),
        },
    },
    computed: {
        notification() {
            return notificationStore || {};
        },
    },
    created() {
        window.addEventListener("scroll", this.windowScroll);
        const userRole = localStorage.getItem('userRole');
        console.log('User role from local storage:', userRole);
        if (userRole) {
            this.userRole = userRole;
        }
    },
    // mounted() {

    // },
    methods: {

        async tryToLogIn() {
            this.submitted = true;
            // stop here if form is invalid
            this.v$.$touch();

            if (this.v$.$invalid) {
                return;
            } else {
                try {

                    const authStore = useAuthStore();
                    const redirectRoute = await authStore.logIn({ email: this.email, password: this.password, role: this.role });
                    const userRole = localStorage.getItem('userRole');
                    console.log('User role from local storage:', userRole);


                    this.authSucces = "Connexion réussie";
                    this.isAuthSucces = true;
                    this.$router.push({ name: redirectRoute });
                } catch (error) {
                    console.error("Login error: ", error);
                    if (error.response && error.response.status === 403) {
                        this.authError = "Compte bloqué; Veuillez contacter votre administrateur"
                    } else {
                        this.authError = "Email ou mot de passe invalide";
                    }

                    this.isAuthError = true;
                }

            }
        },

        /**
         * Window scroll method
         */
        windowScroll() {
            const navbar = document.getElementById("navbar");
            if (navbar) {
                if (
                    document.body.scrollTop >= 50 ||
                    document.documentElement.scrollTop >= 50
                ) {
                    navbar.classList.add("nav-sticky");
                } else {
                    navbar.classList.remove("nav-sticky");
                }
            }
        },
        /**
         * Toggle menu
         */
        toggleMenu() {
            document.getElementById("topnav-menu-content").classList.toggle("show");
        },
        toggleAccordion(item) {
            item.open = !item.open;
        }
    },
};
</script>

<template>
    <div>
        <nav class="navbar navbar-expand-lg navigation fixed-top sticky" id="navbar">
            <BContainer>
                <router-link class="navbar-logo" to="/">
                    <img :src=logo alt height="50" class="logo logo-dark" />
                </router-link>

                <BButton variant="white" class="btn btn-sm px-3 font-size-16 d-lg-none header-item"
                    data-toggle="collapse" @click="toggleMenu()">
                    <i class="fa fa-fw fa-bars"></i>
                </BButton>

                <div class="collapse navbar-collapse" id="topnav-menu-content">
                    <ul class="navbar-nav ms-auto" id="topnav-menu" v-scroll-spy-active="{ selector: 'a.nav-link' }">
                        <li class="nav-item">
                            <BLink class="nav-link" v-scroll-to="'#home'" href="#home">Accueil</BLink>
                        </li>
                    </ul>

                    <!-- <div class="ms-lg-2">
            <BLink href="javascript: void(0);" class="btn btn-outline-success w-xs">Sign in</BLink>
          </div> -->
                </div>
            </BContainer>
        </nav>
        <div v-scroll-spy>
            <section class="section hero-section image" :style="{ backgroundImage: `url(${support1})` }" id="home">
                <div class="bg-overlay "></div>
                <BContainer>
                    <BRow class="align-items-center">
                        <BCol lg="5">
                            <div class="text-dark-50">
                                <h1 class="text-dark fw-semibold mb-3 hero-title">Bienvenue sur votre portail</h1>
                                <p class="font-size-14">Veuillez vous connecter pour avoir accès à votre espace
                                    d'administateur</p>
                            </div>
                        </BCol>
                        <BCol lg="5" md="8" sm="10" class="ms-lg-auto">
                            <BCard no-body class="overflow-hidden mb-0 mt-5 mt-lg-0">
                                <BRow>

                                    <BCol>
                                        <BCardBody class="pt-0">
                                            <BAlert v-model="isAuthError" variant="danger" class="mt-3" dismissible>{{
                                                authError }}
                                            </BAlert>
                                            <div v-if="notification.message" :class="'alert ' + notification.type">
                                                {{ notification.message }}
                                            </div>
                                            <BAlert v-model="isAuthSucces" variant="success" class="mt-3" dismissible>{{
                                                authSucces
                                                }}
                                            </BAlert>
                                            <div v-if="notification.message" :class="'alert ' + notification.type">
                                                {{ notification.message }}
                                            </div>

                                            <BForm class="p-5" @submit.prevent="tryToLogIn">
                                                <BFormGroup class="mb-3" id="input-group-1" label="Email"
                                                    label-for="input-1">
                                                    <BFormInput id="input-1" v-model="email" class="w-100 mb-2"
                                                        type="text" placeholder="Enter email" :class="{
                                                            'is-invalid': submitted && v$.email.$error,
                                                        }"></BFormInput>
                                                    <div v-for="(item, index) in v$.email.$errors" :key="index"
                                                        class="invalid-feedback">
                                                        <span v-if="item.$message">{{ item.$message }}</span>
                                                    </div>
                                                </BFormGroup>

                                                <BFormGroup class="mb-3" id="input-group-2" label="Password"
                                                    label-for="input-2">
                                                    <BFormInput id="input-2" v-model="password" type="password"
                                                        placeholder="Enter password" :class="{
                                                            'is-invalid': submitted && v$.password.$error,
                                                        }"></BFormInput>
                                                    <div v-if="submitted && v$.password.$error"
                                                        class="invalid-feedback">
                                                        <span v-if="v$.password.required.$message">{{
                                                            v$.password.required.$message
                                                            }}</span>
                                                    </div>
                                                </BFormGroup>

                                                <div class="mt-3 d-grid">
                                                    <BButton type="submit" variant="primary" class="btn-block">Log In
                                                    </BButton>
                                                </div>
                                                <div class="mt-4 text-center">
                                                    <router-link to="/forgot-password" class="text-muted">
                                                        <i class="mdi mdi-lock me-1"></i> Forgot your password?
                                                    </router-link>
                                                </div>
                                            </BForm>
                                        </BCardBody>
                                    </BCol>
                                </BRow>
                            </BCard>
                        </BCol>
                    </BRow>
                </BContainer>
            </section>

            <section class="section bg-white" id="roadmap">
                <BContainer>
                    <BRow>
                        <BCol lg="12">
                            <div class="text-center mb-5">
                                <div class="small-title">Tickets</div>
                                <h4>Exemples</h4>
                            </div>
                        </BCol>
                    </BRow>
                    <BRow class="mt-4">
                        <BCol lg="12">
                            <div class="hori-timeline">
                                <swiper class="swiper-wrapper" :loop="true"
                                    :modules="[Autoplay, Navigation, Pagination]" :slides-per-view="3"
                                    :space-between="20" :breakpoints="breakpoints"
                                    :autoplay="{ delay: 2500, disableOnInteraction: false }"
                                    :navigation="{ nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }"
                                    :pagination="{ clickable: true, el: '.swiper-pagination' }">

                                    <!-- Example Ticket Cards -->
                                    <swiper-slide v-for="(ticket, index) in tickets" :key="index">
                                        <BCard class="text-center h-100">
                                            <BCardHeader class="bg-primary text-white">
                                                <h5>{{ ticket.title }}</h5>
                                            </BCardHeader>
                                            <BCardBody>
                                                <BCardTitle>{{ ticket.incident }}</BCardTitle>
                                                <BCardText>
                                                    {{ ticket.description }}
                                                </BCardText>
                                            </BCardBody>
                                        </BCard>
                                    </swiper-slide>

                                    <!-- Navigation buttons -->
                                    <div class="owl-nav">
                                        <BButton role="presentation" class="owl-prev swiper-button-prev"></BButton>
                                        <BButton role="presentation" class="owl-next swiper-button-next"></BButton>
                                    </div>
                                </swiper>
                            </div>
                        </BCol>
                    </BRow>


                    <!-- <BRow class="mt-4">
                        <BCol lg="12">
                            <div class="hori-timeline">
                                <swiper class="swiper-wrapper" :loop="true"
                                    :modules="[Autoplay, Navigation, Pagination]" :slides-per-view="3"
                                    :space-between="20" :breakpoints="breakpoints"
                                    :autoplay="{ delay: 2500, disableOnInteraction: false }"
                                    :navigation="{ nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }"
                                    :pagination="{ clickable: true, el: '.swiper-pagination' }">
                                    <swiper-slide v-for="(event, index) in timelineData" :key="index">
                                        <div class="item event-list text-center" :class="{ active: event.active }">
                                            <div>
                                                <div class="event-date">
                                                    <div class="text-primary mb-1">{{ event.date }}</div>
                                                    <h5 class="mb-4">{{ event.title }}</h5>
                                                </div>
                                                <div class="event-down-icon">
                                                    <i
                                                        class="bx bx-down-arrow-circle h1 text-primary down-arrow-icon"></i>
                                                </div>

                                                <div class="mt-3 px-3">
                                                    <p class="text-muted">{{ event.description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </swiper-slide>
                                    <div class="owl-nav">
                                        <BButton role="presentation" class="owl-prev swiper-button-prev"></BButton>
                                        <BButton role="presentation" class="owl-next swiper-button-next"></BButton>
                                    </div>
                                </swiper>
                            </div>
                        </BCol>
                    </BRow> -->
                </BContainer>
            </section>

            <footer class="bg-dark text-center">
                <BRow>
                    <BCol lg="6">
                        <div class="mb-4">
                            <img :src=logo1 alt height="50" />
                        </div>
                        <p class="text-white">Pour plus d'informations contactez le mail suivant </p>
                    </BCol>
                </BRow>

            </footer>
        </div>
    </div>
</template>

<style>
.teambg {
    display: block;
    width: 100%;
}

.form-slide-in {
    opacity: 0;
    transform: translateY(50px);
    transition: all 0.5s ease-in-out;
}

.form-slide-in.visible {
    opacity: 1;
    transform: translateY(0);
}


.image {

    background-size: cover;
    width: 100%;
    height: 600px;
}
</style>
