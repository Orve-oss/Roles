<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import axios from 'axios';
import Swal from 'sweetalert2';
// import BPagination from 'bootstrap-vue-next';
import { required, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import { useNotificationStore } from '@/state/pinia';
const notificationStore = useNotificationStore();


/**
 * Customers component
 */
export default {
    components: { Layout, PageHeader },
    setup() {
        return {
            v$: useVuelidate(),
        };
    },
    name: 'Client list',
    data() {
        return {
            currentPage: 1,
            totalPages: 1,
            clients: [],
            searchClient: '',
            filteredClients: [],

            showModal: false,
            newClient: {
                nom_clt: '',
                email: '',
            },
            showCreateModal: false,
            showEditModal: false,
            editClient: {
                id: null,
                nom_clt: '',
                email: '',
            },
            errors: {},
            submitted: false,
            errorMessage: '',
            showAlert: false

        };
    },
    validations() {
        return {
            newClient: {
                nom_clt: {
                    required: helpers.withMessage("le nom est requis", required),
                },
                email: {
                    required: helpers.withMessage("l'email est requis", required),
                    // email: helpers.withMessage("Please enter valid email", email),
                },

            }
        };

    },
    computed: {
        notification() {
            return notificationStore || {};
        },
    },
    mounted() {
        this.fetchClients();
    },
    methods: {
        filterClient() {
            const query = this.searchClient.toLowerCase();
            this.filteredClients = this.clients.filter(client => {
                return (
                    client.nom_clt.toLowerCase().includes(query) ||
                    client.email.toLowerCase().includes(query)

                );
            });
        },
        async fetchClients(page = 1) {
            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/clients?page=${page}&perPage=5&sortBy=created_at&sortOrder=asc`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('authToken')}`
                    }
                });
                this.clients = response.data.data;
                this.filteredClients = [...this.clients];
                this.filterClient();
                this.totalPages = response.data.last_page;
                this.currentPage = response.data.current_page;


            } catch (error) {
                console.error('Erreur lors de la récupération des clients:', error);
            }
        },

        async createClient() {
            this.submitted = true;
            this.v$.$touch();
            if (this.v$.$invalid) {
                return;
            } else {
                this.errors = {};
                axios.post('http://127.0.0.1:8000/api/clients', this.newClient, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('authToken')}`
                    }
                })
                    .then((response) => {
                        this.fetchClients();
                        Swal.fire(
                            'Client crée',
                            'Un mail sera envoyé pour l\'activation du compte de ce client',
                            'success'
                        );
                        this.showCreateModal = false;
                        this.newClient = { name: '', email: '' };
                        console.log('Client created successfully:', response.data);
                    })
                    .catch((error) => {
                        if (error.response && error.response.data.email) {
                            this.errorMessage = error.response.data.email[0];
                            this.showAlert = true;
                            setTimeout(() => {
                                this.showAlert = false
                            }, 2000);
                        }
                    })

            }



        },
        editclient(client) {
            this.editClient = { ...client };
            this.showEditModal = true;
        },
        updateClient() {
            axios.put(`http://127.0.0.1:8000/api/updateclt/${this.editClient.id}`, this.editClient, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('authToken')}`
                }
            })
                .then(response => {
                    console.log(response);
                    this.fetchClients();
                    this.showEditModal = false;
                    this.editClient = {
                        id: null,
                        nom_clt: '',
                        email: ''
                    };
                    Swal.fire(
                        'Mise à jour',
                        'Le client a été mis à jour',
                        'success'
                    );
                })
            // .catch(error => {
            //     if(error.response){
            //         this.errors = error.response.data.errors;
            //     }
            //     console.log(response);
            // })
        },

        deleteClient(id) {
            Swal.fire({
                title: 'Etes vous sûr de vouloir supprimer ce client?',
                text: 'Cette action est irreversible',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`http://127.0.0.1:8000/api/deleteclt/${id}`, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('authToken')}`
                        }
                    })
                        .then((response) => {
                            if (response.status === 200) {
                                this.fetchClients();
                                Swal.fire(
                                    'Supprimé!',
                                    'Le client a été supprimé',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Erreur!',
                                    response.data.message,
                                    'error'

                                );
                            }

                        }).catch((error) => {
                            if (error.response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Le client a été supprimé'

                                );

                            }

                        })

                }

            });

        },
        generateResetLink(id) {
            Swal.fire({
                title: 'Etes vous sûr de vouloir générer un lien de réinitialisation?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'oui!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`http://127.0.0.1:8000/api/clients/${id}/generate-reset`)
                        .then((response) => {
                            if (response) {

                                Swal.fire(
                                    'Succes!',
                                    'lien généré',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Erreur!',
                                    'erreur de génération',
                                    'error'

                                );
                            }

                        }).catch((error) => {
                            if (error.response) {
                                Swal.fire(
                                    'Erreur!',
                                    'Erreur',
                                    'error'

                                );

                            }

                        })

                }

            });

        }


    },

};
</script>

