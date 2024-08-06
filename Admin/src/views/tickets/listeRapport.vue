<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import axios from "axios";
import Rapport from "./Rapport.vue";

/** * Invoice-list component */
export default {
    components: { Layout, PageHeader, },
    data() {
        return {
            Rapport,
            historiques:[]

        };
    },
    mounted(){
        this.fetchHistory();
    },
    methods:{
        fetchHistory(){
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

        <BRow>
            <BCol xl="4" sm="6">
                <BCard no-body>
                    <BCardBody>
                        <BRow>

                            <BCol lg="8" v-for="histor in historiques" :key="histor.id">
                                <div>
                                    <router-link :to="Rapport" class="d-block text-primary text-decoration-underline mb-2">Ticket N°{{histor.ticket_id}}</router-link>
                                    <h5 class="text-truncate mb-4 mb-lg-5" title="Rapport généré">{{ histor.description }}</h5>
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item me-3">
                                            <h5 class="font-size-14" data-toggle="tooltip" data-placement="top"
                                                title="Amount">
                                                <i class="bx bx-money me-1 text-muted"></i>vmagnouwai@gmail.com
                                            </h5>
                                        </li>
                                        <li class="list-inline-item ms-1">
                                            <h5 class="font-size-14" data-toggle="tooltip" data-placement="top"
                                                title="Date de création">
                                                <i class="bx bx-calendar me-1 text-muted"></i>{{new Date(histor.created_at).toLocaleDateString()}}
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
    </Layout>
</template>
