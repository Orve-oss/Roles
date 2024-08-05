<script>
import axios from "axios";
import Swal from "sweetalert2";

import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";


/**
 * Add-product component
 */
export default {

    components: {
        Layout,
        PageHeader
    },
    data() {
        return {
            comment: {
                contenu: '',
                ticket_id: null
            },
            ticket: {
                sujet: '',
                description: '',
                status: '',
                image: '',
                service: { nom_service: '' },
                priorite: { niveau: '' },
                type: { libelle: '' },
                created_at: '',
            },
            showImageModal: false,
            workDescription: '',
            showWorkModal: false,
        };
    },
    mounted() {
        this.fetchTicket();
        const id = this.$route.params.id;
        this.comment.ticket_id = id;
        console.log(this.comment.ticket_id);
    },
    methods: {
        getImageUrl(imagePath) {
            return `http://127.0.0.1:8000/storage/${imagePath}`;
        },
        showWorkDescription(){
            this.showWorkModal = true;
        },

        fetchTicket() {
            const id = this.$route.params.id;
            console.log(id);
            axios.get(`http://127.0.0.1:8000/api/tickets/${id}`)
                .then((res) => {
                    console.log(res.data);
                    if (Array.isArray(res.data) && res.data.length > 0) {
                        this.ticket = res.data[0]; // Accède au premier élément du tableau
                        console.log('Données du ticket:', this.ticket); // Affiche les données du ticket après l'assignation
                    }
                    console.log(this.ticket)
                })
                .catch((err) => {
                    return err;
                });

        },

        async updateTicketStatus() {
            const id = this.$route.params.id;
            console.log(id);
            await axios.put(`http://127.0.0.1:8000/api/tickets/update-status/${id}`, { status: this.ticket.status })
                .then(
                    alert('Statut mis à jour avec succès'),
                )
                .catch(error => {
                    console.error('Erreur survenue', error);
                });
            await axios.post(`http://127.0.0.1:8000/api/tickets/send-email`, {
                ticket_id: this.ticket.id,
                work_description: this.workDescription
            });
            this.showWorkModal = false;
            Swal.fire('Succes!', 'Un mail a été envoyé au client', 'success');
        },
        sendEmail() {
            const id = this.$route.params.id;
            Swal.fire({
                title: 'Voulez vous envoyer un mail d\'indisponibilité',

                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Oui envoyer!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`http://127.0.0.1:8000/api/tickets/send-email/${id}`)
                        .then(() => {
                            Swal.fire(
                                'Supprimé!',
                                'Le mail a été envoyé',
                                'success'
                            );


                        }).catch((error) => {
                            if (error.response) {
                                Swal.fire(
                                    'Erreur!',
                                    'Erreur lors de la supression du ticket',
                                    'error'

                                );

                            }

                        })

                }



            })
        },
        sendResolutionEmail() {
            const id = this.$route.params.id;
            Swal.fire({
                title: 'Voulez vous envoyer un mail de résolution de ticket',

                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Oui envoyer!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`http://127.0.0.1:8000/api/tickets/send-resolution/${id}`)
                        .then((response) => {
                            Swal.fire(
                                'Envoyé',
                                response.data.message,
                                'success'
                            );


                        }).catch((error) => {
                            if (error.response) {
                                Swal.fire(
                                    'Erreur!',
                                    'Erreur lors de l\'envoi du mail',
                                    'error'

                                );

                            }

                        })

                }



            })
        },
        generateRapport(ticketId) {
            axios.post(`http://127.0.0.1:8000/ticket/${ticketId}/rapport`)
                .then(response => {
                    Swal.fire('Succes', response.data.message, 'success');
                }).catch(error => {
                    console.log('Erreur', error);
                    Swal.fire('Erreur', 'Erreur de génération', 'error');
                });
        },
        addComment() {

            const formData = new FormData();
            formData.append('contenu', this.comment.contenu);
            formData.append('ticket_id', this.comment.ticket_id);
            axios.post(`http://127.0.0.1:8000/api/comments`, formData)
                .then(response => {
                    console.log(response.data);
                }).catch(error => {
                    console.error('Erreur', error);
                });
        }
    }

};
</script>

