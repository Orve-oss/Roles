<script>
import Layout from "../../layouts/main";//besoin
import PageHeader from "@/components/page-header";
import axios from 'axios';
import Swal from 'sweetalert2';


/**
 * Customers component
 */
export default {
    components: { Layout, PageHeader },
    name: 'Client list',
    data() {
        return {
            clients: [],
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

        };
    },
    mounted() {
        this.fetchClients();
    },
    methods: {
        async fetchClients() {
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/clients');
                this.clients = response.data;
            } catch (error) {
                console.error('Erreur lors de la récupération des clients:', error);
            }
        },
        async createClient() {

            this.errors = {};
            axios.post('http://127.0.0.1:8000/api/clients', this.newClient)
                .then((response) => {
                    this.fetchClients();
                    this.showCreateModal = false;
                    this.newClient = { name: '', email: '' };
                    console.log('Client created successfully:', response.data);
                })
                .catch((error) => {
                    if (error.response) {
                        this.errors = error.response.data.errors;
                    }
                })


        },
        editclient(client){
            this.editClient = {...client };
            this.showEditModal = true;
        },
        updateClient(){
            axios.put(`http://127.0.0.1:8000/api/updateclt/${this.editClient.id}`, this.editClient)
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
                    axios.delete(`http://127.0.0.1:8000/api/deleteclt/${id}`)
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
                                        <input type="text" class="form-control" placeholder="Recherche" />
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
                                        <!-- <BTh>Nbre ticket soumis</BTh> -->
                                        <BTh>Date de creation</BTh>
                                        <BTh>Detail</BTh>
                                        <BTh>Action</BTh>
                                    </BTr>
                                </BThead>
                                <BTbody>
                                    <BTr v-for="(clist, index) in clients" :key="index">
                                        <BTd>{{ index + 1 }}</BTd>
                                        <BTd>{{ clist.nom_clt }}</BTd>
                                        <BTd>
                                            <p class="mb-0">{{ clist.email }}</p>
                                        </BTd>
                                        <!-- <BTd>
                                            <span class="badge bg-success font-size-12">
                                                <i class="mdi mdi-receipt me-1"></i>
                                                0
                                            </span>
                                        </BTd> -->
                                        <BTd>{{ new Date(clist.created_at).toLocaleDateString() }}</BTd>
                                        <BTd>
                                            <BButton variant="primary" class="btn-sm btn-rounded"
                                                @click="showModal = !showModal">
                                                Voir
                                            </BButton>
                                        </BTd>
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
                        <pagination />
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
    <BModal v-model="showModal" title="Ajouter un nouveau client" title-class="font-18" body-class="p-3" hide-footer
        class="v-modal-custom">
        <BForm @submit.prevent="createClient">
            <BRow>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="name">Nom du client</label>
                        <input id="name" v-model="newClient.nom_clt" type="text" class="form-control"
                            placeholder="Insert username" />
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input id="email" v-model="newClient.email" type="email" class="form-control"
                            placeholder="Insert email">
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
