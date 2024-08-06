<script>
import axios from "axios";
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";

/** * Invoice-detail component */
export default {

    components: { Layout, PageHeader },
    data() {
        return {
            rapport: []
        };
    },
    mounted(){
        this.fetchRapport();
    },
    methods: {
        fetchRapport() {
            const ticketId = this.$route.params.ticketId;
            axios.get(`http://127.0.0.1:8000/api/ticket/${ticketId}/rapport`)
                .then(response => {
                    this.rapport = response.data;
                }).catch(error => {
                    console.error('Erreur', error);
                });
        }
    }
};
</script>

<template>
    <Layout>
        <PageHeader title="Detail du rapport" pageTitle="tickets" />

        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <BCardBody>
                        <div class="invoice-title">
                            <h4 class="float-end font-size-16">Ticket N°{{ rapport.ticket_id }}</h4>
                            <div class="mb-4">
                                <img src="@/assets/images/logo.jpg" alt="logo" height="20" />
                            </div>
                        </div>
                        <hr />
                        <BRow>
                            <BCol cols="6" class="mt-3">
                                <address>
                                    <strong>Au client: </strong>
                                    {{ rapport.client }}
                                </address>
                            </BCol>
                            <BCol cols="6" class="mt-3 text-sm-end">
                                <address>
                                    <strong>Résolu le:</strong>
                                    <br />{{ new Date(rapport.updated_at).toLocaleDateString() }}
                                    <br />
                                    <br />
                                </address>
                            </BCol>
                        </BRow>
                        <div class="p-2 mt-3">
                            <h3 class="font-size-16">Rapport du ticket</h3>
                        </div>
                        <div class="table-responsive">
                            <BTableSimple>
                                <BThead>
                                    <BTr>
                                        <BTh style="width: 100px; margin-right: 50px; ">Index</BTh>
                                        <BTh style="padding-left: 300px;">Libelle</BTh>
                                    </BTr>
                                </BThead>
                                <BTbody>


                                    <BTr>
                                        <BTd>Statut : </BTd>
                                        <BTd style="padding-left: 300px;">{{ rapport.statut }}</BTd>
                                    </BTr>

                                    <BTr>
                                        <BTd>Description : </BTd>
                                        <BTd style="padding-left: 300px;">{{ rapport.description }}</BTd>
                                    </BTr>
                                    <BTr>
                                        <BTd>Priorité : </BTd>
                                        <BTd style="padding-left: 300px;">{{ rapport.priorite }}</BTd>
                                    </BTr>


                                    <BTr>
                                        <BTd>Type: </BTd>
                                        <BTd style="padding-left: 300px;">{{ rapport.type }}</BTd>
                                    </BTr>
                                    <BTr>
                                        <BTd>Service: </BTd>
                                        <BTd style="padding-left: 300px;">{{ rapport.service }}</BTd>
                                    </BTr>
                                    <BTr>
                                        <BTd>Attribué à: </BTd>
                                        <BTd style="padding-left: 300px;">{{ rapport.agent }}</BTd>
                                    </BTr>
                                    <BTr>
                                        <BTd>Ouvert le :</BTd>
                                        <BTd style="padding-left: 300px;">{{ new Date(rapport.created_at).toLocaleDateString() }}</BTd>
                                    </BTr>
                                    <BTr>
                                        <BTd>Fermé le :</BTd>
                                        <BTd style="padding-left: 300px;">{{ new Date(rapport.updated_at).toLocaleDateString() }}</BTd>
                                    </BTr>
                                    <!-- <BTr>
                                            <BTd>Commentaire de travail:</BTd>
                                            <BTd>Veltrix - Bootstrap 4 Admin Template</BTd>
                                        </BTr> -->

                                </BTbody>
                            </BTableSimple>
                        </div>
                        <div class="d-print-none">
                            <div class="float-end">
                                <BLink href="javascript:window.print()"
                                    class="btn btn-success waves-effect waves-light me-1"><i class="fa fa-print"></i>
                                </BLink>
                                <BLink href="#" class="btn btn-primary w-md waves-effect waves-light ms-1">Send</BLink>
                            </div>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
