<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";

/**
 * Dashboard Component
 */
export default {
  components: {
    Layout,
    PageHeader,
  },
}
</script>

<template>
  <Layout>
    <PageHeader title="Tableau de bord du client" pageTitle="Dashboard" />
    <div>
        <BRow>
            <BCol xl="8">
            <BRow>
               <BCol sm="4">
                  <BCard no-body>
                     <BCardBody>
                        <div class="d-flex align-items-center mb-3">
                           <div class="avatar-xs me-3">
                              <span class="
                        avatar-title
                        rounded-circle
                        bg-primary-subtle
                        text-primary
                        font-size-18
                      ">
                                 <i :class="`bx bx-receipt`"></i>
                              </span>
                           </div>
                           <h5 class="font-size-14 mb-0">Total</h5>
                        </div>
                        <div class="text-muted mt-4">
                           <h4>
                              50
                              <i class="mdi mdi-chevron-up ms-1 text-success"></i>
                           </h4>

                        </div>
                     </BCardBody>
                  </BCard>
               </BCol>
               <BCol sm="4">
                  <BCard no-body>
                     <BCardBody>
                        <div class="d-flex align-items-center mb-3">
                           <div class="avatar-xs me-3">
                              <span class="
                        avatar-title
                        rounded-circle
                        bg-primary-subtle
                        text-primary
                        font-size-18
                      ">
                                 <i :class="`bx bx-receipt`"></i>
                              </span>
                           </div>
                           <h5 class="font-size-14 mb-0">Total</h5>
                        </div>
                        <div class="text-muted mt-4">
                           <h4>
                              50
                              <i class="mdi mdi-chevron-up ms-1 text-success"></i>
                           </h4>
                           <div class="d-flex">
                              <span :class="`badge badge-soft-success font-size-12`">12%</span>

                           </div>
                        </div>
                     </BCardBody>
                  </BCard>
               </BCol>
               <BCol sm="4">
                  <BCard no-body>
                     <BCardBody>
                        <div class="d-flex align-items-center mb-3">
                           <div class="avatar-xs me-3">
                              <span class="
                        avatar-title
                        rounded-circle
                        bg-primary-subtle
                        text-primary
                        font-size-18
                      ">
                                 <i :class="`bx bx-receipt`"></i>
                              </span>
                           </div>
                           <h5 class="font-size-14 mb-0">Total</h5>
                        </div>
                        <div class="text-muted mt-4">
                           <h4>
                              50
                              <i class="mdi mdi-chevron-up ms-1 text-success"></i>
                           </h4>
                           <div class="d-flex">
                              <span :class="`badge badge-soft-success font-size-12`">30%</span>

                           </div>
                        </div>
                     </BCardBody>
                  </BCard>
               </BCol>
            </BRow>
         </BCol></BRow>
    </div>

  </Layout>
</template>

