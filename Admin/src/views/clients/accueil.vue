<script>
import Acuueil from "@/assets/images/Acuueil.jpg"
import logo from "@/assets/images/logo.jpg"

// import { Autoplay } from "swiper";
import "swiper/css";
import "swiper/css/autoplay";
import axios from "axios";
import { required, email, helpers } from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core"

export default {
   name: "HomePage",

   setup() {
      return {
         v$: useVuelidate(),
      };
   },
   data() {
      return {
         showModal: true,
         Acuueil,
         logo,
         email: '',
         password: '',
         password_confirmation: '',
         submitted: false,
      };
   },
   validations: {
      email: {
         required: helpers.withMessage("Email is required", required),
         email: helpers.withMessage("Please enter valid email", email),
      },
      password: {
         required: helpers.withMessage("Password is required", required),
      },
      password_confirmation: {
         required: helpers.withMessage("Confirm password is required", required),

      },
   },
   methods: {
      changePassword() { },
      tryToResetpwd() {
         this.submitted = true;

         this.v$.$touch();

         if (this.v$.$invalid) {
            return;
         } else {
            axios
               .post(`http://127.0.0.1:8000/api/reset-password`, {
                  email: this.email,
                  password: this.password,
                  password_confirmation: this.password_confirmation,
               })
               .then((res) => {
                  if (res.data.status === 200) {
                     alert('Compte activé avec succès!');
                     this.$router.push('/');
                  }
               });
         }
      },
   }

};
</script>

