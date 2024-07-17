<script>
import axios from "axios";
import { ref } from "vue";

import Layout from "../../layouts/auth";

import { required, email, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";

import {useNotificationStore } from '@/state/pinia'
import { usePinia } from 'pinia';
import { useAuthStore } from "../../state/pinia";

const notificationStore = useNotificationStore();

/**
 * Login component
 */
export default {
    setup() {
        const pinia = usePinia();
        const authStore = useAuthStore(pinia);
        const v$ = useVuelidate() ;
        const email = ref('');
        const password = ref('');
        const submitted = ref(false);
        const authError = ref(null);
        const tryingToLogIn = ref(false);
        const isAuthError = ref(false);
    },

    components: {
        Layout,
    },
    data() {
        return {
            email: "",
            password: "",
            submitted: false,
            authError: null,
            tryingToLogIn: false,
            isAuthError: false,
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
                            console.log("Login successful!")
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

                            <div class="mb-2">
                                <BFormCheckbox class="me-2 form-check-input" id="customControlInline" name="checkbox-1"
                                    value="accepted" unchecked-value="not_accepted"> Remember me</BFormCheckbox>
                            </div>

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
