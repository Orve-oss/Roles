<script>

import axios from "axios";
import Swal from 'sweetalert2';

import Layout from '../../layouts/main'
import PageHeader from '@/components/page-header'

/**
 * Task-create component
 */
export default {
    components: { Layout, PageHeader },
    data() {
        return {


            ticket: {
                sujet: '',
                description: '',
                image: null,
                status: 'En attente',
                type: '',
                service: '',
                priorite: '',
                client_id: null
            },
            successMessage: null,
            types: [],
            services: [],
            priorites: [],
            submitted: false,

        };


    },
    mounted() {
        this.fetchTypes();
        this.fetchServices();
        this.fetchPriorites();
        this.ticket.client_id = this.getclientId();
        console.log('client id', this.ticket.client_id);
    },
    methods: {
        getclientId(){
            const user = JSON.parse(localStorage.getItem('user'));
            console.log('user from localstorage:',user);
            console.log('id',user.id);
            return user ? user.id : null;

        },
        fetchTypes() {
            axios.get('http://127.0.0.1:8000/api/types')
                .then(response => {
                    this.types = response.data;
                })
                .catch(error => {
                    console.error('Error fetching roles:', error);
                });
        },
        fetchServices() {
            axios.get('http://127.0.0.1:8000/api/services')
                .then(response => {
                    this.services = response.data;
                })
                .catch(error => {
                    console.error('Error fetching services:', error);
                });
        },
        fetchPriorites() {
            axios.get('http://127.0.0.1:8000/api/priorites')
                .then(response => {
                    this.priorites = response.data;
                })
                .catch(error => {
                    console.error('Error fetching priorites:', error);
                });
        },
        handleImageUpload(event) {
            const file = event.target.files[0];
            const fileType = file.type;
            const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            const validFileTypes = ['application/pdf'];
            const maxFileSize = 2* 1024 * 1024 * 1024;
            if (validImageTypes.includes(fileType) || validFileTypes.includes(fileType)) {
                if (file.size <= maxFileSize) {
                    this.ticket.image = file;
                }
                else {
                Swal.fire('Erreur', 'Le fichier dépasse la taille maximale d 2Go.', 'error');
            }
            } else {
                Swal.fire('Erreur', 'Seuls les fichiers au format JPEG, PNG, JPG, GIF et PDF sont autorisés', 'error');
            }
        },
        async createTicket() {


            this.submitted = true;
            const formData = new FormData();
            formData.append('sujet', this.ticket.sujet);
            formData.append('description', this.ticket.description);
            formData.append('status', this.ticket.status);
            formData.append('service_id', this.ticket.service);
            formData.append('type_ticket_id', this.ticket.type);
            formData.append('priorite_id', this.ticket.priorite);
            formData.append('client_id', this.ticket.client_id);
            if (this.ticket.image) {
                formData.append('image', this.ticket.image);
            }

            await axios.post('http://127.0.0.1:8000/api/tickets', formData,{
                headers:{
                    'Content-Type': 'multipart/form-data'
                }
            }

             )

                .then(response => {
                    console.log(formData);
                    this.errors = null;
                    console.log(response);

                    this.ticket = {
                        sujet: '',
                        description: '',
                        image: '',
                        status: 'En attente',
                        type: '',
                        service: '',
                        priorite: '',
                        client_id: null
                    };

                    Swal.fire(
                        'Créé!',
                        this.successMessage = response.data.message,
                        'success'
                    );

                })
                .catch(error => {
                    this.successMessage = null;
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.error('Error creating ticket:', error);
                        this.errors = ['Une erreur est survenue lors de la création du ticket'];
                    }
                });
        }

    },

}
</script>

<template>
    <Layout>
        <PageHeader title="Créer un ticket" pageTitle="Tickets" />

        <BRow>
            <BCol lg="12">
                <BCard no-body>
                    <BCardBody>
                        <BCardTitle class="mb-4">Creer un nouveau ticket</BCardTitle>
                        <BForm class="outer-repeater" @submit.prevent="createTicket">
                            <div data-repeater-list="outer-group" class="outer">
                                <div data-repeater-item class="outer">
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label for="sujet" class="col-form-label col-lg-2">Type</label>
                                            <BCol lg="10">
                                                <BFormSelect v-model="ticket.type" class="form-select">
                                                    <BFormSelectOption v-for="type in types" :key="type.id"
                                                        :value="type.id">{{
                                                            type.libelle }}</BFormSelectOption>
                                                </BFormSelect>
                                            </BCol>
                                        </div>
                                    </BFormGroup>
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label for="sujet" class="col-form-label col-lg-2">Service</label>
                                            <BCol lg="10">
                                                <BFormSelect v-model="ticket.service" class="form-select">
                                                    <BFormSelectOption v-for="service in services" :key="service.id"
                                                        :value="service.id">{{
                                                            service.nom_service }}</BFormSelectOption>
                                                </BFormSelect>
                                            </BCol>
                                        </div>
                                    </BFormGroup>
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label for="sujet" class="col-form-label col-lg-2">Sujet</label>
                                            <BCol lg="10">
                                                <BFormInput id="sujet" v-model="ticket.sujet" type="text"
                                                    class="form-control" placeholder="Entrez le sujet du ticket" />
                                            </BCol>
                                        </div>
                                    </BFormGroup>
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label class="col-form-label col-lg-2">Description</label>
                                            <BCol lg="10">
                                                <BFormTextarea id="projectdesc" class="form-control" v-model="ticket.description" rows="3"
                                                    placeholder="Enter Project Description..."></BFormTextarea>
                                            </BCol>
                                        </div>
                                    </BFormGroup>
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label for="sujet" class="col-form-label col-lg-2">Priorité</label>
                                            <BCol lg="10">
                                                <BFormSelect v-model="ticket.priorite" class="form-select">
                                                    <BFormSelectOption :value="null">Select</BFormSelectOption>
                                                    <BFormSelectOption v-for="priorite in priorites" :key="priorite.id"
                                                        :value="priorite.id">{{
                                                            priorite.niveau }}</BFormSelectOption>
                                                </BFormSelect>
                                            </BCol>
                                        </div>
                                    </BFormGroup>
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label for="sujet" class="col-form-label col-lg-2">Image</label>
                                            <BCol lg="5">
                                                <input id="image" @change="handleImageUpload" type="file"
                                                    class="form-control" placeholder="Entrez le sujet du ticket" />
                                            </BCol>
                                        </div>
                                    </BFormGroup>

                                </div>
                            </div>
                            <BRow class="justify-content-end">
                                <BCol lg="10">
                                    <BButton type="submit" variant="primary">Creer le ticket</BButton>
                                </BCol>
                            </BRow>
                        </BForm>

                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
