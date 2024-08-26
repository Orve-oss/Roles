<template>
    <Loader :loading="updating">
        <BCard no-body>
            <BCardBody>
                <div class="d-sm-flex flex-wrap">
                    <BCardTitle class="mb-4">Statistiques des Tickets</BCardTitle>
                    <!-- Sélecteur de service -->
                    <BFormSelect v-model="selectedService" @change="fetchTicketStats">

                        <BFormSelectOption v-for="service in services" :key="service.id" :value="service.id">
                            {{ service.nom_service }}
                        </BFormSelectOption>
                    </BFormSelect>
                </div>

                <!-- Cartes de Statistiques -->
                <BRow>
                    <BCol md="4">
                        <BCard>
                            <BCardBody>
                                <h4>En attente</h4>
                                <h2>{{ stats.pending }}</h2>
                            </BCardBody>
                        </BCard>
                    </BCol>
                    <BCol md="4">
                        <BCard>
                            <BCardBody>
                                <h4>En cours</h4>
                                <h2>{{ stats.progress }}</h2>
                            </BCardBody>
                        </BCard>
                    </BCol>
                    <BCol md="4">
                        <BCard>
                            <BCardBody>
                                <h4>Résolu</h4>
                                <h2>{{ stats.resolved }}</h2>
                            </BCardBody>
                        </BCard>
                    </BCol>
                </BRow>

                <!-- Statistiques en Donut -->
                <BRow class="mt-4">
                    <!-- Statistiques en Donut -->
                    <BCol md="6">
                        <apexchart class="apex-charts" type="donut" height="300"
                            :series="[stats.pending, stats.progress, stats.resolved]" :options="donutOptions">
                        </apexchart>
                    </BCol>

                    <!-- Statistiques en Courbes -->
                    <BCol md="6">
                        <apexchart class="apex-charts" type="line" height="300" :series="series"
                            :options="chartOptions">
                        </apexchart>
                    </BCol>
                </BRow>


            </BCardBody>
        </BCard>
    </Loader>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";

export default {
    components: {
        apexchart: VueApexCharts,
    },
    data() {
        return {
            selectedService: null,
            services: [], // Ajouter les services ici
            stats: {
                pending: 0,
                progress: 0,
                resolved: 0,
            },
            series: [
                {
                    name: "En attente",
                    data: [],
                },
                {
                    name: "En cours",
                    data: [],
                },
                {
                    name: "Résolu",
                    data: [],
                },
            ],
            chartOptions: {
                chart: {
                    type: 'line',
                    toolbar: {
                        show: false
                    }
                },
                xaxis: {
                    categories: [], // Mois de l'année
                },
                colors: ["#f1b44c", "#556ee6", "#34c38f"],
                legend: {
                    position: "bottom",
                },
                fill: {
                    opacity: 1,
                },
            },
            donutOptions: {
                labels: ["En attente", "En cours", "Résolu"],
                colors: ["#f1b44c", "#556ee6", "#34c38f"],
            },
            updating: false,
        };
    },
    mounted() {
        this.fetchServices();
        this.fetchTicketStats();
    },
    methods: {
        fetchServices() {
            axios.get('http://127.0.0.1:8000/api/services')
                .then(response => {
                    this.services = response.data;
                });
        },
        fetchTicketStats() {
            this.updating = true;
            const clientId = this.getClientId();
            const serviceId = this.selectedService || '';
            axios.get(`http://127.0.0.1:8000/api/ticket/${clientId}/client`, {
                params: { service_id: serviceId }
            })
                .then(response => {
                    const stats = response.data;
                    this.stats = {
                        pending: stats.pending || 0,
                        progress: stats.progress || 0,
                        resolved: stats.resolved || 0,
                    };
                    this.updateSeries(stats.monthlyStats || []);
                })
                .finally(() => {
                    this.updating = false;
                });
        },

        updateSeries(monthlyStats) {
            // Vérifiez le format des données reçues
            console.log("Données mensuelles:", monthlyStats);

            const months = monthlyStats.map(stat => stat.month);
            const pendingData = monthlyStats.map(stat => stat.pending);
            const progressData = monthlyStats.map(stat => stat.progress);
            const resolvedData = monthlyStats.map(stat => stat.resolved);

            // Assurez-vous que les mois sont correctement définis
            console.log("Mois:", months);
            console.log("Données en attente:", pendingData);
            console.log("Données en cours:", progressData);
            console.log("Données résolues:", resolvedData);

            this.chartOptions.xaxis.categories = months;

            this.series[0].data = pendingData;
            this.series[1].data = progressData;
            this.series[2].data = resolvedData;
        },

        getClientId() {
            const user = JSON.parse(localStorage.getItem('user'));
            return user ? user.id : null;
        },
    },
};
</script>

<!-- <script>

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
          </div>

          <apexchart class="apex-charts" type="bar" dir="ltr" height="360" :series="series" :options="chartOptions">
          </apexchart>
        </BCardBody>
      </BCard>
    </Loader>
  </template>
 -->
