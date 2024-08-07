<script>

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


</template>