<template>
    <Layout>
        <PageHeader title="Details du ticket" pageTitle="Tickets" />

        <BRow>
            <BCol cols="12">
                <BCard>
                    <BCard no-body>
                        <BCardTitle>Informations du tickets</BCardTitle>
                        <p class="card-title-desc">Soumis le {{ new Date(ticket.created_at).toLocaleDateString() }}</p>

                        <BForm @submit.prevent="showWorkDescription">
                            <BRow>
                                <BCol sm="6">
                                    <div class="mb-3">
                                        <BFormGroup label="Type">
                                            <BFormInput :value="ticket.type?.libelle || 'N/A'" disabled />
                                        </BFormGroup>
                                    </div>
                                    <div class="mb-3">
                                        <BFormGroup label="Service">
                                            <BFormInput :value="ticket.service?.nom_service || 'NA'" disabled />
                                        </BFormGroup>
                                    </div>
                                    <div class="mb-3">
                                        <BFormGroup label="Sujet">
                                            <BFormInput v-model="ticket.sujet" disabled />
                                        </BFormGroup>
                                    </div>
                                    <div class="mb-3">
                                        <BFormGroup label="Priorite">
                                            <BFormInput :value="ticket.priorite?.niveau || 'N/A'" disabled />
                                        </BFormGroup>
                                    </div>
                                </BCol>
                                <BCol sm="6">
                                    <div class="mb-3">
                                        <BFormGroup label="Statut">
                                            <BFormSelect v-model="ticket.status">
                                                <BFormSelectOption value="En cours">En cours</BFormSelectOption>
                                                <BFormSelectOption value="Résolu">Résolu</BFormSelectOption>
                                                <BFormSelectOption value="Fermé"> Fermé</BFormSelectOption>
                                            </BFormSelect>
                                        </BFormGroup>
                                    </div>
                                    <div class="mb-3">
                                        <BFormGroup label="Description">
                                            <BFormTextarea id="productdesc" v-model="ticket.description"
                                                class="form-control" placeholder="Product Description" rows="5"
                                                aria-disabled="true"></BFormTextarea>
                                        </BFormGroup>
                                    </div>
                                    <div v-if="ticket.image" class="mb-3">
                                        <BFormGroup label="Image">
                                            <BCardImg :src="getImageUrl(ticket.image)" @click="showImageModal = true"
                                                style="cursor: pointer; max-width: 50%;"></BCardImg>
                                        </BFormGroup>
                                    </div>
                                </BCol>
                            </BRow>

                            <div class="mt-2">
                                <BButton variant="primary" type="submit" class="me-1">
                                    Save Changes
                                </BButton>
                                <BButton variant="secondary" class="me-1">Cancel</BButton>
                                <BButton variant="success" class="me-1" @click="sendEmail"> Envoyer un mail</BButton>

                            </div>
                            <div class="mt-2" v-if="ticket.status === 'Résolu'">
                                <BButton variant="light" @click="sendResolutionEmail" class="me-2"> Mail</BButton>
                                <BButton variant="primary">Rapport</BButton>
                            </div>
                        </BForm>
                    </BCard>
                </BCard>
            </BCol>
        </BRow>
        <BModal v-model="showImageModal" title="image" hide-footer>
            <img :src="getImageUrl(ticket.image)" class="img-fluid" />
        </BModal>
        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <BCardBody>
                        <div class="pt-3">
                            <BRow class="justify-content-center">
                                <BCol xl="8">
                                    <div>
                                        <BForm @submit.prevent="addComment">
                                            <div class="mt-3">
                                                <div class="mt-5">
                                                    <h5 class="font-size-15">
                                                        <i class="bx bx-message-dots text-muted align-middle me-1"></i>
                                                        Commentaires :
                                                    </h5>


                                                </div>
                                                <div class="mt-4">
                                                    <h5 class="font-size-16 mb-3">Mettre une note de travail</h5>

                                                    <BForm>

                                                        <div class="mb-3">
                                                            <label for="commentmessage-input">Message</label>
                                                            <BFormTextarea class="form-control"
                                                                v-model="comment.contenu" id="commentmessage-input"
                                                                placeholder="Mettez une note de travail" rows="3">
                                                            </BFormTextarea>
                                                        </div>

                                                        <div class="text-end">
                                                            <BButton variant="success" type="submit" class="w-sm">
                                                                Submit
                                                            </BButton>
                                                        </div>
                                                    </BForm>
                                                </div>
                                            </div>
                                        </BForm>

                                    </div>
                                </BCol>
                            </BRow>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <BModal v-model="showWorkModal" hide-header :no-close-on-backdrop="true" @ok="updateTicketStatus">
            <BAlert :model-value="true" variant="success" class="text-center mb-4" >
                Ajouter une description du travail en cours
            </BAlert>
            <BFormTextarea v-model="workDescription" placeholder="Entrez la description du travail en cours" rows="5"></BFormTextarea>
        </BModal>
    </Layout>
</template>
