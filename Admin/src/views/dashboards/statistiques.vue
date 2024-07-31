<script>
import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts';

/**
 * Dashboard Component
 */
export default {
   components: {
      apexchart: VueApexCharts,
   },
   data() {
      return {
         chartData: {
            series: [
               {
                  name: 'Google Workspace',
                  data: [],
               },
               {
                  name: 'WSD GESCOM',
                  data: [],
               },
            ],
            chartOptions: {
               chart: {
                  height: 350,
                  type: 'area',
                  toolbar: {
                     show: false,
                  },
               }
            },
            colors: ["#556ee6", "#f1b44c"],
            dataLabels: {
               enabled: false,
            },
            stroke: {
               curve: "smooth",
               width: 2,
            },
            fill: {
               type: 'gradient',
               gradient: {
                  shadeIntensity: 1,
                  inverseColors: false,
                  opacityFrom: 0.45,
                  opacityTo: 0.05,
                  stops: [20, 100, 100, 100],
               },
            },
            xaxis: {
               categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            },
            markers: {
               size: 3,
               strokeWidth: 3,
               hover: {
                  size: 4,
                  sizeOffset: 2,
               },
            },
            legend: {
               position: "top",
               horizontalAlign: "right",
            },
         },
         selection: "all",
      };

   },
   mounted() {
      this.fetchData();
   },
   methods: {
      async fetchData() {
         try {
            const [googleResponse, gescomResponse] = await Promise.all([
               axios.get(`http://127.0.0.1:8000/api/tickets/service/Google Workspace`),
               axios.get(`http://127.0.0.1:8000/api/tickets/service/WSD GESCOM`)
            ]);
            const google = this.processData(googleResponse.data);
            const gescom = this.processData(gescomResponse.data);
            this.chartData.series[0].data = google;
            this.chartData.series[0].data = gescom;

         } catch (error) {
            console.error('Erreur lors de la récupération des données', error);
         }
      },
      processData(data){
         const monthlyData = Array(12).fill(0);
         data.forEach(ticket => {
            const month = new Date(ticket.created_at).getMonth();
            monthlyData[month] +=1;

         });
         return monthlyData;
      }

   }
}
</script>

<template>
   <Layout>
      <PageHeader title="Statistiques" pageTitle="Dashboards" />
      <BCardBody>
    <div class="d-flex flex-wrap">
      <BCardTitle class="me-2">Tickets par services</BCardTitle>
      <!-- <div class="ms-auto">
        <div class="toolbar button-items text-end">
          <BButton variant="light" class="ms-2" size="sm" @click="updateData('all')" :class="{ active: selection === 'all' }">
            ALL
          </BButton>
          <BButton variant="light" class="ms-2" size="sm" @click="updateData('one_month')" :class="{ active: selection === 'one_month' }">
            1M
          </BButton>
          <BButton variant="light" class="ms-2" size="sm" @click="updateData('six_months')" :class="{ active: selection === 'six_months' }">
            6M
          </BButton>
          <BButton variant="light" class="ms-2" size="sm" @click="updateData('one_year')" :class="{ active: selection === 'one_year' }">
            1Y
          </BButton>
        </div>
      </div> -->
    </div>
    <hr class="mb-4" />

    <apexchart class="apex-charts" dir="ltr" height="350" :series="chartData.series" :options="chartData.chartOptions">
    </apexchart>
  </BCardBody>

   </Layout>
</template>
