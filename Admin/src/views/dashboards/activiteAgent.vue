<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import StatistiquesAgent from "./statistiquesAgent.vue";

/**
 * Dashboard Component
 */
export default {
  components: {
    Layout,
    PageHeader,
    StatistiquesAgent
  },
}
</script>

<template>
  <Layout>
    <PageHeader title="statistiques" pageTitle="Dashboard" />
    <StatistiquesAgent/>

  </Layout>
</template>

