<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import axios from "axios";
import Statistiques from "./statistiques.vue";
// import Visitors from "./visitors";


/**
 * Blog dashboard component
 */
export default {
    data() {
        return {
            numAgents: 0,
            numClients: 0,
            numTickets: 0,
            numTicketsAssigned: 0,
            numTicketsInProgress: 0,
            numTicketsResolved: 0,

        }
    },
    components: {
        Layout,
        PageHeader,
        Statistiques

    },
    mounted() {
        this.fetchStats();
    },
    methods: {
        async fetchStats() {
            try {
                const [agentResponse, clientResponse, ticketsResponse, ticketsAssignedResponse, ticketsInProgressResponse, ticketsResolvedResponse] = await Promise.all([
                    axios.get(`http://127.0.0.1:8000/api/agents`),
                    axios.get(`http://127.0.0.1:8000/api/clients`),
                    axios.get(`http://127.0.0.1:8000/api/tickets`),
                    axios.get(`http://127.0.0.1:8000/api/tickets/status/Assigné`),
                    axios.get(`http://127.0.0.1:8000/api/tickets/status/En cours`),
                    axios.get(`http://127.0.0.1:8000/api/tickets/status/Fermé`),
                ]);
                this.numAgents = agentResponse.data.length;
                this.numClients = clientResponse.data.data.length;
                this.numTickets = ticketsResponse.data.total;
                this.numTicketsAssigned = ticketsAssignedResponse.data.total;
                this.numTicketsInProgress = ticketsInProgressResponse.data.total;
                this.numTicketsResolved = ticketsResolvedResponse.data.total;

            } catch (error) {
                console.error('Erreur', error);
            }

        }
    }
};
</script>

<template>
    <Layout>
        <PageHeader title="Statistiques" pageTitle="Dashboards" />

        <div>
            <BRow>
                <BCol xl="12">
                    <BRow>
                        <BCol lg="4">
                            <BCard no-body>
                                <BCardBody>
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3">
                                            <p class="text-muted mb-2">Agents</p>
                                            <h5 class="mb-0">{{ numAgents || 0 }}</h5>
                                        </div>

                                        <div class="avatar-sm ms-auto">
                                            <div class="avatar-title bg-light rounded-circle text-primary font-size-20">
                                                <i class="bx bx-user"></i>
                                            </div>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol lg="4">
                            <BCard no-body>
                                <BCardBody>
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3">
                                            <p class="text-muted mb-2">Clients</p>
                                            <h5 class="mb-0">{{ numClients || 0 }}</h5>
                                        </div>

                                        <div class="avatar-sm ms-auto">
                                            <div class="avatar-title bg-light rounded-circle text-primary font-size-20">
                                                <i class="bx bx-user"></i>
                                            </div>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol lg="4">
                            <BCard no-body>
                                <BCardBody>
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3">
                                            <p class="text-muted mb-2">Tickets </p>
                                            <h5 class="mb-0">{{ numTickets || 0 }}</h5>
                                        </div>

                                        <div class="avatar-sm ms-auto">
                                            <div class="avatar-title bg-light rounded-circle text-primary font-size-20">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>

                    </BRow>
                    <BRow>
                        <BCol lg="4">
                            <BCard no-body>
                                <BCardBody>
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3">
                                            <p class="text-muted mb-2">Tickets assignés</p>
                                            <h5 class="mb-0">{{ numTicketsAssigned || 0 }}</h5>
                                        </div>

                                        <div class="avatar-sm ms-auto">
                                            <div class="avatar-title bg-light rounded-circle text-warning font-size-20">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol lg="4">
                            <BCard no-body>
                                <BCardBody>
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3">
                                            <p class="text-muted mb-2">Tickets en cours</p>
                                            <h5 class="mb-0">{{ numTicketsInProgress || 0 }}</h5>
                                        </div>

                                        <div class="avatar-sm ms-auto">
                                            <div class="avatar-title bg-light rounded-circle text-primary font-size-20">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>
                        <BCol lg="4">
                            <BCard no-body>
                                <BCardBody>
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3">
                                            <p class="text-muted mb-2">Tickets Résolus</p>
                                            <h5 class="mb-0">{{ numTicketsResolved || 0 }}</h5>
                                        </div>

                                        <div class="avatar-sm ms-auto">
                                            <div class="avatar-title bg-light rounded-circle text-success font-size-20">
                                                <i class="bx bx-receipt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </BCardBody>
                            </BCard>
                        </BCol>

                    </BRow>

                    <BCard no-body>
                        <Statistiques />
                    </BCard>
                </BCol>
            </BRow>
        </div>
    </Layout>
</template>
