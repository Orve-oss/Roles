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
            tickets: [],
            searchQuery: '',
            filteredTickets: [],
            clientId: null,
            showModal: false,
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
    mounted() {
        this.fetchTickets();
        emitter.on('ticket-status', this.fetchTickets);
        this.fetchTypes();
        this.fetchServices();
        this.fetchPriorites();

    },
    beforeUnmount(){
        emitter.off('ticket-status', this.fetchTickets);
    },
    methods: {
        viewTicket(id) {
            this.$router.push({ name: 'ShowTicket', params: { id } });
        },
        getClientId(){
            const user = JSON.parse(localStorage.getItem('user'));
            return user ? user.id : null;
        },
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

        fetchTickets(status) {
            this.clientId = this.getClientId();
            if (!this.clientId) {
                console.error('Agent Id is not defined');
            }

            let url = `http://127.0.0.1:8000/api/tickets/client/${this.clientId}`;
            if (status) {
                url = `http://127.0.0.1:8000/api/tickets/client/${this.clientId}/status/${status}`;
            }
            axios.get(url)
                .then(response => {
                    this.tickets = response.data;
                    this.filteredTickets = [...this.tickets];
                    this.filterTickets();
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des tickets:', error);
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
        removeTicket(ticketId) {
            this.tickets = this.tickets.filter(ticket => ticket.id !== ticketId);
        }
    }
}

</script>

<template>
    <Layout>
        <PageHeader title="Liste des tickets" pageTitle="Tickets" />

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
                                    <BTr v-for="(ticket, index) in filteredTickets" :key="index">
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

                                                <BDropdownItem v-if="!['Assigné','En cours', 'Résolu', 'Fermé'].includes(ticket.status)"
                                                @click="editticket(ticket)">
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Edit
                                                </BDropdownItem>

                                                <BDropdownItem v-if="!['Assigné','En cours', 'Résolu'].includes(ticket.status)" @click="removeTicket(ticket.id)">
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

</template>
