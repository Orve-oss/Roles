<script>
import axios from "axios";
import Swal from "sweetalert2";

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
            agentId: null
        };
    },
    mounted() {
        this.agentId = this.getAgentId();
        console.log(this.agentId);
        this.fetchAgentTickets();
    },
    methods: {
        viewTicket(id) {
            this.$router.push({ name: 'TicketDetail', params: { id } });
        },
        getAgentId() {
            const user = JSON.parse(localStorage.getItem('user'));
            return user ? user.id : null;
        },

        fetchAgentTickets(status) {
            this.agentId = this.getAgentId();

            if (!this.agentId) {
                console.error('Agent Id is not defined');
            }
            let url= `http://127.0.0.1:8000/api/tickets/agent/${this.agentId}`;
            if (status) {
                url= `http://127.0.0.1:8000/api/tickets/agent/${this.agentId}/status/${status}`;
            }
            axios.get(url)
                .then(response => {
                    console.log(response.data);
                    this.tickets = response.data;
                    console.log(this.tickets);
                })
                .catch(error => {
                    console.error('Error fetching tickets:', error);
                });

        },
        any() {
            Swal.fire('Information', 'Vous n\'avez pas la possibilité de modidier ou de supprimer ce ticket', 'info');
        },
        getStatusColor(status) {
            switch (status) {
                case 'En attente':
                    return 'yellow';
                case 'Assigné':
                    return 'gray';
                case 'En cours':
                    return 'blue';
                case 'Résolu':
                    return 'green';
                case 'Fermé':
                    return 'red';
                default:
                    return 'transparent'; // Si le statut est inconnu
            }
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
                                <div class="text-sm-end">
                                    <BDropdown>

                                        <template v-slot:button-content>

                                           Filtrer les tickets

                                            <i class="mdi mdi-chevron-down"></i>
                                        </template>
                                        <BDropdownItem @click="fetchAgentTickets('')">Tout</BDropdownItem>
                                        <BDropdownItem @click="fetchAgentTickets('En cours')">En cours</BDropdownItem>
                                        <BDropdownItem @click="fetchAgentTickets('Résolu')">Résolu</BDropdownItem>
                                    </BDropdown>
                                </div>
                            </BCol>
                        </BRow>
                        <div class="table-responsive">
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
                                        <BTd> {{ ticket.status }} </BTd><BTd>
                                            <span :style="{
                                                'background-color': getStatusColor(ticket?.status),
                                                'border-radius': '50%',
                                                display: 'inline-block',
                                                width: '10px',
                                                height: '10px',
                                                marginRight: '8px',
                                            }"></span>
                                            {{ ticket?.status || 'N/A' }}
                                        </BTd><!-- <BTd> {{ ticket.type?.libelle || 'N/A' }} </BTd> -->
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
                                                <BDropdownItem
                                                    v-if="['En cours', 'Résolu', 'Assigné', 'En attente'].includes(ticket.status)"
                                                    @click="any">
                                                    Aucun
                                                </BDropdownItem>
                                                <BDropdownItem v-if="['Fermé'].includes(ticket.status)">
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
</template>