<template>
    <Layout>
        <PageHeader title="Liste des clients" pageTitle="Clients" />

        <BRow>
            <BCol cols="12">
                <BCard no-body>
                    <BCardBody>
                        <BRow class="mb-2">
                            <BCol sm="4">
                                <div class="search-box me-2 mb-2 d-inline-block">
                                    <div class="position-relative">
                                        <input type="text" class="form-control" placeholder="Recherche"
                                            v-model="searchClient" @input="filterClient" />
                                        <i class="bx bx-search-alt search-icon"></i>
                                    </div>
                                </div>
                            </BCol>
                            <BCol sm="8">
                                <div class="text-sm-end">
                                    <BButton variant="success" class="btn-rounded mb-2 me-2"
                                        @click="showModal = !showModal">
                                        <i class="mdi mdi-plus me-1"></i>Nouveau client
                                    </BButton>
                                </div>
                            </BCol>
                        </BRow>
                        <div class="table-responsive">
                            <BTableSimple class="table-centered table-nowrap align-middle">
                                <BThead>
                                    <BTr>
                                        <BTh>Index</BTh>
                                        <BTh>Nom du client</BTh>
                                        <BTh>Email du client</BTh>
                                        <BTh>Compte</BTh>
                                        <BTh>Date de creation</BTh>

                                        <BTh>Action</BTh>
                                    </BTr>
                                </BThead>
                                <BTbody>
                                    <BTr v-for="(clist, index) in filteredClients" :key="index">
                                        <BTd>{{ index + 1 }}</BTd>
                                        <BTd>{{ clist.nom_clt }}</BTd>
                                        <BTd>
                                            <p class="mb-0">{{ clist.email }}</p>
                                        </BTd>
                                        <BTd>
                                            <div class="mt-2 text-center">
                                                <span v-if="clist.account_locked_at" class="text-danger">
                                                    <i class="fas fa-circle"></i>Bloqué
                                                    <BButton variant="link" size="sm"
                                                        @click="generateResetLink(clist.id)">
                                                        <i class="fas fa-link"></i>
                                                    </BButton>
                                                </span>
                                                <span v-else class="text-success">
                                                    <i class="fas fa-circle"></i> Actif
                                                </span>
                                            </div>
                                        </BTd>
                                        <BTd>{{ new Date(clist.created_at).toLocaleDateString() }}</BTd>
                                        <!-- <BTd>
                                            <BButton variant="primary" class="btn-sm btn-rounded"
                                                @click="showModal = !showModal">
                                                Voir
                                            </BButton>
                                        </BTd> -->
                                        <BTd>
                                            <BDropdown class="card-drop" variant="white" right toggle-class="p-0"
                                                menu-class="dropdown-menu-end">
                                                <template v-slot:button-content>
                                                    <i class="mdi mdi-dots-horizontal font-size-18"></i>
                                                </template>

                                                <BDropdownItem @click="editclient(clist)">
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Edit
                                                </BDropdownItem>

                                                <BDropdownItem @click="deleteClient(clist.id)">
                                                    <i class="fas fa-trash-alt text-danger me-1"></i>
                                                    Delete
                                                </BDropdownItem>
                                            </BDropdown>
                                        </BTd>

                                    </BTr>
                                </BTbody>
                            </BTableSimple>
                        </div>
                        <BPagination v-model="currentPage" :total-rows="totalPages * 5" :per-page="5"
                            @update:modelValue="fetchClients(currentPage)" />
                        <!-- <div class="mt-3">
                            <BPagination v-model="currentPage" :total-rows="totalClients" :per-page="perPage"
                                align="end" @change="handlePageChange" />


                        </div> -->
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
    <BModal v-model="showModal" title="Ajouter un nouveau client" title-class="font-18" body-class="p-3" hide-footer
        class="v-modal-custom">
        <BForm @submit.prevent="createClient">
            <BAlert v-model="showAlert" variant="danger" dismissible @dismissed="showAlert = false">{{ errorMessage }}
            </BAlert>
            <BRow>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="name">Nom du client</label>
                        <BFormInput id="name" v-model="newClient.nom_clt" type="text" class="form-control"
                            placeholder="Insert username" :class="{
                                'is-invalid': submitted && v$.newClient.nom_clt.$error,
                            }" />
                        <div v-for="(item, index) in v$.newClient.nom_clt.$errors" :key="index"
                            class="invalid-feedback">
                            <span v-if="item.$message">{{ item.$message }}</span>
                        </div>
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <BFormInput id="email" v-model="newClient.email" type="email" class="form-control"
                            placeholder="Insert email" :class="{
                                'is-invalid': submitted && v$.newClient.email.$error,
                            }" />
                        <div v-for="(item, index) in v$.newClient.email.$errors" :key="index" class="invalid-feedback">
                            <span v-if="item.$message">{{ item.$message }}</span>
                        </div>
                    </div>
                </BCol>
            </BRow>
            <div class="text-end pt-5 mt-3">
                <BButton variant="light" @click="showModal = false">Fermer</BButton>
                <BButton type="submit" variant="success" class="ms-1">Creer le client</BButton>
            </div>
        </BForm>
    </BModal>
    <BModal v-model="showEditModal" title="Modifier un client" title-class="font-18" body-class="p-3" hide-footer
        class="v-modal-custom">
        <BForm @submit.prevent="updateClient">
            <BRow>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="name">Nom du client</label>
                        <input id="name" v-model="editClient.nom_clt" type="text" class="form-control" />
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input id="email" v-model="editClient.email" type="email" class="form-control" />
                    </div>
                </BCol>
            </BRow>
            <div class="text-end pt-5 mt-3">
                <BButton variant="light" @click="showEditModal = false">Fermer</BButton>
                <BButton type="submit" variant="success" class="ms-1">Modifier le client</BButton>
            </div>
        </BForm>
    </BModal>

</template>
