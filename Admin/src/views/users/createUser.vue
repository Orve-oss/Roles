<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import { required, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import axios from "axios";
import Swal from "sweetalert2";
import { useNotificationStore } from '@/state/pinia';
const notificationStore = useNotificationStore();
/**
 * Dashboard Component
 */
export default {
    setup() {
        return {
            v$: useVuelidate(),
        };
    },
    components: {
        Layout,
        PageHeader,
    },
    data() {
        return {
            exManualSelected: null,
            user: {
                name: '',
                email: '',
                password: '',
                role: '',
            },
            roles: [],
            errors: null,
            successMessage: null,
        };
    },
    validations() {
        return {
            user: {
                name: {
                    required: helpers.withMessage("le nom est requis", required),
                },
                email: {
                    required: helpers.withMessage("l'email est requis", required),
                    // email: helpers.withMessage("Please enter valid email", email),
                },
                password: {
                    required: helpers.withMessage("le mot de passe est requis", required),
                },
                role: {
                    required: helpers.withMessage("Le role est requis", required),
                }
            }
        };

    },
    computed: {
        notification() {
            return notificationStore || {};
        },
    },
    created() {
        this.fetchRoles();
    },
    methods: {
        fetchRoles() {
            axios.get('http://127.0.0.1:8000/api/roles')
                .then(response => {
                    this.roles = response.data.filter(role => role.name !== 'Client');
                })
                .catch(error => {
                    console.error('Error fetching roles:', error);
                });
        },

        async createUser() {
            this.submitted = true;

            this.v$.$touch();
            if (this.v$.$invalid) {
                return;
            } else {
                await axios.post('http://127.0.0.1:8000/api/users', this.user,{
                    headers: {
                    'Authorization': `Bearer ${localStorage.getItem('authToken')}`
                }}
                )
                    .then(response => {
                        this.successMessage = response.data.message;
                        this.user = {
                            name: '',
                            email: '',
                            password: '',
                            role: '',
                        };
                        this.errors = null;


                        Swal.fire(
                            'Créé!',
                            this.successMessage = response.data.message,
                            'success'
                        );


                    })
                    .catch(error => {
                        this.successMessage = null;
                        if (error.response && error.response.data.errors) {
                            this.errors = error.response.data.errors;
                        } else {
                            console.error('Error creating user:', error);
                            this.errors = ['Une erreur est survenue lors de la création de l\'utilisateur'];
                        }
                    });
            }
        }

    }
}
</script>

<template>
    <Layout>
        <PageHeader title="Ajouter des utilisateurs" pageTitle="Utilisateurs" />

        <BRow class="justify-content-center">
            <BCol lg="7">
                <BCard no-body>
                    <BCardBody>
                        <BCardTitle class="mb-4">Ajouter un utilisateur</BCardTitle>

                        <BForm @submit.prevent="createUser">
                            <BFormGroup class="mb-3" label="Nom" label-for="horizontal-firstname-input"
                                label-cols-sm="3">
                                <BFormInput id="horizontal-firstname-input" v-model="user.name" type="text"
                                    placeholder="Enter the name" :class="{
                                        'is-invalid': submitted && v$.user.name.$error,
                                    }">
                                </BFormInput>
                                <div v-for="(item, index) in v$.user.name.$errors" :key="index"
                                    class="invalid-feedback">
                                    <span v-if="item.$message">{{ item.$message }}</span>
                                </div>

                            </BFormGroup>

                            <BFormGroup class="mb-4" label="Email" label-for="horizontal-email-input" label-cols-sm="3">
                                <BFormInput id="horizontal-email-input" v-model="user.email" type="email"
                                    placeholder="Enter the email" :class="{
                                        'is-invalid': submitted && v$.user.email.$error,
                                    }">
                                </BFormInput>
                                <div v-for="(item, index) in v$.user.email.$errors" :key="index"
                                    class="invalid-feedback">
                                    <span v-if="item.$message">{{ item.$message }}</span>
                                </div>
                            </BFormGroup>

                            <BFormGroup class="mb-4" label="Mot de passe" label-for="horizontal-password-input"
                                label-cols-sm="3">
                                <BFormInput id="horizontal-password-input" v-model="user.password" type="password"
                                    placeholder="Enter the password" :class="{
                                        'is-invalid': submitted && v$.user.password.$error,
                                    }"></BFormInput>
                                <div v-for="(item, index) in v$.user.password.$errors" :key="index"
                                    class="invalid-feedback">
                                    <span v-if="item.$message">{{ item.$message }}</span>
                                </div>
                            </BFormGroup>
                            <BFormGroup class="mb-4" label="Role" label-for="horizontal-role-input" label-cols-sm="3">
                                <BFormSelect v-model="user.role" class="form-select" :class="{
                                    'is-invalid': submitted && v$.user.role.$error,
                                }">
                                    <BFormSelectOption :value="null">Select</BFormSelectOption>
                                    <BFormSelectOption v-for="role in roles" :key="role.id" :value="role.name">{{
                                        role.name }}</BFormSelectOption>
                                </BFormSelect>
                                <div v-for="(item, index) in v$.user.role.$errors" :key="index"
                                    class="invalid-feedback">
                                    <span v-if="item.$message">{{ item.$message }}</span>
                                </div>

                            </BFormGroup>

                            <BRow class="justify-content-end">
                                <BCol sm="9">

                                    <div>
                                        <BButton type="submit" variant="primary">Créer</BButton>
                                    </div>
                                </BCol>
                            </BRow>
                        </BForm>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>

    </Layout>
</template>
