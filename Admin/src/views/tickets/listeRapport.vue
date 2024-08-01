<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";

/** * Invoice-list component */
export default {
    components: { Layout, PageHeader },
    data() {
        return {

        }
    }
};
</script>

<template>
    <Layout>
        <PageHeader title="Rapport d'activité" pageTitle="Tickets" />

        <BRow>
            <BCol xl="4" sm="6">
                <BCard no-body>
                    <BCardBody>
                        <BRow>

                            <BCol lg="8">
                                <div>
                                    <!-- <router-link :to="" class="d-block text-primary text-decoration-underline mb-2">Ticket N°1</router-link> -->
                                    <h5 class="text-truncate mb-4 mb-lg-5">Probleme de ticket</h5>
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item me-3">
                                            <h5 class="font-size-14" data-toggle="tooltip" data-placement="top"
                                                title="Amount">
                                                <i class="bx bx-money me-1 text-muted"></i>vero@gmail.com
                                            </h5>
                                        </li>
                                        <li class="list-inline-item ms-1">
                                            <h5 class="font-size-14" data-toggle="tooltip" data-placement="top"
                                                title="Due Date">
                                                <i class="bx bx-calendar me-1 text-muted"></i> 19,Jul 2024
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
