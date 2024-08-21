<script>
import accueil from "@/assets/images/accueil.jpg"
import logo from "@/assets/images/logo.jpg"
import googleCloud from "@/assets/images/googleCloud.jpg"
import googleWorkspace from "@/assets/images/googleWorkspace.jpg"
import application from "@/assets/images/application.jpeg"


// import { Autoplay } from "swiper";
import "swiper/css";
import "swiper/css/autoplay";
// import axios from "axios";
import { useAuthStore } from "../../state/pinia/auth";

import { useNotificationStore } from '@/state/pinia'
import { required, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core"
const notificationStore = useNotificationStore();

export default {
    name: "HomePage",

    setup() {
        return {
            v$: useVuelidate(),
        };
    },
    data() {
        return {
            showModal: true,
            accueil,
            logo,

            googleCloud,
            googleWorkspace,
            application,
            email: "",
            password: "",
            submitted: false,
            authSucces: false,
            isAuthSucces: false,
            authError: null,
            tryingToLogIn: false,
            isAuthError: false,
            role: null
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
        const userRole = localStorage.getItem('userRole');
        console.log('User role from local storage:', userRole);
        if (userRole) {
            this.userRole = userRole;
        }
    },
    methods: {
        changePassword() { },
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
                    this.showModal = false
                } catch (error) {
                    console.error("Login error: ", error);
                    this.authError = "Email ou mot de passe invalide";
                    this.isAuthError = true;
                }

            }
        },
        gotoclients() {
            this.$router.push('/listTicketClient');
        },
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
    }

};
</script>

