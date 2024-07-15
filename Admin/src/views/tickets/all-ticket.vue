<script>
import Layout from "../../layouts/main";//besoin
import PageHeader from "@/components/page-header";


/**
 * Customers component
 */
export default {
    components: { Layout, PageHeader },

    data() {
        return {

            showModal: false,
        }
    }
}

</script>

<template>
    <Layout>
        <PageHeader title="Liste des tickets" pageTitle="All ticket" />

        <BRow>
            <BCol cols="12">
                <BCard no-body>
                    <BCardBody>
                        <BRow class="mb-2">
                            <BCol sm="4">
                                <div class="search-box me-2 mb-2 d-inline-block">
                                    <div class="position-relative">
                                        <input type="text" class="form-control" placeholder="Recherche" />
                                        <i class="bx bx-search-alt search-icon"></i>
                                    </div>
                                </div>
                            </BCol>
                            <BCol sm="8">
                                <div class="text-sm-end">
                                    <BButton variant="success" class="btn-rounded mb-2 me-2"
                                        @click="showModal = !showModal">
                                        <i class="mdi mdi-plus me-1"></i>Nouveau ticket
                                    </BButton>
                                </div>
                            </BCol>
                        </BRow>
                        <div class="table-responsive">
                            <BTableSimple class="table-centered table-nowrap align-middle">
                                <BThead>
                                    <BTr>
                                        <BTh>Id</BTh>
                                        <BTh>Sujet</BTh>
                                        <BTh>Email du client</BTh>
                                        <BTh>Type-Ticket</BTh>
                                        <BTh>Priorite</BTh>
                                        <BTh>Date</BTh>
                                        <BTh>Detail</BTh>
                                        <BTh>Action</BTh>
                                    </BTr>
                                </BThead>
                                <BTbody>
                                    <BTr>
                                        <BTd>
                                            209#
                                        </BTd>
                                        <BTd>Probleme de connexion</BTd>
                                        <BTd>
                                            <p class="mb-0">orve@gmail.com</p>
                                        </BTd>
                                        <BTd>Probleme </BTd>
                                        <BTd>Urgent</BTd>
                                        <BTd>13-06-2024</BTd>
                                        <BTd>
                                            <BButton variant="primary" class="btn-sm btn-rounded"
                                                @click="showModal = !showModal">
                                                Voir
                                            </BButton>
                                        </BTd>
                                        <BTd>
                                            <BDropdown class="card-drop" variant="white" right toggle-class="p-0"
                                                menu-class="dropdown-menu-end">
                                                <template v-slot:button-content>
                                                    <i class="mdi mdi-dots-horizontal font-size-18"></i>
                                                </template>

                                                <BDropdownItem>
                                                    <i class="fas fa-pencil-alt text-success me-1"></i>
                                                    Edit
                                                </BDropdownItem>

                                                <BDropdownItem>
                                                    <i class="fas fa-trash-alt text-danger me-1"></i>
                                                    Delete
                                                </BDropdownItem>
                                            </BDropdown>
                                        </BTd>
                                    </BTr>
                                </BTbody>
                            </BTableSimple>
                        </div>
                        <pagination />
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
    <BModal v-model="showModal" title="Order Details" centered>
    <p class="mb-2">Product id: <span class="text-primary">#SK2540</span></p>
    <p class="mb-4">Billing Name: <span class="text-primary">Neal Matthews</span></p>

    <div class="table-responsive">
      <BTableSimple class="align-middle table-nowrap">
        <BTbody>

          <BTr>
            <BTd colspan="2">
              <h6 class="m-0 text-right">Id:</h6>
            </BTd>
            <BTd>
              209#
            </BTd>
          </BTr>
          <BTr>
            <BTd colspan="2">
              <h6 class="m-0 text-right">Sujet:</h6>
            </BTd>
            <BTd>
              Probleme de connexion
            </BTd>
          </BTr>
          <BTr>
            <BTd colspan="2">
              <h6 class="m-0 text-right">Total:</h6>
            </BTd>
            <BTd>
              $ 400
            </BTd>
          </BTr>
        </BTbody>
      </BTableSimple>
    </div>
    <template v-slot:modal-footer>
      <BButton variant="secondary" @click="showModal = !showModal">Close</BButton>
    </template>
  </BModal>
</template>
