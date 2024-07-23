<script>
import axios from "axios";
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";

/**
 * Dashboard Component
 */
export default {
    components: {
        Layout,
        PageHeader,
    },

    data() {
        return {
            users: []

        };

    },
    mounted() {
        this.fetchUsers();
    },
    methods: {
        fetchUsers() {
            axios.get(`http://127.0.0.1:8000/api/users`)
                .then(response => {
                    this.users = response.data;
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des tickets:', error);
                });
        }
    }
}
</script>

<template>
    <Layout>
        <PageHeader title="Liste des utilisateurs" pageTitle="utilisateurs" />
        <BRow>
            <BCol xl="4" sm="4" v-for="(user, index) in users" :key="index">
                <BCard no-body>
                    <BCardBody>
                        <div class="d-flex">
                            <div class="avatar-md me-4">
                                <span class="avatar-title rounded-circle bg-light text-danger font-size-16">
                                    <img src="@/assets/images/companies/img-1.png" alt height="30" />
                                </span>
                            </div>

                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="text-truncate font-size-15">
                                    <BLink href="javascript: void(0);" class="text-dark">{{ user.name }} </BLink>
                                </h5>
                                <p class="text-muted mb-4">{{ user.role?.name || 'N/A' }}</p>

                            </div>
                        </div>
                    </BCardBody>
                    <div class="px-4 py-3 border-top">
                        <ul class="list-inline mb-0">
                            <li v-b-tooltip.hover.top class="list-inline-item me-3" title="Date de création">
                                <i class="bx bx-calendar me-1"></i>
                                {{ new Date(user.created_at).toLocaleDateString() }}
                            </li>
                        </ul>
                    </div>
                </BCard>
            </BCol>
        </BRow>

    </Layout>
</template>
