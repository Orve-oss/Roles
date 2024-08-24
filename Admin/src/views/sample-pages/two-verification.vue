<script>

import axios from 'axios';
import Swal from 'sweetalert2'



/**
 * Two-step-verification component
 */
export default {
    components: {},
    props: ['email'],

    data() {
        return {

            jsonData: [
                {
                    id: "digit1-input",
                    label: "Digit 1",
                    maxLength: 1,
                    value: ''
                },
                {
                    id: "digit2-input",
                    label: "Digit 2",
                    maxLength: 1,
                    value: ''
                },
                {
                    id: "digit3-input",
                    label: "Digit ",
                    maxLength: 1,
                    value: ''
                },
                {
                    id: "digit4-input",
                    label: "Digit 4",
                    maxLength: 1,
                    value: ''
                },
                {
                    id: "digit5-input",
                    label: "Digit 5",
                    maxLength: 1,
                    value: ''
                },
                {
                    id: "digit6-input",
                    label: "Digit 6",
                    maxLength: 1,
                    value: ''
                },

            ]
        }
    },
    // created(){
    //     console.log(this.localEmail);
    // },
    mounted() {
        console.log(this.email);  // Vérifiez que l'email est correctement récupéré
    },

    methods: {
        verifyCode() {

            const code = this.jsonData.map(input => input.value).join('');
            if (code.length !== 6) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Le code doit contenir 6 chiffres.',
                });
                return;
            }
            axios.post(`http://127.0.0.1:8000/api/verify`, {
                email: this.email,
                code: code
            }).then(response => {
                Swal.fire({
                    icon: 'success', title: 'Vérification réussie', text: response.data.message,
                }).then(() => {
                    this.$router.push({ name: 'ResetPassword User', query: { email: this.email, token: response.data.token } });
                });
            }).catch(error => {
                Swal.fire({
                    icon: 'error', title: 'Erreur de vérification', text: error.response.data.message,
                });
            })
        }
    }
};
</script>

<template>
    <div class="account-pages my-5 pt-sm-5">
        <BContainer>
            <BRow>
                <BCol lg="12">
                    <div class="text-center mb-5 text-muted">
                        <router-link to="/" class="d-block auth-logo">
                            <img src="@/assets/images/logo1.png" alt="" height="20" class="auth-logo-dark mx-auto" />
                            <img src="@/assets/images/logo1.png" alt="" height="20" class="auth-logo-light mx-auto" />
                        </router-link>

                    </div>
                </BCol>
            </BRow>

            <BRow class="justify-content-center">
                <BCol md="8" lg="6" xl="5">
                    <BCard no-body>
                        <BCardBody>
                            <div class="p-2">
                                <div class="text-center">
                                    <div class="avatar-md mx-auto">
                                        <div class="avatar-title rounded-circle bg-light">
                                            <i class="bx bxs-envelope h1 mb-0 text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="p-2 mt-4">
                                        <h4>Code de vérification</h4>
                                        <p class="mb-5">
                                            Veuillez entrer le code de vérification envoyé à
                                            <span class="fw-semibold">{{ email }}</span>
                                        </p>
                                        <BForm @submit.prevent="verifyCode">
                                            <BRow>
                                                <BCol cols="3" v-for="inputData in jsonData" :key="inputData.id">
                                                    <div class="mb-3">
                                                        <label :for="inputData.id" class="visually-hidden">{{
                                                            inputData.label }}</label>
                                                        <input type="text"
                                                            class="form-control form-control-lg text-center two-step"
                                                            :maxLength="inputData.maxLength"
                                                            v-model="inputData.value" :id="inputData.id">
                                                    </div>
                                                </BCol>
                                            </BRow>
                                            <div class="mt-4">
                                                <BButton type="submit" class="btn btn-success w-md">Confirm</BButton>
                                            </div>
                                        </BForm>

                                    </div>
                                </div>
                            </div>
                        </BCardBody>
                    </BCard>
                    <div class="mt-5 text-center">
                        <p>
                            Didn't receive a code ?
                            <Blink href="#" class="fw-medium text-primary"> Resend </Blink>
                        </p>
                    </div>
                </BCol>
            </BRow>
        </BContainer>
    </div>
</template>
