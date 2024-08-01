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
            userId: null,
            agents: [],
            showModal: false,
            currentTicketId: null
        };
    },
    mounted() {
        this.fetchTickets();
        this.fetchAgents();
    },
    methods: {
        viewTicket(id) {
            this.$router.push({ name: 'TicketDetail', params: { id } });
        },

        fetchTickets(status) {
            let url = `http://127.0.0.1:8000/api/tickets`;
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
        async fetchAgents() {
            await axios.get('http://127.0.0.1:8000/api/users')
                .then(response => {
                    console.log(response.data);
                    this.agents = response.data.filter(user => user.roles[0].name === 'Agent');
                    console.log(this.agents);
                })
                .catch(error => {
                    console.error('Error fetching agents:', error);
                });

        },
        openAssignModal(ticketId) {
            this.currentTicketId = ticketId;
            this.showModal = true;
        },
        async assignTicket() {

            const ticketId = this.currentTicketId;
            console.log(ticketId);
            console.log(this.userId);
            if (!this.userId) {
                Swal.fire(
                    'Erreur',
                    'Veuillez selectionner un agent',
                    'error'
                );
                return;
            }
            await axios.post(`http://127.0.0.1:8000/api/tickets/${ticketId}/assign`, {
                user_id: this.userId
            })
                .then(response => {
                    this.showModal= false;
                    Swal.fire(
                        'Assigné',
                        response.data.message,
                        'success'
                    );
                }).catch(error => {
                    console.error('Erreur d\'assignation de ticket :', error);
                    Swal.fire(
                        'Erreur',
                        'Une erreur est survenue',
                        'error'
                    );
                });
        }
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
                                    <BButton @click="fetchTickets('')" variant="secondary"
                                        class="btn-rounded mb-2 me-2">
                                        Tout
                                    </BButton>
                                    <BButton @click="fetchTickets('En attente')" class="btn-rounded mb-2 me-2"
                                        variant="dark">
                                        <i class="bx bx-hourglass bx-spin font-size-16 align-middle"></i>
                                        En attente
                                    </BButton>
                                    <BButton @click="fetchTickets('En cours')" variant="primary"
                                        class="btn-rounded mb-2 me-2">
                                        En cours
                                    </BButton>
                                    <BButton @click="fetchTickets('Résolu')" variant="success"
                                        class="btn-rounded mb-2 me-2">
                                        Résolu
                                    </BButton>
                                    <BButton @click="fetchTickets('Fermé')" variant="danger"
                                        class="btn-rounded mb-2 me-2">
                                        Fermé
                                    </BButton>
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
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Edit
                                                </BDropdownItem>

                                                <BDropdownItem>
                                                    <i class="fas fa-trash-alt text-danger me-1"></i>
                                                    Delete
                                                </BDropdownItem>
                                                <BDropdownItem @click="openAssignModal(ticket.id)">
                                                    <i class="fas fa-user-alt text-primary me-1"></i>
                                                    Assigner à
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
    <BModal id="assignModal" v-model="showModal" ref="assignModal" title="Assigner un ticket à">
        <BForm @submit.prevent="assignTicket">
            <BFormGroup label="Choisir un agent" label-for="agent-select">
                <BFormSelect v-model="userId" id="agent-select">
                    <!-- <BFormSelectOption :value="null">Select</BFormSelectOption> -->
                    <BFormSelectOption v-for="agent in agents" :key="agent.id" :value="agent.id">
                       Nom : {{ agent.name }} Email: {{ agent.email }}
                    </BFormSelectOption>
                </BFormSelect>
            </BFormGroup>
            <div style="margin-top: 10px;">
                <BFormGroup>
                    <BButton variant="primary" type="submit">Assigner</BButton>
                </BFormGroup>
            </div>
        </BForm>
    </BModal>
</template>
