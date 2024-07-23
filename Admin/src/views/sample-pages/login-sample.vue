<script>
import axios from "axios";



import Layout from "../../layouts/auth";


import { required, email, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";

import { useNotificationStore } from '@/state/pinia'


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
            email: helpers.withMessage("Please enter valid email", email),
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
    methods: {
        // Try to log the user in with the username
        // and password they provided.

        tryToLogIn() {
            this.submitted = true;
            // stop here if form is invalid
            this.v$.$touch();

            if (this.v$.$invalid) {
                return;
            } else {

                axios.post("http://127.0.0.1:8000/api/login", {
                    email: this.email,
                    password: this.password,
                })
                    .then((res) => {
                        if (res.data.status === "success") {
                            console.log("Login successful!");
                            this.authSucces = res.data.message;
                            this.isAuthSucces = true;
                            localStorage.setItem("authToken", res.data.token);//stocker le token de l'utilisateur
                            const user = res.data.user.role;

                            localStorage.setItem("userRole", user);
                            // this.$router.push('/activite');

                            let redirectRoute = '/listuser';
                            let redirRoute = '/listticket';
                            // let redRoute = '/createtickets';

                            if (user === 'Admin') {
                                redirectRoute = '/listuser';
                            // } else if (user === 'Client'){
                            //     redRoute = '/createtickets'
                            // }
                            }
                            else if(user === 'Agent'){
                                redirRoute = '/listticket'
                            }
                            this.$router.push(redirectRoute);
                            this.$router.push(redirRoute);
                            // this.$router.push(redRoute);


                        } else {
                            this.authError = res.data.message;
                            this.isAuthError = true;
                        }
                    })
                    .catch((error) => {
                        console.error("Login error: ", error);
                    })


            }
        },
    },
};
</script>

<template>
    <Layout>
        <BRow class="justify-content-center">
            <BCol md="8" lg="6" xl="5">
                <BCard no-body class="overflow-hidden">
                    <BCardBody class="pt-0">
                        <BAlert v-model="isAuthError" variant="danger" class="mt-3" dismissible>{{ authError }}</BAlert>
                        <div v-if="notification.message" :class="'alert ' + notification.type">
                            {{ notification.message }}
                        </div>
                        <BAlert v-model="isAuthSucces" variant="success" class="mt-3" dismissible>{{ authSucces }}
                        </BAlert>
                        <div v-if="notification.message" :class="'alert ' + notification.type">
                            {{ notification.message }}
                        </div>

                        <BForm class="p-5" @submit.prevent="tryToLogIn">
                            <BFormGroup class="mb-3" id="input-group-1" label="Email" label-for="input-1">
                                <BFormInput id="input-1" v-model="email" type="text" placeholder="Enter email" :class="{
                                    'is-invalid': submitted && v$.email.$error,
                                }"></BFormInput>
                                <div v-for="(item, index) in v$.email.$errors" :key="index" class="invalid-feedback">
                                    <span v-if="item.$message">{{ item.$message }}</span>
                                </div>
                            </BFormGroup>

                            <BFormGroup class="mb-3" id="input-group-2" label="Password" label-for="input-2">
                                <BFormInput id="input-2" v-model="password" type="password" placeholder="Enter password"
                                    :class="{
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
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