<template>
    <div>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white sticky" id="navbar">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img :src="logo" alt="Logo" height="50">
                </a>
                <BButton class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </BButton>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="#head">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#footer">Contact</a>
                        </li>
                        <!-- <li class="nav-item">
                     <a class="btn btn-primary" href="#">SIGN IN / SIGN UP</a>
                  </li> -->
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Header -->
        <header id="head" class="text-center image" :style="{ backgroundImage: `url(${accueil})` }">
            <!-- <img :src="Acuueil" alt="Background Image" class="img-fluid" /> -->
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="display-4">Comment pouvons nous vous aider?</h1>
                        <p>
                            <a class="btn btn-secondary btn-lg me-1" role="button">Plus d'infos</a>
                            <a class="btn btn-primary btn-lg" @click="gotoclients" role="button">Espace Client</a>
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Services Section -->
        <section class="services text-center my-5" id="services">
            <div class="container">
                <h2 class="font-weight-light">Nos services</h2>
                <p class="text-muted">Parcourez ici nos services</p>
                <Layout>
                    <BRow class="justify-content-center">
                        <BCol lg="10">
                            <BRow>
                                <BCol xs="12" sm="6" md="4" class="mb-4">
                                    <BCard no-body class="card-hover h-100">
                                        <BCardBody>
                                            <div class="d-flex align-items-center mb-3">
                                                <h2 class="font-size-14 mb-0"><strong>Google Workspace
                                                        Expertise</strong></h2>
                                            </div>
                                            <div class="text-muted mt-4">
                                                <div class="d-flex">
                                                    <span class="ms-2" style="white-space: normal;">
                                                        Lorem ipsum, in graphical and textual context, refers to filler
                                                        text that is placed in a document or visual presentation. Lorem
                                                        ipsum is derived from the Latin “dolorem ipsum” roughly
                                                        translated as “pain itself.”
                                                    </span>
                                                </div>
                                                <img :src="googleWorkspace" alt="" class="img-fluid mt-3">
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                                <BCol xs="12" sm="6" md="4" class="mb-4">
                                    <BCard no-body class="card-hover h-100">
                                        <BCardBody>
                                            <div class="d-flex align-items-center mb-3">
                                                <h5 class="font-size-14 mb-0"><strong>Google Workspace</strong></h5>
                                            </div>
                                            <div class="text-muted mt-4">
                                                <div class="d-flex">
                                                    <span class="ms-2" style="white-space: normal;">
                                                        Lorem ipsum, in graphical and textual context, refers to filler
                                                        text that is placed in a document or visual presentation. Lorem
                                                        ipsum is derived from the Latin “dolorem ipsum” roughly
                                                        translated as “pain itself.”
                                                    </span>
                                                </div>
                                                <img :src="googleCloud" alt="" class="img-fluid mt-3">
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                                <BCol xs="12" sm="6" md="4" class="mb-4">
                                    <BCard no-body class="card-hover h-100">
                                        <BCardBody>
                                            <div class="d-flex align-items-center mb-3">
                                                <h2 class="font-size-14 mb-0"><strong>Application Development</strong>
                                                </h2>
                                            </div>
                                            <div class="text-muted mt-4">
                                                <div class="d-flex">
                                                    <span class="ms-2" style="white-space: normal;">
                                                        Lorem ipsum, in graphical and textual context, refers to filler
                                                        text that is placed in a document or visual presentation. Lorem
                                                        ipsum is derived from the Latin “dolorem ipsum” roughly
                                                        translated as “pain itself.”
                                                    </span>
                                                </div>
                                                <img :src="application" alt="" class="img-fluid mt-3">
                                            </div>
                                        </BCardBody>
                                    </BCard>
                                </BCol>
                            </BRow>
                        </BCol>
                    </BRow>
                </Layout>
            </div>
            <BModal md="12"  v-model="showModal" hide-footer hide-header :no-close-on-backdrop="true" >

                <div class="p-2">
                    <BRow>

                        <BCol>
                            <BCardBody class="pt-0 fly-in-top">
                                <h4>Se connecter</h4>
                                <BAlert v-model="isAuthError" variant="danger" class="mt-3" dismissible>{{ authError }}
                                </BAlert>
                                <div v-if="notification.message" :class="'alert ' + notification.type">
                                    {{ notification.message }}
                                </div>
                                <BAlert v-model="isAuthSucces" variant="success" class="mt-3" dismissible>{{ authSucces
                                    }}
                                </BAlert>
                                <div v-if="notification.message" :class="'alert ' + notification.type">
                                    {{ notification.message }}
                                </div>

                                <BForm class="p-4 " @submit.prevent="tryToLogIn">
                                    <BFormGroup class="mb-3" id="input-group-1" label="Email" label-for="input-1">
                                        <BFormInput id="input-1" v-model="email" class="w-100 mb-2" type="text"
                                            placeholder="Enter email" :class="{
                                                'is-invalid': submitted && v$.email.$error,
                                            }"></BFormInput>
                                        <div v-for="(item, index) in v$.email.$errors" :key="index"
                                            class="invalid-feedback">
                                            <span v-if="item.$message">{{ item.$message }}</span>
                                        </div>
                                    </BFormGroup>

                                    <BFormGroup class="mb-3" id="input-group-2" label="Password" label-for="input-2">
                                        <BFormInput id="input-2" v-model="password" type="password"
                                            placeholder="Enter password" :class="{
                                                'is-invalid': submitted && v$.password.$error,
                                            }"></BFormInput>
                                        <div v-if="submitted && v$.password.$error" class="invalid-feedback">
                                            <span v-if="v$.password.required.$message">{{
                                                v$.password.required.$message
                                                }}</span>
                                        </div>
                                    </BFormGroup>

                                    <div class="mt-3 d-grid">
                                        <BButton type="submit" variant="primary" class="btn-block">Log In</BButton>
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
                </div>

            </BModal>
        </section>




        <!-- Footer -->
        <footer class="footer-expand-lg bg-dark text-center py-4" id="footer">
            <div class="container">
                <p class="text-white">Pour plus d'informations, contactez le mail suivant <a
                        href="mailto:info@example.com">info@example.com</a></p>

            </div>
        </footer>
    </div>
</template>



<style scoped>
/* Add your custom styles here */
.header-background {
    position: relative;
    height: 100vh;
    overflow: hidden;
}

.header-background img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

#head {
    color: white;
    padding: 150px 0;
    text-align: center;
}

.header-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    text-shadow: 0px 0px 10px rgba(0, 0, 0, 0.7);
}

.services .service-item {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 20px;
}

.footer {
    background-color: #f8f9fa;
    color: #6c757d;
}

.card-hover:hover {
    transform: translateX(10px);
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.image {

    background-size: cover;
    width: 100%;
    height: 600px;
}
@keyframes flyInFromTop {
  0% {
    transform: translateY(-100%);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

.fly-in-top {
  animation: flyInFromTop 0.5s ease-out;
}
</style>
