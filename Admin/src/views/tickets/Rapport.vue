<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";

/** * Invoice-detail component */
export default {

  components: { Layout, PageHeader },
};
</script>

<template>
  <Layout>
    <PageHeader title="Detail" pageTitle="Invoices" />

    <BRow>
      <BCol lg="12">
        <BCard no-body>
          <BCardBody>
            <div class="invoice-title">
              <h4 class="float-end font-size-16">Ticket N°1</h4>
              <!-- <div class="mb-4">
                <img src="@/assets/images/logo-dark.png" alt="logo" height="20" />
              </div> -->
            </div>
            <hr />
            <BRow>
              <BCol cols="6" class="mt-3">
                <address>
                  <strong>Au client: </strong>
                  vmagnouwai@gmail.com
                </address>
              </BCol>
              <BCol cols="6" class="mt-3 text-sm-end">
                <address>
                  <strong>Résolu le:</strong>
                  <br />October 16, 2019
                  <br />
                  <br />
                </address>
              </BCol>
            </BRow>
            <div class="p-2 mt-3">
              <h3 class="font-size-16">Rapport du ticket</h3>
            </div>
            <div class="table-responsive">
              <BTableSimple>
                <BThead>
                  <BTr>
                    <BTh style="width: 70px">No.</BTh>
                    <BTh>Item</BTh>
                  </BTr>
                </BThead>
                <BTbody>
                  <BTr>
                    <BTd>Ticket N°1</BTd>
                    <BTd>Skote - Bootstrap 5 Admin Dashboard</BTd>
                  </BTr>

                  <BTr>
                    <BTd>Statut : </BTd>
                    <BTd>Skote - Bootstrap 4 Landing Template</BTd>
                  </BTr>

                  <BTr>
                    <BTd>Description : </BTd>
                    <BTd>Veltrix - Bootstrap 4 Admin Template</BTd>
                  </BTr>
                  <BTr>
                    <BTd>Statut : </BTd>
                    <BTd>Veltrix - Bootstrap 4 Admin Template</BTd>
                  </BTr>
                  <BTr>
                    <BTr>
                    <BTd>Attribué à: </BTd>
                    <BTd>Veltrix - Bootstrap 4 Admin Template</BTd>
                  </BTr>
                  <BTr>
                    <BTd>Ouvert le :</BTd>
                    <BTd>Veltrix - Bootstrap 4 Admin Template</BTd>
                  </BTr>
                  </BTr>
                </BTbody>
              </BTableSimple>
            </div>
            <div class="d-print-none">
              <div class="float-end">
                <BLink href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"><i
                    class="fa fa-print"></i></BLink>
                <BLink href="#" class="btn btn-primary w-md waves-effect waves-light ms-1">Send</BLink>
              </div>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>
