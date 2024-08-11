<script>
import axios from 'axios';
import Layout from '../../layouts/main';
import PageHeader from '@/components/page-header';
import Swal from 'sweetalert2';



/**
 * Products-order component
 */
export default {
    components: { Layout, PageHeader },
    data() {
        return {
            tickets: [],
            agentId: null
        };
    },
    mounted() {
        this.agentId = this.getUserId();
        this.fetchUnassign();
    },
    methods: {
        fetchUnassign() {
            axios.get(`http://127.0.0.1:8000/api/ticket/unassigned`)
                .then(response => {
                    this.tickets = response.data;
                }).catch(error => {
                    console.error('Erreur', error);
                });
        },
        getUserId() {
            const user = JSON.parse(localStorage.getItem('user'));
            console.log('user from localstorage:', user);
            console.log('id', user.id);
            return user ? user.id : null;

        },
        assignToAgent(ticketId) {
            Swal.fire({
                title: 'Voulez vous traiter ce ticket?',
                text: 'Ce ticket vous sera assigné!',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Oui, traiter!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`http://127.0.0.1:8000/api/tickets/${ticketId}/assigned`, { agentId: this.agentId })
                        .then(response => {
                            if (response.status === 200) {
                                this.$emit('ticketAssigned', ticketId);
                                Swal.fire('Assigné!', 'le ticket a été assigné', 'success');
                                this.removeTicket(ticketId);
                            } else {
                                Swal.fire('Erreur!', 'Erreur', 'error');
                            }
                        })
                }
            })

        },
        removeTicket(ticketId) {
            this.tickets = this.tickets.filter(ticket => ticket.id !== ticketId);
        }
    }
};
</script>

<template>
    <Layout>
        <PageHeader title="Tickets non assignés" pageTitle="Ticket" />

        <BRow>
            <BCol cols="12">
                <BCard no-body>
                    <BCardBody>
                        <BRow class="mb-2">
                            <BCol sm="4">
                                <div class="search-box me-2 mb-2 d-inline-block">
                                    <div class="position-relative">
                                        <input type="text" class="form-control" id="searchTableList"
                                            placeholder="Search..." />
                                        <i class="bx bx-search-alt search-icon"></i>
                                    </div>
                                </div>
                            </BCol>
                        </BRow>
                        <div class="table-responsive mb-0">
                            <BTableSimple class="align-middle table-nowrap" v-if="tickets.length">
                                <BThead class="table-primary">
                                    <BTr>
                                        <BTh style="width: 20px">
                                            <div class="form-check font-size-16 align-middle">
                                                <input class="form-check-input" type="checkbox"
                                                    id="transactionCheck01" />
                                                <label class="form-check-label" for="transactionCheck01"></label>
                                            </div>
                                        </BTh>
                                        <BTh class="align-middle">Index</BTh>
                                        <BTh class="align-middle">Sujet</BTh>
                                        <BTh class="align-middle">Statut</BTh>
                                        <BTh class="align-middle">Priorite</BTh>
                                        <BTh class="align-middle">Date</BTh>
                                        <BTh class="align-middle">Action</BTh>
                                    </BTr>
                                </BThead>
                                <BTbody>
                                    <BTr v-for="(ticket, index) in tickets" :key="index">
                                        <BTd>
                                            <div class="form-check font-size-16">
                                                <input class="form-check-input" type="checkbox"
                                                    :id="`transactionCheck${ticket.index}`" />
                                                <label class="form-check-label"
                                                    :for="`transactionCheck${ticket.index}`"></label>
                                            </div>
                                        </BTd>
                                        <BTd>

                                            {{ index + 1 }}

                                        </BTd>
                                        <BTd>{{ ticket.sujet }}</BTd>
                                        <BTd>{{ ticket.statut }}</BTd>
                                        <BTd>{{ ticket.priorite?.niveau || 'N/A' }}</BTd>
                                        <BTd> {{ new Date(ticket.created_at).toLocaleDateString() }} </BTd>

                                        <BTd>
                                            <BButton variant="primary" class="btn-sm btn-rounded"
                                                @click="assignToAgent(ticket.id)">
                                                Traiter
                                            </BButton>
                                        </BTd>
                                    </BTr>
                                </BTbody>
                            </BTableSimple>
                            <BTableSimple v-else colspan="6">
                                <p align="center"> Aucun ticket non assigné</p>
                            </BTableSimple>
                        </div>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
