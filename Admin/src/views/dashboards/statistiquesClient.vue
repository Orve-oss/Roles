<script>

import VueApexCharts from "vue3-apexcharts";
import axios from "axios";

export default {
  components: {

    apexchart: VueApexCharts
  },
  data() {
    return {
        clientId: null,
      isActive: "year",
      statistics: [],
      googleWorkspace: {
        created: 0,
        pending: 0,
        progress: 0,
        resolved: 0,
      },
      wsdGescom: {
        created: 0,
        pending: 0,
        progress: 0,
        resolved: 0,
      },
      series: [
        {
          name: "Crées",
          data: [0, 0], // Initialisation avec des valeurs par défaut
        },
        {
          name: "En attente",
          data: [0, 0],
        },
        {
          name: "En cours",
          data: [0, 0],
        },
        {
          name: "Résolu",
          data: [0, 0],
        },
      ],
      chartOptions: {
        chart: {
          stacked: true,
          toolbar: {
            show: false,
          },
          zoom: {
            enabled: true,
          },
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "5%",
            endingShape: "rounded",
          },
        },
        dataLabels: {
          enabled: false,
        },
        xaxis: {
          categories: ["Google Workspace", "WSD GESCOM"],
        },
        colors: ["#716868","#f1b44c","#556ee6" , "#34c38f"],
        legend: {
          position: "bottom",
        },
        fill: {
          opacity: 1,
        },
      },
      updating: false,
    };
  },
  mounted() {
    this.fetchTicketStats();
  },
  methods: {
    getClientId(){
            const user = JSON.parse(localStorage.getItem('user'));
            return user ? user.id : null;
        },
    fetchTicketStats() {
        this.clientId = this.getClientId();
      this.updating = true;
      axios.get(`http://127.0.0.1:8000/api/ticket/${this.clientId}/client`)
      .then((response) => {
          this.statistics = response.data;
          this.statistics.forEach((stat) => {
            if (stat.service === "Google Workspace") {
              this.googleWorkspace = {
                created: stat.created || 0,
                pending: stat.pending || 0,
                progress: stat.progress || 0,
                resolved: stat.resolved || 0,
              };
            } else if (stat.service === "WSD GESCOM") {
              this.wsdGescom = {
                created: stat.created || 0,
                pending: stat.pending || 0,
                progress: stat.progress || 0,
                resolved: stat.resolved || 0,
              };
            }
            this.series = [
          {
            name: "Crées",
            data: [this.googleWorkspace.created, this.wsdGescom.created],
          },
          {
            name: "En attente",
            data: [this.googleWorkspace.pending, this.wsdGescom.pending],
          },
          {
            name: "En cours",
            data: [this.googleWorkspace.progress, this.wsdGescom.progress],
          },
          {
            name: "Résolu",
            data: [this.googleWorkspace.resolved, this.wsdGescom.resolved],
          },
        ];
          });
        })
      .catch(error => {
        console.error('Erreur lors de la récupération des statistiques:', error);
      })
      .finally(() => {
        this.updating = false;
      });
    },
    changeVal(value) {
      this.isActive = value;
      this.fetchTicketStats();
    },
  },
};
</script>
<template>
    <Loader :loading="updating">
      <BCard no-body>
        <BCardBody>
          <div class="d-sm-flex flex-wrap">
            <BCardTitle class="mb-4">Statistiques des Tickets</BCardTitle>
            <!-- <div class="ms-auto">
              <ul class="nav nav-pills">
                <li class="nav-item">
                  <BLink class="nav-link" href="javascript: void(0);" @click="changeVal('week')"
                    :class="{ 'active': isActive == 'week' }">Semaine</BLink>
                </li>
                <li class="nav-item">
                  <BLink class="nav-link" href="javascript: void(0);" @click="changeVal('month')"
                    :class="{ 'active': isActive == 'month' }">Mois</BLink>
                </li>
                <li class="nav-item">
                  <BLink class="nav-link" href="javascript: void(0);" @click="changeVal('year')"
                    :class="{ 'active': isActive == 'year' }">Année</BLink>
                </li>
              </ul>
            </div> -->
          </div>

          <apexchart class="apex-charts" type="bar" dir="ltr" height="360" :series="series" :options="chartOptions">
          </apexchart>
        </BCardBody>
      </BCard>
    </Loader>
  </template>

