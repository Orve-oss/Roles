<script>
import axios from "axios";
import { required, email, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core"



/**
 * Recover password Sample page
 */
export default {
    setup() {


        return {
            v$: useVuelidate(),

        };

    },
    data() {
        return {

            email: '',
            password: '',
            password_confirmation: '',
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
            // sameAsPassword: helpers.withMessage(
            //     "Le mot de passe ne correspond pas",
            //     sameAs(() => this.password)
            // ),
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
                    .post(`http://127.0.0.1:8000/api/reset-password`, {
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                    })
                    .then((res) => {
                        if (res.data.status === 200) {
                            alert('Compte activé avec succès!');
                            this.$router.push('/');
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
                <BCol md="8" lg="6" xl="5">
                    <BCard no-body class="overflow-hidden">
                        <BCardBody class="pt-0">
                            <div class="p-4">
                                <BForm class="form-horizontal" @submit.prevent="tryToResetpwd">
                                    <BFormGroup>
                                        <label for="useremail">Email</label>
                                        <BFormInput class="mb-2" v-model="email" id="useremail"
                                            placeholder="Enter email"
                                            :class="{ 'is-invalid': submitted && v$.email.$error }"
                                            aria-disabled="true" />
                                        <div v-for="(item, index) in v$.email.$errors" :key="index"
                                            class="invalid-feedback">
                                            <span v-if="item.$message">{{ item.$message }}</span>
                                        </div>
                                    </BFormGroup>

                                    <BFormGroup>
                                        <label for="pwd">Password</label>
                                        <BFormInput class="mb-2" type="password" v-model="password" id="pwd"
                                            placeholder="Enter password" :class="{
                                                'is-invalid': submitted && v$.password.$error,
                                            }" />
                                        <div v-if="submitted && v$.password.$error" class="invalid-feedback">
                                            <span v-if="v$.password.required.$message">{{
                                                v$.password.required.$message
                                                }}</span>
                                        </div>
                                    </BFormGroup>

                                    <BFormGroup>
                                        <label for="confirm_pwd">Confirm Password</label>
                                        <BFormInput class="mb-2" v-model="password_confirmation" type="password"
                                            id="confirm_pwd" placeholder="Enter confirm password" :class="{
                                                'is-invalid':
                                                    submitted && v$.password_confirmation.$error,
                                            }" />
                                        <div v-if="submitted && v$.password_confirmation.$error"
                                            class="invalid-feedback">
                                            <span v-if="v$.password_confirmation.required.$message">{{
                                                v$.password_confirmation.required.$message
                                                }}</span>
                                            <!-- <span v-if="v$.password_confirmation.sameAsPassword.$message">
                                                {{ v$.password_confirmation.sameAsPassword.$message }}
                                            </span> -->
                                        </div>
                                    </BFormGroup>

                                    <div class="form-group row mb-0">
                                        <BCol cols="12" class="text-end">
                                            <BButton variant="primary" class="w-md" type="submit">
                                                Activez
                                            </BButton>
                                        </BCol>
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
