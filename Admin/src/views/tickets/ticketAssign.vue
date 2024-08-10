<script>
import axios from "axios";

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
</template>
