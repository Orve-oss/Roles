<script>
import axios from "axios";
import Swal from "sweetalert2";
import Layout from "../../layouts/main";//besoin
import PageHeader from "@/components/page-header";
import emitter from "../../eventBus";


/**
 * Customers component
 */
export default {
    components: { Layout, PageHeader },

    data() {
        return {
            searchQuery: '',
            filteredTickets: [],
            tickets: [],
            currentPage: 1,
            totalPages: 1,
            userId: null,
            types: [],
            services: [],
            priorites: [],
            agents: [],
            showModal: false,
            currentTicketId: null,
            currentUser: null,
            chooseAgent: null,
            selectedAgent: null,
            showEditModal: false,
            editTicket: {
                id: null,
                sujet: '',
                description: '',
                status: '',
                service: { nom_service: '' },
                priorite: { niveau: '' },
                type: { libelle: '' },
                created_at: '',
                image: null
            },


        };
    },
    created(){
        this.fetchTickets();
    },

    mounted() {
        this.fetchTickets();
        emitter.on('ticket-status', this.fetchTickets);
        this.fetchTypes();
        this.fetchServices();
        this.fetchPriorites();
        this.fetchAgents();
        this.currentUser = this.getUserId();
        console.log(this.currentUser);
    },
    beforeUnmount(){
        emitter.off('ticket-status', this.fetchTickets);
    },
    methods: {
        filterTickets(){
            const query = this.searchQuery.toLowerCase();
            this.filteredTickets = this.tickets.filter(ticket => {
                return (
                    ticket.sujet.toLowerCase().includes(query) ||
                    ticket.status.toLowerCase().includes(query) ||
                    ticket.priorite.niveau.toLowerCase().includes(query)
                );
            });
        },
        fetchTypes() {
            axios.get('http://127.0.0.1:8000/api/types')
                .then(response => {
                    this.types = response.data;
                })
                .catch(error => {
                    console.error('Error fetching roles:', error);
                });
        },
        fetchServices() {
            axios.get('http://127.0.0.1:8000/api/services')
                .then(response => {
                    this.services = response.data;
                })
                .catch(error => {
                    console.error('Error fetching services:', error);
                });
        },
        fetchPriorites() {
            axios.get('http://127.0.0.1:8000/api/priorites')
                .then(response => {
                    this.priorites = response.data;
                })
                .catch(error => {
                    console.error('Error fetching priorites:', error);
                });
        },
        viewTicket(id) {
            this.$router.push({ name: 'voirTicket', params: { id } });
        },
        any() {
            Swal.fire('Information', 'Vous n\'avez pas la possibilité de modidier ou de supprimer ce ticket', 'info');
        },

        fetchTickets(status, page = 1) {

            let url = `http://127.0.0.1:8000/api/tickets?page=${page}&perPage=5&sortBy=created_at&sortOrder=asc`;
            if (status) {
                url = `http://127.0.0.1:8000/api/tickets/status/${status}?page=${page}&perPage=5&sortBy=created_at&sortOrder=asc`;
            }
            axios.get(url, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('authToken')}`
                }
            })
                .then(response => {
                    this.tickets = response.data.data;
                    console.log(this.tickets);
                    this.filteredTickets = [...this.tickets];
                    this.filterTickets();
                    this.totalPages = response.data.last_page;
                    this.currentPage = response.data.current_page;
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des tickets:', error);
                });
        },
        async fetchAgents() {
            await axios.get('http://127.0.0.1:8000/api/users', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('authToken')}`
                }
            })
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
        getUserId() {
            const user = JSON.parse(localStorage.getItem('user'));
            console.log('user from localstorage:', user);
            console.log('id', user.id);
            return user ? user.id : null;

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
                user_id: this.userId, assigned_by: this.currentUser
            })
                .then(response => {
                    this.fetchTickets();
                    this.showModal = false;
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
        },
        selectAgent(agentId) {
            this.userId = agentId;
            this.selectedAgent = agentId;
        },
        editticket(ticket) {
            this.editTicket = { ...ticket };
            this.showEditModal = true;
        },
        async updateTicket() {
            const formData = new FormData();
            formData.append('sujet', this.editTicket.sujet);
            formData.append('description', this.editTicket.description);
            formData.append('service_id', this.editTicket.service);
            formData.append('type_ticket_id', this.editTicket.type);
            formData.append('priorite_id', this.editTicket.priorite);
            if (this.editTicket.image) {
                formData.append('sujet', this.editTicket.sujet);
            }
            await axios.post(`http://127.0.0.1:8000/api/updateticket/${this.editTicket.id}`, formData)
                .then(response => {

                    console.log(response);
                    this.fetchTickets();
                    Swal.fire(
                        'Mis à jour!',
                        'ticket mis à jour',
                        'success'
                    );

                })
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
                                        <input type="text" class="form-control" placeholder="Recherche" v-model="searchQuery" @input="filterTickets"/>
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
                                    <BTr v-for="(ticket, index) in filteredTickets" :key="index">
                                        <BTd> {{ index + 1 }} </BTd>
                                        <BTd> {{ ticket.sujet || 'Sujet indisponible' }} </BTd>
                                        <BTd> {{ ticket?.status || 'N/A' }} </BTd>
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
                                                <BDropdownItem
                                                    v-if="['En cours', 'Résolu', 'Fermé', 'Assigné'].includes(ticket.status)"
                                                    @click="any">
                                                    Aucun
                                                </BDropdownItem>

                                                <BDropdownItem
                                                    v-if="!['En cours', 'Résolu', 'Fermé', 'Assigné'].includes(ticket.status)"
                                                    @click="editticket(ticket)">
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Edit
                                                </BDropdownItem>

                                                <BDropdownItem
                                                    v-if="!['En cours', 'Résolu', 'Fermé', 'Assigné'].includes(ticket.status)">
                                                    <i class="fas fa-trash-alt text-danger me-1"></i>
                                                    Delete
                                                </BDropdownItem>
                                                <BDropdownItem @click="openAssignModal(ticket.id)"
                                                    v-if="!['En cours', 'Résolu', 'Assigné'].includes(ticket.status)">
                                                    <i class="fas fa-user-alt text-primary me-1"></i>
                                                    Assigner à
                                                </BDropdownItem>
                                            </BDropdown>
                                        </BTd>
                                    </BTr>
                                </BTbody>
                            </BTableSimple>
                        </div>
                        <BPagination  v-model="currentPage" :total-rows="totalPages * 5" :per-page="5" @update:modelValue="fetchTickets(status, currentPage)"  />
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
    <BModal id="modal-scrollable" v-model="showModal" ref="modal-scrollable" title="Assigner un ticket à">
        <BForm @submit.prevent="assignTicket">
            <table class="table agent-table" @click="selectAgent">

                <tbody>
                    <tr v-for="(agent, index) in agents" :key="agent.id"
                        :class="[{ 'selected-agent': selectedAgent === agent.id }, index % 2 === 0]"
                        >
                        <td>
                            <input type="checkbox" :checked="selectedAgent === agent.id"
                                @change="selectAgent(agent.id)" />
                        </td>
                        <td>
                            <div class="agent-info">
                                <span class="agent-name">{{ agent.name }}</span>
                                <span class="agent-email">{{ agent.email }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-3">
                <BFormGroup>
                    <BButton variant="primary" type="submit" :disabled="!selectedAgent">Assigner</BButton>
                </BFormGroup>
            </div>
        </BForm>
    </BModal>
    <BModal v-model="showEditModal" title="Modifier le ticket" title-class="font-18" body-class="p-3" hide-footer
        hide-header class="v-modal-custom">
        <BForm @submit.prevent="updateTicket">
            <BRow>
                <BCol cols="12">
                    <div class="mb-3">

                        <BFormGroup label="Type de ticket">
                            <BFormSelect v-model="editTicket.type.id" class="form-select">
                                <BFormSelectOption v-for="type in types" :key="type.id" :value="type.id">{{
                                    type.libelle }}</BFormSelectOption>
                            </BFormSelect>
                        </BFormGroup>

                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <BFormGroup label="Service">
                            <BFormSelect v-model="editTicket.service.id" class="form-select">
                                <BFormSelectOption v-for="service in services" :key="service.id" :value="service.id">{{
                                    service.nom_service }}</BFormSelectOption>
                            </BFormSelect>
                        </BFormGroup>
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="sujet">Sujet </label>
                        <input id="sujet" :value="editTicket.sujet" type="text" class="form-control" />
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="description">Description</label>
                        <BFormTextarea id="productdesc" :value="editTicket.description" class="form-control"
                            placeholder="Product Description">
                        </BFormTextarea>
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <BFormGroup label="Priorite">
                            <BFormSelect v-model="editTicket.priorite.id" class="form-select">
                                <BFormSelectOption v-for="priorite in priorites" :key="priorite.id"
                                    :value="priorite.id">{{
                                        priorite.niveau }}</BFormSelectOption>
                            </BFormSelect>
                        </BFormGroup>
                    </div>
                </BCol>

            </BRow>
            <div class="text-end pt-2 mt-1">
                <BButton variant="light" @click="showEditModal = false">Fermer</BButton>
                <BButton type="submit" variant="success" class="ms-1">Modifier le ticket</BButton>
            </div>
        </BForm>
    </BModal>
    <!-- <BModal id="assignModal" v-model="showModal" ref="assignModal" title="Assigner un ticket à">
        <BForm @submit.prevent="assignTicket">
            <BFormGroup label="Choisir un agent" label-for="agents">
                <BFormCheckboxGroup v-model="selectedAgents" id="agents">
                    <BFormCheckbox v-for="agent in agents" :key="agent.id" :value="agent.id">
                        {{ agent.name }} - {{ agent.email }}
                    </BFormCheckbox>
                </BFormCheckboxGroup>
            </BFormGroup>
            <div style="margin-top: 10px;">
                <BFormGroup>
                    <BButton variant="primary" type="submit">Assigner</BButton>
                </BFormGroup>
            </div>
        </BForm>
    </BModal> -->
</template>
<style>
.agent-table {
    width: 100%;
    border-collapse: collapse;
}

.agent-table thead th {
    text-align: left;
}

.agent-table tbody tr {
    cursor: pointer;
}

.agent-table tbody tr.selected {
    background-color: #E9ECEF;
    color: white;
}

.agent-info {
    display: flex;
    flex-direction: column;
}

.agent-name {
    font-size: 1.2em;
    font-weight: bold;
}

.agent-email {
    font-size: 0.9em;
    color: #555;
    margin-top: 5px;
}

.table-light {
    background-color: #fff;
}

.selected-agent {
    background-color: #007bff;
}
</style>
