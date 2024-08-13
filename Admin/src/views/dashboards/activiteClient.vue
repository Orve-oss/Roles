<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import StatistiquesClient from "./statistiquesClient.vue";

/**
 * Dashboard Component
 */
export default {
  components: {
    Layout,
    PageHeader,
    StatistiquesClient
  },
}
</script>

<template>
  <Layout>
    <PageHeader title="Tableau de bord du client" pageTitle="Dashboard" />
   <StatistiquesClient/>

  </Layout>
</template>

