<script>
import Layout from "../../layouts/main";//besoin
import PageHeader from "@/components/page-header";
import axios from 'axios';


/**
 * Customers component
 */
export default {
    components: { Layout, PageHeader },
    name: 'Client list',
    props:{
        client: {
            type: Object,
            default: null
        }
    },

    data() {
        return {
            clients: [],
            showModal: false,

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
        // async deleteClient(id) {
        //     if (confirm("Etes vous sur de vouloir supprimer ce client??")) {
        //         try {
        //             await axios.delete('http://127.0.0.1:8000/api/delete/${id}');
        //             this.fetchClients();

        //         } catch (error) {
        //             console.log('Erreur lors de la suppression du client:', error);
        //         }
        //     }
        // }
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
                                            <p class="mb-0">{{ clist.email_clt }}</p>
                                        </BTd>
                                        <!-- <BTd>
                                            <span class="badge bg-success font-size-12">
                                                <i class="mdi mdi-receipt me-1"></i>
                                                0
                                            </span>
                                        </BTd> -->
                                        <BTd>{{ clist.created_at }}</BTd>
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

                                                <BDropdownItem>
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Edit
                                                </BDropdownItem>

                                                <BDropdownItem>
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
        <BForm>
            <BRow>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="name">Nom du client</label>
                        <input id="name" type="text" class="form-control" placeholder="Insert username" />
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control" placeholder="Insert email">
                    </div>
                </BCol>
            </BRow>
            <div class="text-end pt-5 mt-3">
                <BButton variant="light" @click="showModal = false">Fermer</BButton>
                <BButton type="submit" variant="success" class="ms-1">Creer le client</BButton>
            </div>
        </BForm>
    </BModal>
</template>
