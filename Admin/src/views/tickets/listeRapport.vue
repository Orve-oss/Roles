<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import axios from "axios";


/** * Invoice-list component */
export default {
    components: { Layout, PageHeader, },
    data() {
        return {

            historiques: []

        };
    },
    mounted() {
        this.fetchHistory();
    },
    methods: {
        fetchHistory() {
            axios.get(`http://127.0.0.1:8000/api/historiques`)
                .then(response => {
                    this.historiques = response.data;
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des historiques:', error);
                });
        }
    }
};
</script>

<template>
    <Layout>
        <PageHeader title="Liste des rapports" pageTitle="Tickets" />

        <BRow v-if="historiques.length">
            <BCol xl="4" sm="6" v-for="histor in historiques" :key="histor.id">
                <BCard no-body>
                    <BCardBody>
                        <BRow>

                            <BCol lg="8">
                                <div>
                                    <router-link :to="{ name: 'Rapport', params: { ticketId: histor.ticket_id } }"
                                        class="d-block text-primary text-decoration-underline mb-2">Ticket
                                        N°{{ histor.ticket_id }}</router-link>
                                    <h5 class="text-truncate mb-4 mb-lg-5" title="Rapport généré">{{ histor.description
                                        }}</h5>
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item me-3">

                                        </li>
                                        <li class="list-inline-item ms-1">
                                            <h5 class="font-size-14" data-toggle="tooltip" data-placement="top"
                                                title="Date de création">
                                                <i class="bx bx-calendar me-1 text-muted"></i>{{ new
                                                Date(histor.created_at).toLocaleDateString()}}
                                            </h5>
                                        </li>
                                    </ul>
                                </div>
                            </BCol>
                        </BRow>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
        <BRow v-else class="justify-content-center align-items-center text-center">
            <BCol>
                <p>Aucun rapport</p>
                <img src="@/assets/images/rapport.png" alt="Aucun rapport" height="200" width="200">
            </BCol>
        </BRow>

    </Layout>
</template>
