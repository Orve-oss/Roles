<script>
import axios from "axios";
import { required, email, helpers, } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core"
import Swal from "sweetalert2";



/**
 * Recover password Sample page
 */
export default {
    props: ['email', 'token'],
    setup() {
        return {
            v$: useVuelidate(),
        };
    },


    data() {
        return {
            localEmail: this.email,
            password: '',
            password_confirmation: '',
            passwordVisible: false,
            passwordConfirmationVisible: false,
            errorMessage: '',
            submitted: false,


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
        password_confirmation: {
            required: helpers.withMessage("Confirm password is required", required),


        },
    },


    methods: {
        togglePasswordVisibility() {
            this.passwordVisible = !this.passwordVisible;
        },
        togglePasswordConfirmationVisibility() {
            this.passwordConfirmationVisible = !this.passwordConfirmationVisible;
        },

        tryToResetpwd() {
            this.submitted = true;
            // stop here if form is invalid
            this.v$.$touch();

            if (this.v$.$invalid) {
                return;
            } else {
                axios
                    .post(`http://127.0.0.1:8000/api/resetPasswordUser`, {
                        email: this.localEmail,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                        token: this.token,
                    })
                    .then((res) => {
                        if (res.data.message) {
                            alert('Mot de passe  changé!');
                            const userRole = localStorage.getItem('userRole');
                            if (userRole === 'Agent') {
                                this.$router.push('/connexionAgent');

                            } else {
                                this.$router.push('/connexionAdmin');
                            }

                        } else {
                            this.errorMessage = 'Erreur';
                            Swal.fire('Erreur', 'Erreur de modification de mot de passe', 'error');
                        }
                    });
            }
        },
    },

};
</script>

<template>
    <div class="account-pages my-5 pt-5">
        <BContainer>

            <BRow class="justify-content-center">
                <BCol md="12" lg="8" xl="8">
                    <BCard no-body class="overflow-hidden">
                        <BRow no-gutters>
                            <BCol md="4" class="bg-light p-4 d-flex flex-column justify-content-center">
                                <div class="text-center">
                                    <h4> Hello</h4>
                                    <p class="mb-0"> Merci de changer de mot de passe</p>
                                </div>
                            </BCol>
                            <BCol md="8">
                                <BCardBody class="pt-0">

                                    <div class="p-2">
                                        <BAlert :model-value="true" variant="success" class="text-center mb-4">
                                            Veuillez changer de mot de passe
                                        </BAlert>
                                        <BAlert v-if="errorMessage" variant="danger" class="text-center mb-4">
                                            {{ errorMessage }}
                                        </BAlert>
                                        <BForm class="form-horizontal" @submit.prevent="tryToResetpwd">
                                            <BFormGroup>
                                                <label for="useremail">Email</label>
                                                <BFormInput class="mb-2" v-model="localEmail" id="useremail"
                                                    placeholder="Enter email" disabled
                                                    :class="{ 'is-invalid': submitted && v$.email.$error }" />
                                                <div v-for="(item, index) in v$.email.$errors" :key="index"
                                                    class="invalid-feedback">
                                                    <span v-if="item.$message">{{ item.$message }}</span>
                                                </div>
                                            </BFormGroup>

                                            <BFormGroup>
                                                <label for="pwd">Password</label>
                                                <div class="input-group">
                                                    <BFormInput class="mb-2" :type="passwordVisible ? 'text' : 'password'"
                                                                v-model="password" id="pwd" placeholder="Enter password"
                                                                :class="{ 'is-invalid': submitted && v$.password.$error }" />
                                                    <BButton variant="secondary" @click="togglePasswordVisibility">
                                                        <i :class="passwordVisible ? 'mdi mdi-eye-off' : 'mdi mdi-eye'"></i>
                                                    </BButton>
                                                </div>
                                                <div v-if="submitted && v$.password.$error" class="invalid-feedback">
                                                    <span v-if="v$.password.required.$message">{{
                                                        v$.password.required.$message
                                                    }}</span>
                                                </div>
                                            </BFormGroup>

                                            <BFormGroup>
                                                <label for="confirm_pwd">Confirm Password</label>
                                                <div class="input-group">
                                                    <BFormInput class="mb-1" :type="passwordConfirmationVisible ? 'text' : 'password'"
                                                                v-model="password_confirmation" id="confirm_pwd" placeholder="Enter confirm password"
                                                                :class="{ 'is-invalid': submitted && v$.password_confirmation.$error }" />
                                                    <BButton variant="secondary" @click="togglePasswordConfirmationVisibility">
                                                        <i :class="passwordConfirmationVisible ? 'mdi mdi-eye-off' : 'mdi mdi-eye'"></i>
                                                    </BButton>
                                                </div>
                                                <div v-if="submitted && v$.password_confirmation.$error"
                                                    class="invalid-feedback">
                                                    <span v-if="v$.password_confirmation.required.$message">{{
                                                        v$.password_confirmation.required.$message
                                                    }}</span>
                                                </div>
                                            </BFormGroup>

                                            <div class="form-group row mb-0">
                                                <BCol cols="12" class="text-end">
                                                    <BButton variant="primary" class="w-md" type="submit">
                                                        Reset
                                                    </BButton>
                                                </BCol>
                                            </div>

                                        </BForm>
                                    </div>

                                </BCardBody>
                            </BCol>
                        </BRow>



                    </BCard>

                </BCol>
            </BRow>
        </BContainer>
    </div>


</template>
