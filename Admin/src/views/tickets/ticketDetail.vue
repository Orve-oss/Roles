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
         },
      };
   },
   mounted() {
      this.fetchTicket();
   },
   methods: {

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

      updateTicketStatus() {
         const id = this.$route.params.id;
         console.log(id);
         axios.put(`http://127.0.0.1:8000/api/tickets/update-status/${id}`, { status: this.ticket.status })
            .then(
               alert('Statut mis à jour avec succès'),
            )
            .catch(error => {
               console.error('Erreur survenue', error);
            });
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
      sendResolutionMail(){
         const id = this.$route.params.id;
         axios.post(`http://127.0.0.1:8000/api/tickets/send-resolution/${id}`)
         .then(()=>{
            alert('Email de résolution envoyé avec succès');
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

                  <BForm @submit.prevent="updateTicketStatus">
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
                                    <BFormSelectOption value="En attente"> En attente </BFormSelectOption>
                                    <BFormSelectOption value="En cours">En cours</BFormSelectOption>
                                    <BFormSelectOption value="Résolu">Résolu</BFormSelectOption>
                                    <BFormSelectOption value="Fermé"> Fermé</BFormSelectOption>
                                 </BFormSelect>
                              </BFormGroup>
                           </div>
                           <div class="mb-3">
                              <BFormGroup label="Description">
                                 <BFormTextarea id="productdesc" v-model="ticket.description" class="form-control"
                                    placeholder="Product Description" rows="5" aria-disabled="true"></BFormTextarea>
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
                        <BButton variant="light" @click="sendResolutionMail"> Mail</BButton>
                     </div>
                  </BForm>
               </BCard>
            </BCard>
         </BCol>
      </BRow>
      <BRow>
         <BCol lg="12">
            <BCard no-body>
               <BCardBody>
                  <div class="pt-3">
                     <BRow class="justify-content-center">
                        <BCol xl="8">
                           <div>

                              <div class="mt-3">
                                 <div class="mt-5">
                                    <h5 class="font-size-15">
                                       <i class="bx bx-message-dots text-muted align-middle me-1"></i>
                                       Commentaires :
                                    </h5>

                                 </div>

                                 <div class="mt-4">
                                    <h5 class="font-size-16 mb-3">Leave a Message</h5>

                                    <BForm>

                                       <div class="mb-3">
                                          <label for="commentmessage-input">Message</label>
                                          <BFormTextarea class="form-control" id="commentmessage-input"
                                             placeholder="Your message..." rows="3"></BFormTextarea>
                                       </div>

                                       <div class="text-end">
                                          <BButton variant="success" type="submit" class="w-sm">
                                             Submit
                                          </BButton>
                                       </div>
                                    </BForm>
                                 </div>
                              </div>
                           </div>
                        </BCol>
                     </BRow>
                  </div>
               </BCardBody>
            </BCard>
         </BCol>
      </BRow>
   </Layout>
</template>
