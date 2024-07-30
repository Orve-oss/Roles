<script>
import Layout from "../../layouts/main";

/**
 * Starter component
 */
export default {
   components: { Layout },
   data() {
      return {
         profileImage: '@/assets/images/users/avatar-6.jpg',

      };
   }
};
</script>
<template>
   <Layout>
      <BRow>
         <BCol lg="12">
            <BCard no-body class="mx-n0 mt-n4">
               <div class="text-center mb-4">
                  <img :src="profileImage" alt="" class="avatar-md rounded-circle mx-auto d-block"
                     @click="handleImageClick">
                  <i class="fa fa-camera" aria-hidden="true" @click="handleImageClick"></i>
                  <h5 class="mt-3 mb-1">Steven Franklin</h5>
                  <p class="text-muted mb-3">UI/UX Designer</p>
               </div>
            </BCard>
         </BCol>
      </BRow>
      <BRow>
         <BCol lg="3">
            <BCard no-body>
               <BCardBody>
                  <ul class="list-unstyled vstack gap-3 mb-0">
                     <li>
                        <div class="d-flex">
                           <i class='bx bx-calendar font-size-18 text-primary'></i>
                           <div class="ms-3">
                              <h6 class="mb-1 fw-semibold">Experience:</h6>
                              <span class="text-muted">2+ Years</span>
                           </div>
                        </div>
                     </li>
                     <li>
                        <div class="d-flex">
                           <i class='bx bx-money font-size-18 text-primary'></i>
                           <div class="ms-3">
                              <h6 class="mb-1 fw-semibold">Current Salary:</h6>
                              <span class="text-muted">$ 3451</span>
                           </div>
                        </div>
                     </li>
                     <li>
                        <div class="d-flex">
                           <i class='bx bx-money font-size-18 text-primary'></i>
                           <div class="ms-3">
                              <h6 class="mb-1 fw-semibold">Expected Salary:</h6>
                              <span class="text-muted">$ 4000+</span>
                           </div>
                        </div>
                     </li>
                     <li>
                        <div class="d-flex">
                           <i class='bx bx-user font-size-18 text-primary'></i>
                           <div class="ms-3">
                              <h6 class="mb-1 fw-semibold">Gender:</h6>
                              Male
                           </div>
                        </div>
                     </li>
                     <li>
                        <div class="d-flex">
                           <i class='mdi mdi-book-education font-size-18 text-primary'></i>
                           <div class="ms-3">
                              <h6 class="mb-1 fw-semibold">Qualification:</h6>
                              <span class="text-muted">Master Degree</span>
                           </div>
                        </div>
                     </li>
                     <li>
                        <div class="d-flex">
                           <i class='mdi mdi-google-translate font-size-18 text-primary'></i>
                           <div class="ms-3">
                              <h6 class="mb-1 fw-semibold">Languages:</h6>
                              <span class="text-muted">English, France</span>
                           </div>
                        </div>
                     </li>
                  </ul>
               </BCardBody>
            </BCard>
         </BCol>
         <BCol lg="9">
            <BCard no-body>
               <BCardBody>
                  <h5 class="mb-3">Informations</h5>
                  <BForm>
                     <BRow>
                        <BCol sm="10">
                           <div class="mb-3">
                              <BFormGroup label="Nom">
                                 <BFormInput type="text" placeholder="Entrez un nom" />
                              </BFormGroup>
                           </div>
                           <div class="mb-3">
                              <BFormGroup label="Email">
                                 <BFormInput type="email" placeholder="Entrez un mail" />
                              </BFormGroup>
                           </div>
                           <div class="mb-3">
                              <BFormGroup label="Role">
                                 <BFormInput type="text" placeholder="Entrez un role" />
                              </BFormGroup>
                           </div>
                        </BCol>
                     </BRow>

                     <div class="mt-2">
                        <BButton variant="primary" class="me-1">
                           Save Changes
                        </BButton>
                     </div>
                  </BForm>
               </BCardBody>
            </BCard>
         </BCol>
      </BRow>
   </Layout>
</template>
