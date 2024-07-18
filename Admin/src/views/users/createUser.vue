<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import axios from "axios";

/**
 * Dashboard Component
 */
export default {
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
        createUser() {
            axios.post('http://127.0.0.1:8000/api/users', this.user)
                .then(response => {
                    this.errors = null;
                    this.successMessage = response.data.message;
                    this.user = {
                        name: '',
                        email: '',
                        password: '',
                        role: '',
                    };
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
</script>

<template>
    <Layout>
        <PageHeader title="Ajouter des utilisateurs" pageTitle="Utilisateurs" />

        <BRow class="justify-content-center">
            <BCol lg="7">
                <BCard no-body>
                    <BCardBody>
                        <BCardTitle class="mb-4">Ajouter un utilisateur</BCardTitle>

                        <BForm>
                            <BFormGroup class="mb-3" label="Nom" label-for="horizontal-firstname-input"
                                label-cols-sm="3">
                                <BFormInput id="horizontal-firstname-input" v-model="user.name" type="text"
                                    placeholder="Enter the name">
                                </BFormInput>
                            </BFormGroup>

                            <BFormGroup class="mb-4" label="Email" label-for="horizontal-email-input" label-cols-sm="3">
                                <BFormInput id="horizontal-email-input" v-model="user.email" type="email"
                                    placeholder="Enter the email">
                                </BFormInput>
                            </BFormGroup>

                            <BFormGroup class="mb-4" label="Mot de passe" label-for="horizontal-password-input"
                                label-cols-sm="3">
                                <BFormInput id="horizontal-password-input" v-model="user.password" type="password"
                                    placeholder="Enter the password"></BFormInput>
                            </BFormGroup>
                            <BFormGroup class="mb-4" label="Role" label-for="horizontal-role-input" label-cols-sm="3">
                                <BFormSelect v-model="user.role" class="form-select" aria-placeholder="Selectionner">
                                    <BFormSelectOption :value="null">Select</BFormSelectOption>
                                    <BFormSelectOption v-for="role in roles" :key="role.id" value="role.name">{{
                                        role.name }}</BFormSelectOption>
                                </BFormSelect>

                            </BFormGroup>

                            <BRow class="justify-content-end">
                                <BCol sm="9">

                                    <div>
                                        <BButton type="submit" variant="primary">Submit</BButton>
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
