<script>
// import axios from "axios";

import Layout from "../../layouts/auth";


import { required, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
// import { useRouter } from "vue-router";
import { useAuthStore } from "../../state/pinia/auth";

import { useNotificationStore } from '@/state/pinia'
// import router from "../../router";
// import { ref } from "vue";
// import { useRouter } from "vue-router";
// import router from "../../router";


const notificationStore = useNotificationStore();

/**
 * Login component
 */
export default {
    setup() {

        return {

            v$: useVuelidate(),
        };

    },

    components: {
        Layout,

    },
    data() {
        return {

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
        watch: {
            email() {
                this.v$.email.$touch();
                this.v$.password.$touch();
            },
            password() {
                this.v$.email.$touch();
                this.v$.password.$touch();
            }
        },


        async tryToLogIn() {
            this.submitted = true;
            // stop here if form is invalid
            this.v$.email.$touch();
            this.v$.password.$touch();
            if (this.v$.email.$invalid || this.v$.password.$invalid) {
                return;
            } else {
                try {

                    const authStore = useAuthStore();
                    const redirectRoute = await authStore.logIn({ email: this.email, password: this.password, role: this.role });
                    const userRole = localStorage.getItem('userRole');
                    console.log('User role from local storage:', userRole);
                    if (userRole !== 'Agent') {
                        // Si l'utilisateur n'est pas un client, déconnectez-le et redirigez-le vers une page 403
                        authStore.logOut();
                        this.$router.push({ name: 'page403' });
                        return;
                    }


                    this.authSucces = "Connexion réussie";
                    this.isAuthSucces = true;
                    this.$router.push({ name: redirectRoute });
                } catch (error) {
                    console.error("Login error: ", error);
                    this.authError = "Email ou mot de passe invalide";
                    this.isAuthError = true;
                }

            }
        },
    },
};
</script>

<template>
    <Layout>
        <BRow class="justify-content-center">
            <BCol md="10" lg="8" xl="8">
                <BCard no-body class="overflow-hidden">
                    <BRow>
                        <BCol md="5" class="bg-light p-4 d-flex flex-column justify-content-center">
                            <div class="text-center">
                                <h4> Bienvenue sur votre portail d'agent de support</h4>
                                <p class="mb-0"> Merci de vous connecter</p>
                            </div>
                        </BCol>
                        <BCol md="7">
                            <BCardBody class="pt-0">
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
                                            }" @input="validateEmail"></BFormInput>
                                        <div v-for="(item, index) in v$.email.$errors" :key="index"
                                            class="invalid-feedback">
                                            <span v-if="item.$message">{{ item.$message }}</span>
                                        </div>
                                    </BFormGroup>

                                    <BFormGroup class="mb-3" id="input-group-2" label="Password" label-for="input-2">
                                        <BFormInput id="input-2" v-model="password" type="password"
                                            placeholder="Enter password" :class="{
                                                'is-invalid': submitted && v$.password.$error,
                                            }" @input="validateEmail"></BFormInput>
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

                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
