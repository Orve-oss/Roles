<script>
import axios from "axios";
// import Swal from "sweetalert2";
import Layout from "../../layouts/main";//besoin
import PageHeader from "@/components/page-header";


/**
 * Customers component
 */
export default {
    components: { Layout, PageHeader },

    data() {
        return {
            tickets: [],
            clientId: null,
            showModal: false,

        };
    },
    mounted() {
        this.fetchTickets();

    },
    methods: {
        viewTicket(id) {
            this.$router.push({ name: 'ShowTicket', params: { id } });
        },
        getClientId(){
            const user = JSON.parse(localStorage.getItem('user'));
            return user ? user.id : null;
        },

        fetchTickets(status) {
            this.clientId = this.getClientId();
            if (!this.clientId) {
                console.error('Agent Id is not defined');
            }

            let url = `http://127.0.0.1:8000/api/tickets/client/${this.clientId}`;
            if (status) {
                url = `http://127.0.0.1:8000/api/tickets/status/${status}`;
            }
            axios.get(url)
                .then(response => {
                    this.tickets = response.data;
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des tickets:', error);
                });
        },
    }
}

</script>

<template>
    <Layout>
        <PageHeader title="Liste des tickets" pageTitle="All ticket" />

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
                                <div class="clearfix mt-4 mt-lg-0">
                                    <BDropdown class="float-end" right variant="primary" menu-class="dropdown-menu-end">
                                        <template v-slot:button-content>
                                            <i class="bx bx-filter align-middle me-1"></i> Filtrer
                                        </template>
                                        <BDropdownItem @click="fetchTickets('')">Tout</BDropdownItem>
                                        <BDropdownItem @click="fetchTickets('En attente')">En attente</BDropdownItem>
                                        <BDropdownItem @click="fetchTickets('En cours')">En cours</BDropdownItem>
                                        <BDropdownItem @click="fetchTickets('Résolu')">Résolu</BDropdownItem>
                                        <BDropdownItem @click="fetchTickets('Fermé')">Fermé</BDropdownItem>
                                    </BDropdown>
                                </div>
                            </BCol>
                        </BRow>
                        <div class="table-responsive" v-if="tickets.length">
                            <BTableSimple class="table-centered table-nowrap align-middle">
                                <BThead>
                                    <BTr>
                                        <BTh>Index</BTh>
                                        <BTh>Sujet</BTh>
                                        <BTh>Statut</BTh>
                                        <BTh>Priorite</BTh>
                                        <BTh>Date</BTh>
                                        <BTh>Detail</BTh>
                                        <BTh>Action</BTh>
                                    </BTr>
                                </BThead>
                                <BTbody>
                                    <BTr v-for="(ticket, index) in tickets" :key="index">
                                        <BTd> {{ index + 1 }} </BTd>
                                        <BTd> {{ ticket.sujet }} </BTd>
                                        <BTd> {{ ticket.status }} </BTd>
                                        <!-- <BTd> {{ ticket.type?.libelle || 'N/A' }} </BTd> -->
                                        <BTd> {{ ticket.priorite?.niveau || 'N/A' }} </BTd>
                                        <BTd> {{ new Date(ticket.created_at).toLocaleDateString() }} </BTd>
                                        <BTd>
                                            <BButton variant="primary" class="btn-sm btn-rounded"
                                                @click="viewTicket(ticket.id)">
                                                Voir
                                            </BButton>
                                        </BTd>
                                        <BTd>
                                            <BDropdown class="card-drop" variant="white" right toggle-class="p-0"
                                                menu-class="dropdown-menu-end">
                                                <template v-slot:button-content>
                                                    <i class="mdi mdi-dots-horizontal font-size-18"></i>
                                                </template>

                                                <BDropdownItem v-if="!['En cours', 'Résolu', 'Fermé'].includes(ticket.status)">
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Edit
                                                </BDropdownItem>

                                                <BDropdownItem v-if="!['En cours', 'Résolu', 'Fermé'].includes(ticket.status)">
                                                    <i class="fas fa-trash-alt text-danger me-1"></i>
                                                    Delete
                                                </BDropdownItem>

                                            </BDropdown>
                                        </BTd>
                                    </BTr>
                                </BTbody>
                            </BTableSimple>
                        </div>
                        <div colspan="6" v-else align="center"> Aucun ticket soumis</div>
                        <pagination />
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>

</template>
