<script>
import CKEditor from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
import axios from "axios";

import Layout from '../../layouts/main'
import PageHeader from '@/components/page-header'

/**
 * Task-create component
 */
export default {
    components: { ckeditor: CKEditor.component, Layout, PageHeader },
    data() {
        return {
            editor: ClassicEditor,

            ticket: {
                sujet: '',
                description: '',
                image: '',
                type: '',
                service: '',
                priorite: ''
            },
            successMessage: null,
            types: [],
            services: [],
            priorites: [],
            submitted: false,
            options: [
                "Incident",
                "Problème"
            ],
        };


    },
    mounted() {
        this.fetchTypes();
        this.fetchServices();
        this.fetchPriorites();
    },
    methods: {
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
                    this.priorites = response.data.data;
                })
                .catch(error => {
                    console.error('Error fetching priorites:', error);
                });
        },
        createTicket() {
            axios.post('http://127.0.0.1:8000/api/tickets', this.ticket)
                .then(response => {
                    this.errors = null;
                    this.successMessage = response.data.message;
                    this.ticket = {
                        sujet: '',
                        description: '',
                        image: '',
                        type: '',
                        service: '',
                        priorite: '',
                    };
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
                                                    <BFormSelectOption :value="null">Select</BFormSelectOption>
                                                    <BFormSelectOption v-for="type in types" :key="type.id"
                                                        :value="type.libelle">{{
                                                        type.libelle }}</BFormSelectOption>
                                                </BFormSelect>
                                            </BCol>
                                        </div>
                                    </BFormGroup>
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label for="sujet" class="col-form-label col-lg-2">Sujet</label>
                                            <BCol lg="10">
                                                <BFormInput id="sujet" v-model="ticket.sujet"  type="text" class="form-control"
                                                    placeholder="Entrez le sujet du ticket" />
                                            </BCol>
                                        </div>
                                    </BFormGroup>
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label class="col-form-label col-lg-2">Description</label>
                                            <BCol lg="10">
                                                <div class="form-ckeditor">
                                                    <ckeditor :editor="editor" v-model="ticket.description"></ckeditor>
                                                </div>
                                            </BCol>
                                        </div>
                                    </BFormGroup>

                                    <!-- <BFormGroup class="form-group row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label class="col-form-label col-lg-2">Date</label>
                                            <BCol lg="10">
                                                <datepicker class="form-control" v-model="daterange" range
                                                    append-to-body lang="en" confirm>
                                                </datepicker>
                                            </BCol>
                                        </div>
                                    </BFormGroup> -->
                                    <BFormGroup class="row mb-4">
                                        <div class="d-flex flex-column flex-lg-row">
                                            <label for="sujet" class="col-form-label col-lg-2">Priorité</label>
                                            <BCol lg="10">
                                                <BFormSelect v-model="ticket.priorite" class="form-select">
                                                    <BFormSelectOption :value="null" >Select</BFormSelectOption>
                                                    <BFormSelectOption v-for="priorite in priorites" :key="priorite.id"
                                                        :value="priorite.niveau">{{
                                                        priorite.niveau }}</BFormSelectOption>
                                                </BFormSelect>
                                            </BCol>
                                        </div>
                                    </BFormGroup>

                                    <div class="inner-repeater mb-4">
                                        <div class="inner form-group mb-0 row">

                                            <div class="inner col-lg-10 ml-md-auto">
                                                <div class="mb-3 row align-items-center">
                                                    <label class="col-form-label col-lg-2">Image</label>
                                                    <BCol md="6">
                                                        <div class="mt-4 mt-md-0">
                                                            <input type="file" class="form-control" />
                                                        </div>
                                                    </BCol>
                                                    <BCol md="4">
                                                        <div class="mt-2 mt-md-0 d-grid">
                                                            <input type="button" class="btn btn-primary btn-block inner"
                                                                value="Delete" />
                                                        </div>
                                                    </BCol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <BRow class="justify-content-end">
                            <BCol lg="10">
                                <BButton variant="primary">Creer le ticket</BButton>
                            </BCol>
                        </BRow>
                        </BForm>

                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
