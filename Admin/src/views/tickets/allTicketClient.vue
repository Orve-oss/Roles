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
            deletedTickets: [],
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
            types: [],
            services: [],
            selectedTicket: null,
            showTicketModal: false,
            showFeedbackModal: false,
            description: '',
            ticket: null,



        };
    },
    mounted() {
        this.fetchTickets();
        emitter.on('ticket-status', this.fetchTickets);
        this.fetchTypes();
        this.fetchServices();


    },
    beforeUnmount() {
        emitter.off('ticket-status', this.fetchTickets);
    },
    methods: {
        // viewTicket(id) {
        //     this.$router.push({ name: 'ShowTicket', params: { id } });
        // },
        getClientId() {
            const user = JSON.parse(localStorage.getItem('user'));
            return user ? user.id : null;
        },
        filterTickets() {
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
                    this.filteredTickets = this.tickets.filter(ticket => !this.deletedTickets.includes(ticket.id));
                    this.filterTickets();
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des tickets:', error);
                });
        },
        fetchTypes(serviceId) {
            axios.get(`http://127.0.0.1:8000/api/services/${serviceId}/types`)
                .then(response => {
                    this.types = response.data;
                })
                .catch(error => {
                    console.error('Error fetching types:', error);
                });
        },
        handleServiceChange() {
            this.fetchTypes(this.editTicket.service);
            this.editTicket.type = ''; // Réinitialiser le type sélectionné
        },
        showProblems() {
            const selectedType = this.types.find(type => type.id === this.editTicket.type);
            if (selectedType) {
                Swal.fire({
                    title: `Problèmes pour ${selectedType.libelle}`,
                    text: selectedType.problemes.map(p => p.libelle).join(', '),
                    icon: 'info'
                });
            } else {
                Swal.fire('Info', 'Aucun type sélectionné.', 'info');
            }
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
        handleImageUpload(event) {
            const file = event.target.files[0];
            const fileType = file.type;
            const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            const validFileTypes = ['application/pdf'];
            const maxFileSize = 2 * 1024 * 1024 * 1024;
            if (validImageTypes.includes(fileType) || validFileTypes.includes(fileType)) {
                if (file.size <= maxFileSize) {
                    this.editTicket.image = file;
                }
                else {
                    Swal.fire('Erreur', 'Le fichier dépasse la taille maximale d 2Go.', 'error');
                    event.target.value = '';
                }
            } else {
                Swal.fire('Erreur', 'Seuls les fichiers au format JPEG, PNG, JPG, GIF et PDF sont autorisés', 'error');
                event.target.value = '';
            }
        },
        editticket(ticket) {
            this.editTicket = {
                id: ticket.id,
                sujet: ticket.sujet || '',
                description: ticket.description || '',
                service: ticket.service_id || '',
                type: ticket.type_ticket_id || '',
                priorite: ticket.priorite_id || '',
                image: ticket.image || null,
            };
            // this.editTicket = { ...ticket };
            this.showEditModal = true;
            this.fetchTypes(this.editTicket.service);
        },
        async updateTicket() {
            const formData = new FormData();
            formData.append('sujet', this.editTicket.sujet);
            formData.append('description', this.editTicket.description);
            formData.append('service_id', this.editTicket.service);
            formData.append('type_ticket_id', this.editTicket.type);
            formData.append('priorite_id', this.editTicket.priorite);
            if (this.editTicket.image) {
                formData.append('image', this.editTicket.image); // Corrigé ici
            }
            for (let pair of formData.entries()) {
                console.log(`${pair[0]}: ${pair[1]}`);
            }
            try {
                const response = await axios.put(`http://127.0.0.1:8000/api/updateticket/${this.editTicket.id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                console.log(response);
                this.fetchTickets();
                Swal.fire(
                    'Mis à jour!',
                    'Ticket mis à jour avec succès',
                    'success'
                );
            } catch (error) {
                console.error('Erreur lors de la mise à jour du ticket:', error);
                Swal.fire(
                    'Erreur!',
                    "Une erreur s'est produite lors de la mise à jour du ticket",
                    'error'
                );
            }
        },

        removeTicket(ticketId) {
            // Afficher une boîte de dialogue de confirmation avant de supprimer
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action est irreversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.deletedTickets.push(ticketId);
                    // Utilisez un index pour identifier le ticket dans le tableau
                    const index = this.tickets.findIndex(ticket => ticket.id === ticketId);
                    if (index !== -1) {
                        // Supprimez le ticket du tableau des tickets
                        this.tickets.splice(index, 1);
                        this.filteredTickets = this.filteredTickets.filter(ticket => ticket.id !== ticketId);
                        console.log(`Ticket with ID ${ticketId} removed from the display.`);
                        Swal.fire(
                            'Supprimé!',
                            'Le ticket a été supprimé.',
                            'success'
                        );
                    } else {
                        console.log(`Ticket with ID ${ticketId} not found.`);
                    }
                }
            });
        },
        viewTicket(ticketId) {
            axios.get(`http://127.0.0.1:8000/api/tickets/${ticketId}`)
                .then((res) => {
                    console.log(res.data);
                    if (Array.isArray(res.data) && res.data.length > 0) {
                        this.selectedTicket = res.data[0]; // Accède au premier élément du tableau
                        console.log('Données du ticket:', this.ticket); // Affiche les données du ticket après l'assignation
                    }
                    console.log(this.selectedTicket);
                    this.showTicketModal = true;
                })
                .catch((err) => {
                    return err;
                });
        },
        resetSelectedTicket() {
            this.selectedTicket = null; // Réinitialiser les données du ticket sélectionné lors de la fermeture du modal
        },
        any() {
            Swal.fire('Information', 'Vous n\'avez pas la possibilité de modidier ou de supprimer ce ticket', 'info');
        },
        openFeedbackModal(ticket) {
            this.selectedTicket = ticket;
            this.showFeedbackModal = true;
        },
        sendFeedback() {

            axios.post(`http://127.0.0.1:8000/api/tickets/${this.selectedTicket.id}/feedback`, {
                description: this.description,
            })
                .then(() => {
                    // Fermer le modal
                    this.showFeedbackModal = false;
                    this.description = '';
                    console.log('Feedback envoyé avec succès.');
                })
                .catch(() => {
                    console.error("Une erreur s'est produite lors de l'envoi du feedback.");
                });
        },

        extractFileName(file) {
            if (file instanceof File) {
                return file.name;
            } else {
                // Au cas où le fichier ne serait pas un objet File, on pourrait extraire en fonction du chemin
                return file.split('/').pop();
            }
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
        <PageHeader title="Liste des tickets" pageTitle="Tickets" />

        <BRow>
            <BCol cols="12">
                <BCard no-body>
                    <BCardBody>
                        <BRow class="mb-2">
                            <BCol sm="4">
                                <div class="search-box me-2 mb-2 d-inline-block">
                                    <div class="position-relative">
                                        <input type="text" class="form-control" placeholder="Recherche"
                                            v-model="searchQuery" @input="filterTickets" />
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
                                        <!-- <BTh>Priorite</BTh> -->
                                        <BTh>Date</BTh>
                                        <BTh>Detail</BTh>
                                        <BTh>Action</BTh>
                                    </BTr>
                                </BThead>
                                <BTbody>
                                    <BTr v-for="(ticket, index) in filteredTickets" :key="index">
                                        <BTd> {{ index + 1 }} </BTd>
                                        <BTd> {{ ticket.sujet }} </BTd>
                                        <BTd>
                                            <span :style="{
                                                'background-color': getStatusColor(ticket?.status),
                                                'border-radius': '50%',
                                                display: 'inline-block',
                                                width: '10px',
                                                height: '10px',
                                                marginRight: '8px',
                                            }"></span>
                                            {{ ticket?.status || 'N/A' }}
                                        </BTd>
                                        <!-- <BTd> {{ ticket.type?.libelle || 'N/A' }} </BTd> -->
                                        <!-- <BTd> {{ ticket.priorite?.niveau || 'N/A' }} </BTd> -->
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
                                                    v-if="['En cours', 'Résolu', 'Assigné'].includes(ticket.status)"
                                                    @click="any">
                                                    Aucun
                                                </BDropdownItem>

                                                <BDropdownItem v-if="['Résolu'].includes(ticket.status)"
                                                    @click="openFeedbackModal(ticket)">
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Feedback
                                                </BDropdownItem>

                                                <BDropdownItem
                                                    v-if="!['Assigné', 'En cours', 'Résolu', 'Fermé'].includes(ticket.status)"
                                                    @click="editticket(ticket)">
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Edit
                                                </BDropdownItem>

                                                <BDropdownItem
                                                    v-if="!['Assigné', 'En cours', 'Résolu'].includes(ticket.status)"
                                                    @click="removeTicket(ticket.id)">
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
        hide-header class="v-modal-custom fly-in-top">
        <BForm @submit.prevent="updateTicket">
            <BRow>
                <BCol cols="12">
                    <div class="mb-3">
                        <BFormGroup label="Service">
                            <BFormSelect v-model="editTicket.service" class="form-select" @change="handleServiceChange">
                                <BFormSelectOption v-for="service in services" :key="service.id" :value="service.id">{{
                                    service.nom_service }}</BFormSelectOption>
                            </BFormSelect>
                        </BFormGroup>
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <BCol lg="11">
                            <BFormGroup label="Type de ticket">
                                <BFormSelect v-model="editTicket.type" class="form-select">
                                    <BFormSelectOption v-for="type in types" :key="type.id" :value="type.id">{{
                                        type.libelle }}</BFormSelectOption>
                                </BFormSelect>
                            </BFormGroup>
                        </BCol>
                        <BCol lg="1">
                            <i class="fas fa-info-circle" @click="showProblems"></i>
                        </BCol>

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
                    <BFormGroup class="row mb-4">
                        <div class="d-flex flex-column flex-lg-row">
                            <label for="image" class="col-form-label col-lg-2">Image</label>
                            <BCol lg="5">
                                <input id="image" @change="handleImageUpload" type="file" class="form-control" />
                            </BCol>
                            <BCol lg="5" v-if="editTicket.image">
                                <span class="mt-2 ms-lg-3">{{ extractFileName(editTicket.image) }}</span>
                            </BCol>
                        </div>
                    </BFormGroup>
                </BCol>

            </BRow>
            <div class="text-end pt-2 mt-1">
                <BButton variant="light" @click="showEditModal = false">Fermer</BButton>
                <BButton type="submit" variant="success" class="ms-1">Modifier le ticket</BButton>
            </div>
        </BForm>
    </BModal>
    <BModal v-model="showTicketModal" title="Details du ticket" title-class="font-18" body-class="p-3" hide-footer
        class="v-modal-custom fly-in-top" v-if="selectedTicket" id="view-ticket-modal" @hide="resetSelectedTicket">
        <BForm>
            <BRow>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="sujet">Service </label>
                        <input id="sujet" :value="selectedTicket.service.nom_service" type="text" class="form-control"
                            disabled />
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="sujet">Type de ticket </label>
                        <input id="sujet" :value="selectedTicket.type.libelle" type="text" class="form-control"
                            disabled />
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="sujet">Sujet </label>
                        <input id="sujet" :value="selectedTicket.sujet" type="text" class="form-control" disabled />
                    </div>
                </BCol>
                <BCol cols="12">
                    <div class="mb-3">
                        <label for="description">Description</label>
                        <BFormTextarea id="productdesc" v-model="selectedTicket.description" class="form-control"
                            placeholder="Product Description" disabled>
                        </BFormTextarea>
                    </div>
                </BCol>
                <!-- <BCol cols="12">
                    <div class="mb-3">
                        <label for="sujet">Priorite </label>
                        <input id="sujet" :value="selectedTicket.priorite.niveau" type="text" class="form-control"
                            disabled />
                    </div>
                </BCol> -->

            </BRow>

        </BForm>
    </BModal>
    <BModal v-model="showFeedbackModal" hide-footer hide-header>
        <BAlert :model-value="true" variant="success" class="text-center mb-4">
            Veuillez renseigner votre feedback
        </BAlert>

        <BForm @submit.prevent="sendFeedback">
            <BFormGroup label="Description" label-for="description">
                <BFormTextarea id="description" v-model="description" rows="5" placeholder="Entrez votre feedback ici">
                </BFormTextarea>
            </BFormGroup>
            <div class="d-flex justify-content-end">
                <BButton variant="secondary" @click="showFeedbackModal = false">Annuler</BButton>
                <BButton type="submit" variant="primary" class="ms-2">Envoyer</BButton>
            </div>
        </BForm>
    </BModal>

</template>
<style>
@keyframes flyInFromTop {
    0% {
        transform: translateY(-100%);
        opacity: 0;
    }

    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

.fly-in-top {
    animation: flyInFromTop 0.5s ease-out;
}
</style>
