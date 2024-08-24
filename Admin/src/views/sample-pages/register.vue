<script>
import axios from "axios";
import { required, helpers, } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core"

/**
 * Register sample page
 */
export default {
    props: ['token', 'email'],
    setup() {
        return {
            v$: useVuelidate(),
        };
    },
    data() {
        return {
            localToken: this.token,
            localEmail: this.email,
            password: '',
            password_confirmation: '',
            submitted: false
        };
    },
    validations: {

        password: {
            required: helpers.withMessage("Password is required", required),
        },
        password_confirmation: {
            required: helpers.withMessage("Confirm password is required", required),


        },
    },
    methods: {
        tryToResetpwd() {
            this.submitted = true;
            // stop here if form is invalid
            this.v$.$touch();

            if (this.v$.$invalid) {
                return;
            } else {
                axios
                    .post(`http://127.0.0.1:8000/api/changeClient`, {
                        email: this.localEmail,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                        token: this.localToken,
                    })
                    .then((res) => {
                        if (res.data.message) {
                            alert('Compte activé avec succès!');
                            this.$router.push('/accueil');
                        } else {
                            this.errorMessage = 'Erreur';
                        }
                    });
            }
        },
    }

};
</script>

<template>
    <div class="account-pages my-5 pt-5">
        <BContainer>
            <BRow class="justify-content-center">
                <BCol md="8" lg="6" xl="5">
                    <BCard no-body class="overflow-hidden">
                        <div class="bg-primary-subtle">
                            <BRow>
                                <BCol cols="7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Compte bloqué?</h5>
                                        <p>Veuillez changer de mot de passe.</p>
                                    </div>
                                </BCol>

                            </BRow>
                        </div>
                        <BCardBody class="pt-0">
                            <div>
                                <BLink href="/">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="@/assets/images/logo1.png" alt class="rounded-circle"
                                                height="20" />
                                        </span>
                                    </div>
                                </BLink>
                            </div>
                            <div class="p-2">
                                <BForm class="form-horizontal" @submit.prevent="tryToResetpwd">
                                    <div class="mb-3">
                                        <label for="useremail">Email</label>
                                        <BFormInput type="email" v-model="localEmail" class="form-control" id="useremail"
                                            placeholder="Enter email" disabled />

                                    </div>

                                    <div class="mb-3">
                                        <label for="username">Mot de passe</label>
                                        <BFormInput type="password" v-model="password" class="form-control" id="username"
                                            placeholder="Enter password" :class="{
                                                'is-invalid': submitted && v$.password.$error,
                                            }" />
                                        <div v-if="submitted && v$.password.$error" class="invalid-feedback">
                                            <span v-if="v$.password.required.$message">{{
                                                v$.password.required.$message
                                                }}</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="userpassword">Confirmation de mot de passe</label>
                                        <BFormInput type="password" v-model="password_confirmation" class="form-control"
                                            id="userpassword" placeholder="Enter password" :class="{
                                                'is-invalid':
                                                    submitted && v$.password_confirmation.$error,
                                            }" />
                                        <div v-if="submitted && v$.password_confirmation.$error" class="invalid-feedback">
                                            <span v-if="v$.password_confirmation.required.$message">{{
                                                v$.password_confirmation.required.$message
                                            }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-4 d-grid">
                                        <BButton variant="primary" type="submit">
                                            Register
                                        </BButton>
                                    </div>

                                </BForm>
                            </div>
                        </BCardBody>
                    </BCard>
                </BCol>
            </BRow>
        </BContainer>
    </div>
</template>
