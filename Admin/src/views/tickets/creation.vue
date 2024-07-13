<script>
import Datepicker from "vue3-datepicker";
import CKEditor from '@ckeditor/ckeditor5-vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
import { ref } from 'vue'

import Layout from '../../layouts/main'
import PageHeader from '@/components/page-header'

/**
 * Task-create component
 */
export default {
  components: { Datepicker, ckeditor: CKEditor.component, Layout, PageHeader },
  data() {
    return {
      daterange: ref(new Date()),
      editor: ClassicEditor,
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
            <BForm class="outer-repeater">
              <div data-repeater-list="outer-group" class="outer">
                <div data-repeater-item class="outer">
                  <BFormGroup class="row mb-4">
                    <div class="d-flex flex-column flex-lg-row">
                      <label for="sujet" class="col-form-label col-lg-2">Sujet</label>
                      <BCol lg="10">
                        <BFormInput id="sujet" name="sujet" type="text" class="form-control" placeholder="Entrez le sujet du ticket" />
                      </BCol>
                    </div>
                  </BFormGroup>
                  <BFormGroup class="row mb-4">
                    <div class="d-flex flex-column flex-lg-row">
                      <label class="col-form-label col-lg-2">Description</label>
                      <BCol lg="10">
                        <div class="form-ckeditor">
                          <ckeditor :editor="editor" ></ckeditor>
                        </div>
                      </BCol>
                    </div>
                  </BFormGroup>

                  <BFormGroup class="form-group row mb-4">
                    <div class="d-flex flex-column flex-lg-row">
                      <label class="col-form-label col-lg-2">Date</label>
                      <BCol lg="10">
                        <datepicker class="form-control" v-model="daterange" range append-to-body lang="en" confirm>
                        </datepicker>
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
                              <input type="button" class="btn btn-primary btn-block inner" value="Delete" />
                            </div>
                          </BCol>
                        </div>
                      </div>
                    </div>
                  </div>
                  <BFormGroup class="row mb-4">
                    <div class="d-flex flex-column flex-lg-row">
                      <label for="taskbudget" class="col-form-label col-lg-2">Budget</label>
                      <BCol lg="10">
                        <BFormInput id="taskbudget" name="taskbudget" type="text" placeholder="Enter Task Budget..." class="form-control" />
                      </BCol>
                    </div>
                  </BFormGroup>
                </div>
              </div>
            </BForm>
            <BRow class="justify-content-end">
              <BCol lg="10">
                <BButton variant="primary">Creer le ticket</BButton>
              </BCol>
            </BRow>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>
