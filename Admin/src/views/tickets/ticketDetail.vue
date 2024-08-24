<script>
import axios from "axios";
import Swal from "sweetalert2";

import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
// import emitter from "../../eventBus";


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
            comments: [],
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
            showNoteModal: false,
            showComment: false,
            workDescription: '',
            showWorkModal: false,
            showbutton: false,
            ticketId: null,
            reportGenerate: false,
            isStatusDisabled: false,

        };
    },
    created(){
        this.disableStatus();
    },

    mounted() {
        this.fetchTicket();
        this.disableStatus();
        const id = this.$route.params.id;
        this.comment.ticket_id = id;
        console.log(this.comment.ticket_id);
        this.ticketId = id;
        console.log(this.ticketId);
    },
    methods: {
        isImage(file) {
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            const fileExtension = file.split('.').pop().toLowerCase();
            return imageExtensions.includes(fileExtension);
        },
        isPdf(file) {
            const pdfExtension = 'pdf';
            const fileExtension = file.split('.').pop().toLowerCase();
            return fileExtension === pdfExtension;
        },
        getImageUrl(imagePath) {
            const fullUrl = `http://127.0.0.1:8000/storage/${imagePath}`;
            console.log(fullUrl); // Ajoutez ceci pour voir l'URL générée
            return fullUrl;
        },
        showWorkDescription() {
            this.showWorkModal = true;
            this.showNoteModal = true;
        },
        disableStatus(){
            if (this.ticket.status === 'Fermé') {
                this.isStatusDisabled = true;
                console.log(this.isStatusDisabled);

            }
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
                .then(() => {
                    //alert('Statut mis à jour avec succès');
                    // emitter.emit('ticket-status');
                    // if (this.ticket.status === 'Résolu') {
                    //     this.showNoteModal = true;
                    // }
                    this.$router.push('/agent/tickets');
                })
                .catch(error => {
                    console.error('Erreur survenue', error);
                });
            await axios.post(`http://127.0.0.1:8000/api/tickets/send-email`, {
                ticket_id: this.ticket.id,
                work_description: this.workDescription
            });
            this.showWorkModal = false;
            this.showNoteModal = false;

            Swal.fire('Succes!', 'Un mail a été envoyé au client', 'success');
        },
        async updateTicketResolved() {
            const id = this.$route.params.id;
            console.log(id);

            Swal.fire({
                title: 'Voulez vous fermer ce ticket?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Oui, fermer'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.put(`http://127.0.0.1:8000/api/tickets/${id}/close`, { status: this.ticket.status })
                        .then(() => {

                            this.generateRapport();
                            this.isStatusDisabled = true;
                            console.log(this.isStatusDisabled);
                            Swal.fire('Succes!', 'Un mail est envoyé au client avec son rapport de résolution', 'success');
                            this.$router.push({ name: 'opentickets' });

                        })

                } else {
                    Swal.fire(
                        'Erreur!',
                        'Erreur de génération de rapport',
                        'error'
                    );
                }
            }).catch((error) => {
                if (error.response) {
                    Swal.fire(
                        'Erreur',
                        'erreur',
                        'error'
                    );
                }
            })

            // await axios.put(`http://127.0.0.1:8000/api/tickets/${id}/close`, { status: this.ticket.status })
            //     .then(() => {
            //         // alert('Statut mis à jour avec succès');
            //         if (this.ticket.status === 'Fermé') {
            //             this.generateRapport();
            //             Swal.fire('Succes!', 'Un mail est envoyé au client avec son rapport de résolution', 'success');
            //             this.$router.push({ name: 'opentickets' });
            //         }
            //     })
            //     .catch(error => {
            //         console.error('Erreur survenue', error);
            //     });
            // await axios.post(`http://127.0.0.1:8000/api/tickets/send-email`, {
            //     ticket_id: this.ticket.id,
            //     work_description: this.workDescription
            // });
            // this.showNoteModal = false;
            // console.log('Email envoyé');

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
        generateRapport() {
            axios.post(`http://127.0.0.1:8000/api/ticket/${this.ticketId}/rapport`)
                .then(response => {
                    console.log(response.data.message);

                }).catch(error => {
                    console.error('Erreur', error);
                })

        },
        addComment() {
            const id = this.$route.params.id;
            this.comment.ticket_id = id;
            console.log(this.comment.ticket_id);

            const formData = new FormData();
            formData.append('contenu', this.comment.contenu);
            formData.append('ticket_id', this.comment.ticket_id);
            axios.post(`http://127.0.0.1:8000/api/commentaires`, formData)
                .then(response => {
                    this.fetchcomments();
                    this.showbutton = true;
                    console.log(response.data);
                }).catch(error => {
                    console.error('Erreur', error);
                });
        },
        fetchcomments() {

            axios.get(`http://127.0.0.1:8000/api/ticket/${this.ticketId}/comments`)
                .then(response => {
                    this.comments = response.data;
                })
                .catch(error => {
                    console.error('Erreur', error);
                });
        },
        archiveTicket(id) {
            Swal.fire({
                title: 'Êtes-vous sûr de vouloir fermer ce ticket?',
                text: 'Cette action va archiver le ticket.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Oui, fermer!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`http://127.0.0.1:8000/api/deleteticket/${id}`
                        // headers: {
                        //     'Authorization': `Bearer ${localStorage.getItem('authToken')}`
                        // }
                    )
                        .then((response) => {
                            if (response) {
                                this.fetchTickets(); // Actualisez la liste des tickets
                                this.$router.push({ name: 'opentickets' });
                                Swal.fire(
                                    'Fermé!',
                                    'Le ticket a été archivé avec succès.',
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
                            Swal.fire(
                                'Erreur!',
                                error,
                                'error'
                            );
                        });
                }
            });
        }


    },

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
                                        <!-- <BFormGroup label="Statut" v-if="ticket.status === 'Résolu'">
                                            <BFormInput v-model="ticket.status" disabled />
                                        </BFormGroup> -->
                                        <BFormGroup label="Statut">
                                            <BFormSelect v-model="ticket.status" :disabled="isStatusDisabled">
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
                                                disabled></BFormTextarea>
                                        </BFormGroup>
                                    </div>
                                    <div v-if="ticket.image" class="mb-3">
                                        <BFormGroup label="Fichier">
                                            <template v-if="isImage(ticket.image)">
                                                <BCardImg :src="getImageUrl(ticket.image)"
                                                    @click="showImageModal = true"
                                                    style="cursor: pointer; max-width: 50%;"></BCardImg>
                                            </template>
                                            <template v-else-if="isPdf(ticket.image)">
                                                <embed :src="getImageUrl(ticket.image)" type="application/pdf"
                                                    width="100%" height="300px" />
                                                <BButton variant="primary" :href="getImageUrl(ticket.image)" download>
                                                    Télécharger le PDF</BButton>
                                            </template>
                                        </BFormGroup>
                                    </div>
                                </BCol>
                            </BRow>

                            <div class="mt-2">
                                <BButton variant="primary" type="submit" class="me-1">
                                    Save Changes
                                </BButton>
                                <BButton variant="danger"  class="me-1">Fermer
                                </BButton>
                                <!-- <BButton variant="success" class="me-1" v-if="showbutton" @click="generateRapport">
                                    Rapport
                                </BButton> -->

                            </div>
                            <!-- <div class="mt-2" v-if="ticket.status === 'Résolu'">
                        <BButton variant="light" @click="sendResolutionEmail" class="me-2"> Mail</BButton>
                        <BButton variant="primary">Rapport</BButton>
                     </div> -->
                        </BForm>
                    </BCard>
                </BCard>
            </BCol>
        </BRow>
        <BModal v-model="showImageModal" title="image" hide-footer>
            <img :src="getImageUrl(ticket.image)" class="img-fluid" />
        </BModal>
        <!-- <BRow v-if="showComment">
            <BCol lg="12">
                <BCard no-body>
                    <BCardBody>
                        <div class="pt-3">
                            <BRow class="justify-content-center">
                                <BCol xl="8">
                                    <div>

                                        <BForm>
                                            <div class="mt-3">
                                                <div class="d-flex py-3" v-for="comment in comments" :key="comment.id">

                                                    <div class="flex-grow-1">
                                                        <h5 class="font-size-14 mb-1">
                                                            Agent
                                                            <small class="text-muted float-end">{{ new
                                                                Date(comment.created_at).toLocaleTimeString() }}</small>
                                                        </h5>
                                                        <p class="text-muted">
                                                            {{ comment.contenu }}
                                                        </p>

                                                    </div>
                                                </div>
                                                <div class="mt-5">
                                                    <h5 class="font-size-15">
                                                        <i class="bx bx-message-dots text-muted align-middle me-1"></i>
                                                        Commentaires :
                                                    </h5>
                                                </div>
                                                <div class="mt-4">
                                                    <h5 class="font-size-16 mb-3">Mettre une note de travail</h5>

                                                    <BForm @submit.prevent="addComment">

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
        </BRow> -->
        <BModal v-model="showWorkModal" v-if="['En cours', 'Résolu'].includes(ticket.status)" hide-header :no-close-on-backdrop="true"
            @ok="updateTicketStatus">
            <BAlert :model-value="true" variant="success" class="text-center mb-4">
                Ajouter une description du travail en cours
            </BAlert>
            <BFormTextarea v-model="workDescription" placeholder="Entrez la description du travail en cours" rows="5">
            </BFormTextarea>
        </BModal>
        <BModal v-model="showNoteModal" v-else hide-header :no-close-on-backdrop="true" @ok="updateTicketResolved">
            <BAlert :model-value="true" variant="success" class="text-center mb-4">
                Ajouter une note de fermeture
            </BAlert>
            <BFormTextarea v-model="workDescription" placeholder="Entrez la note de travail" rows="5">
            </BFormTextarea>
        </BModal>
    </Layout>
</template>