<template>
   <div>
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
         <div class="container">
            <a class="navbar-brand" href="#">
               <img :src="logo" alt="Logo" height="40">
            </a>
            <BButton class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
               aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </BButton>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
               <ul class="navbar-nav ml-auto">
                  <li class="nav-item active">
                     <a class="nav-link" href="#">Accueil</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" href="#">About</a>
                  </li>
                  <li class="nav-item dropdown">
                     <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        More Pages
                     </a>
                     <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Left Sidebar</a>
                        <a class="dropdown-item" href="#">Right Sidebar</a>
                     </div>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" href="#">Contact</a>
                  </li>
                  <li class="nav-item">
                     <a class="btn btn-primary" href="#">SIGN IN / SIGN UP</a>
                  </li>
               </ul>
            </div>
         </div>
      </nav>

      <!-- Header -->
      <header id="head" class="text-center" :style="{ backgroundImage: `url(${Acuueil})` }">
         <!-- <img :src="Acuueil" alt="Background Image" class="img-fluid" /> -->
         <div class="container">
            <div class="row">
               <div class="col-lg-12">
                  <h1 class="display-4">Comment pouvons nous vous aider?</h1>
                  <p>
                     <a class="btn btn-secondary btn-lg" role="button">Plus d'infos</a>
                     <a class="btn btn-primary btn-lg" role="button">Espace Client</a>
                  </p>
               </div>
            </div>
         </div>
      </header>

      <!-- Services Section -->
      <section class="services text-center my-5">
         <div class="container">
            <h2 class="font-weight-light">Nos services</h2>
            <p class="text-muted">Parcourez ici nos services</p>
            <Layout>
               <BRow>
                  <BCol xl="8">
                     <BRow>
                        <BCol sm="4" >
                           <BCard no-body :style="{height: '400px'}">
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
                                    <h5 class="font-size-14 mb-0">Google workspace</h5>
                                 </div>
                                 <div class="text-muted mt-4">

                                    <div class="d-flex">

                                       <span class="ms-2 text-truncate">Découvrez les produits et
                                          service</span>
                                    </div>
                                 </div>
                              </BCardBody>
                           </BCard>
                        </BCol>
                        <BCol sm="4" >
                           <BCard no-body :style="{height: '400px'}">
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
                                    <h5 class="font-size-14 mb-0">Google workspace</h5>
                                 </div>
                                 <div class="text-muted mt-4">

                                    <div class="d-flex">

                                       <span class="ms-2 text-truncate">Découvrez les produits et
                                          service</span>
                                    </div>
                                 </div>
                              </BCardBody>
                           </BCard>
                        </BCol>
                        <BCol sm="4">
                           <BCard no-body :style="{height: '400px'}">
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
                                    <h5 class="font-size-14 mb-0">Google workspace</h5>
                                 </div>
                                 <div class="text-muted mt-4">

                                    <div class="d-flex">

                                       <span class="ms-2 text-truncate">Découvrez les produits et
                                          service</span>
                                    </div>
                                 </div>
                              </BCardBody>
                           </BCard>
                        </BCol>

                     </BRow>

                  </BCol>

               </BRow>
            </Layout>
            <!-- <div class="row">
                    <div class="col-md-4">
                        <div class="service-item">
                            <h4>Service 1</h4>
                            <p>Description of Service 1</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="service-item">
                            <h4>Service 2</h4>
                            <p>Description of Service 2</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="service-item">
                            <h4>Service 3</h4>
                            <p>Description of Service 3</p>
                        </div>
                    </div>
                </div> -->
         </div>
         <BModal v-model="showModal" hide-footer  :no-close-on-backdrop="true">


            <div class="p-2">
               <BAlert :model-value="true" variant="success" class="text-center mb-4">
                  Veuillez changer de mot de passe
                </BAlert>
               <BForm class="form-horizontal" @submit.prevent="tryToResetpwd">
                  <BFormGroup>
                     <label for="useremail">Email</label>
                     <BFormInput class="mb-2" v-model="email" id="useremail" placeholder="Enter email"
                        :class="{ 'is-invalid': submitted && v$.email.$error }" aria-disabled="true" />
                     <div v-for="(item, index) in v$.email.$errors" :key="index" class="invalid-feedback">
                        <span v-if="item.$message">{{ item.$message }}</span>
                     </div>
                  </BFormGroup>

                  <BFormGroup>
                     <label for="pwd">Password</label>
                     <BFormInput class="mb-2" type="password" v-model="password" id="pwd" placeholder="Enter password"
                        :class="{
                           'is-invalid': submitted && v$.password.$error,
                        }" />
                     <div v-if="submitted && v$.password.$error" class="invalid-feedback">
                        <span v-if="v$.password.required.$message">{{
                           v$.password.required.$message
                        }}</span>
                     </div>
                  </BFormGroup>

                  <BFormGroup>
                     <label for="confirm_pwd">Confirm Password</label>
                     <BFormInput class="mb-2" v-model="password_confirmation" type="password" id="confirm_pwd"
                        placeholder="Enter confirm password" :class="{
                           'is-invalid':
                              submitted && v$.password_confirmation.$error,
                        }" />
                     <div v-if="submitted && v$.password_confirmation.$error" class="invalid-feedback">
                        <span v-if="v$.password_confirmation.required.$message">{{
                           v$.password_confirmation.required.$message
                        }}</span>
                        <!-- <span v-if="v$.password_confirmation.sameAsPassword.$message">
                                                {{ v$.password_confirmation.sameAsPassword.$message }}
                                            </span> -->
                     </div>
                  </BFormGroup>

                  <div class="form-group row mb-0">
                     <BCol cols="12" class="text-end">
                        <BButton variant="primary" class="w-md" type="submit">
                           Activez
                        </BButton>
                     </BCol>
                  </div>

               </BForm>
            </div>







         </BModal>
      </section>

      <!-- Footer -->
      <footer class="footer-expand-lg bg-dark text-center py-4">
         <div class="container">
            <p>Contact us: <a href="mailto:info@example.com">info@example.com</a></p>

         </div>
      </footer>
   </div>
</template>



<style scoped>
/* Add your custom styles here */
.header-background {
   position: relative;
   height: 100vh;
   overflow: hidden;
}

.header-background img {
   width: 100%;
   height: 100%;
   object-fit: cover;
}

#head {
   color: white;
   padding: 150px 0;
   text-align: center;
}

.header-content {
   position: absolute;
   top: 50%;
   left: 50%;
   transform: translate(-50%, -50%);
   color: white;
   text-shadow: 0px 0px 10px rgba(0, 0, 0, 0.7);
}

.services .service-item {
   padding: 20px;
   border: 1px solid #ddd;
   border-radius: 5px;
   margin-bottom: 20px;
}

.footer {
   background-color: #f8f9fa;
   color: #6c757d;
}
</style>
