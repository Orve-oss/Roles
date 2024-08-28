<template>
    <div>
        <BCard no-body v-for="(data, index) in serviceData" :key="index">
            <BCardBody>
                <BCardTitle class="mb-3">{{ data.service }}</BCardTitle>

                <BRow>
                    <BCol lg="4">
                        <div class="mt-4">
                            <p>Nombre total de tickets assignés : {{ data.totalAssigned }}</p>

                            <BRow>
                                <BCol cols="3" class="mx-2">
                                    <div class="ticket-stats">
                                        <div class="status-box bg-warning">
                                            <p class="mb-2">Attente</p>
                                            <h5>{{ data.pendingCount }}</h5>
                                        </div>
                                    </div>
                                </BCol>
                                <BCol cols="3" class="mx-2">
                                    <div class="ticket-stats">
                                        <div class="status-box bg-info">
                                            <p class="mb-2">En cours</p>
                                            <h5>{{ data.progressCount }}</h5>
                                        </div>
                                    </div>
                                </BCol>
                                <BCol cols="3">
                                    <div class="ticket-stats">
                                        <div class="status-box bg-success">
                                            <p class="mb-2">Résolus</p>
                                            <h5>{{ data.resolvedCount }}</h5>
                                        </div>
                                    </div>
                                </BCol>
                            </BRow>
                        </div>
                    </BCol>

                    <BCol lg="8">
                        <div>
                            <apexchart class="apex-charts" dir="ltr" type="radialBar" height="200"
                                :series="data.chartSeries" :options="chartOptions"></apexchart>
                        </div>
                        <div class="mt-4">
                            <!-- Ajoutez le composant de graphique en courbes ici -->
                            <apexchart class="apex-charts" type="line" height="300" :series="data.lineSeries"
                                :options="lineChartOptions"></apexchart>
                        </div>
                    </BCol>
                </BRow>
            </BCardBody>
        </BCard>
    </div>
</template>
<script>
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
export default {

    components: {
        apexchart: VueApexCharts
    },
    data() {
        return {
            serviceData: [],
            agentId: null,
            chartOptions: {
                chart: {
                    type: 'radialBar',
                    height: 200, // Réduit la taille du cercle
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: {
                                fontSize: '22px',
                            },
                            value: {
                                fontSize: '16px',
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: () => {
                                    return 100;
                                }
                            }
                        }
                    }
                },
                labels: ['Assigné', 'En attente', 'En cours', 'Résolu'],
            },
            lineChartOptions: {
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
            }
        };
    },
    mounted() {
        this.fetchData();
        this.agentId = this.getAgentId();
    },
    methods: {
        getAgentId() {
            const user = JSON.parse(localStorage.getItem('user'));
            return user ? user.id : null;
        },
        fetchData() {
            this.agentId = this.getAgentId();

            if (!this.agentId) {
                console.error('Agent Id is not defined');
            }
            axios.get(`http://127.0.0.1:8000/api/ticket/${this.agentId}/dashboard`)
                .then(response => {
                    this.serviceData = response.data.serviceData;
                    // Assurez-vous que les données pour le graphique en courbes sont formatées correctement
                    this.serviceData.forEach(service => {
                        // Assurez-vous que `lineSeries` est un tableau d'objets
                        service.lineSeries = [{
                            name: service.service,
                            data: service.monthlyStats.map(stat => stat.tickets)
                        }];
                        this.lineChartOptions.xaxis.categories = service.monthlyStats.map(stat => stat.month);
                    });
                }).catch(error => {
                    console.error('Erreur ', error);
                });
        }

    }
}

</script>

<style scoped>
.ticket-stats {
    text-align: center;
    width: 90px;
}

.status-box {
    padding: 8px;
    border-radius: 2px;
    color: white;
}

.bg-warning {
    background-color: #f1b44c;
}

.bg-info {
    background-color: #556ee6;
}

.bg-success {
    background-color: #34c38f;
}
</style>


<!-- <script>

import VueApexCharts from "vue3-apexcharts";
import axios from "axios";
/**
 * Dashboard Component
 */
export default {
    components: {

        apexchart: VueApexCharts
    },
    data() {
        return {

            serviceData: [],
            agentId: null,
            chartOptions: {
                chart: {
                    type: 'radialBar',
                    height: 300,
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: {
                                fontSize: '22px',
                            },
                            value: {
                                fontSize: '16px',
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: () => {
                                    return 100;
                                }
                            }
                        }
                    }
                },
                labels: ['Assigné', 'En attente', 'En cours', 'Résolu'],
            }
        };
    },
    mounted() {
        this.fetchData();
        this.agentId = this.getAgentId();
    },
    methods: {
        getAgentId(){
            const user = JSON.parse(localStorage.getItem('user'));
            return user ? user.id : null;
        },
        fetchData() {
            this.agentId = this.getAgentId();

            if (!this.agentId) {
                console.error('Agent Id is not defined');
            }
            axios.get(`http://127.0.0.1:8000/api/ticket/${this.agentId}/dashboard`)
                .then(response => {
                    this.serviceData = response.data.serviceData;
                }).catch(error => {
                    console.error('Erreur ', error);
                });
        }
    }
}
</script>

<template>

        <div>
            <BCard no-body v-for="(data, index) in serviceData" :key="index">
                <BCardBody>
                    <BCardTitle class="mb-3">{{ data.service }}</BCardTitle>

                    <BRow>
                        <BCol lg="4">
                            <div class="mt-4">
                                <p>Nombre total de tickets assignés : {{ data.totalAssigned }}</p>

                                <BRow>
                                    <BCol cols="6">
                                        <div>
                                            <p class="mb-2">En attente</p>
                                            <h5>{{ data.chartSeries[1] }}%</h5>
                                        </div>
                                    </BCol>
                                    <BCol cols="6">
                                        <div>
                                            <p class="mb-2">En cours</p>
                                            <h5>{{ data.chartSeries[2] }}%</h5>
                                        </div>
                                    </BCol>
                                    <BCol cols="6">
                                        <div>
                                            <p class="mb-2">Résolus</p>
                                            <h5>{{ data.chartSeries[3] }}%</h5>
                                        </div>
                                    </BCol>
                                </BRow>
                            </div>
                        </BCol>

                        <BCol lg="4" sm="6">
                            <div>
                                <apexchart class="apex-charts" dir="ltr" type="radialBar" height="300"
                                    :series="data.chartSeries" :options="chartOptions"></apexchart>
                            </div>
                        </BCol>
                    </BRow>
                </BCardBody>
            </BCard>
        </div>


</template> -->
