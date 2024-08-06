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
            ticket: {
                sujet: '',
                description: '',
                status: '',
                service: { nom_service: '' },
                priorite: { niveau: '' },
                type: { libelle: '' },
                created_at: '',
                image: null
            },
            ticketId: this.$route.params.id
        };
    },
    mounted() {
        this.fetchTicket();
    },
    methods: {

        fetchTicket() {

            axios.get(`http://127.0.0.1:8000/api/tickets/${this.ticketId}`)
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
        onfilechange(event) {
            this.ticket.image = event.target.files[0];
        },
        async updateTicket() {
            const formData = new FormData();
            formData.append('sujet', this.ticket.sujet);
            formData.append('description', this.ticket.description);
            formData.append('service_id', this.ticket.service);
            formData.append('type_ticket_id', this.ticket.type);
            formData.append('priorite_id', this.ticket.priorite);
            if (this.ticket.image) {
                formData.append('sujet', this.ticket.sujet);
            }
            await axios.post(`http://127.0.0.1:8000/api/updateticket/${this.ticketId}`, formData)
                .then(response => {

                    console.log(response);
                    Swal.fire(
                        'Mis à jour!',
                        'ticket mis à jour',
                        'success'
                    );

                })
        },
        back(){
            this.$router.push('/listticket');
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


                        <BForm @submit.prevent="updateTicket">
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
                                            <BFormInput :value="ticket.priorite?.niveau || 'N/A'"  disabled/>
                                        </BFormGroup>
                                    </div>
                                </BCol>
                                <BCol sm="6">
                                    <div class="mb-3">
                                        <BFormGroup label="Statut">
                                            <BFormInput v-model="ticket.status" disabled />
                                        </BFormGroup>
                                    </div>
                                    <div class="mb-3">
                                        <BFormGroup label="Description">
                                            <BFormTextarea id="productdesc" v-model="ticket.description"
                                                class="form-control" placeholder="Product Description" rows="5" disabled>
                                            </BFormTextarea>
                                        </BFormGroup>
                                    </div>
                                </BCol>
                            </BRow>

                            <div class="mt-2">
                                <BButton variant="primary" type="submit" class="me-1">
                                    Save Changes
                                </BButton>
                                <BButton variant="danger"
                                        @click="back">
                                        Retour
                                    </BButton>
                            </div>
                        </BForm>
                    </BCard>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
