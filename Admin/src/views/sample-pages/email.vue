<script>
import axios from 'axios';

/**
 * Lock screen page -component
 */
export default {
    data(){
        return {
            email: ''
        };
    },
    methods:{
        sendEmail(){
            axios.post(`http://127.0.0.1:8000/api/sendVerification`)
            .then(response =>{
                console.log(response.data);
                this.$router.push('/auth/two-step-verification');
            }).catch(error =>{
                console.log(error);
            })
        }
    }

};
</script>

<template>
  <div>
    <div class="home-btn d-none d-sm-block">
      <router-link to="/" class="text-dark">
        <i class="mdi mdi-home-variant h2"></i>
      </router-link>
    </div>
    <div class="account-pages my-5 pt-5">
      <BContainer>
        <BRow class="justify-content-center">
          <BCol md="8" lg="6" xl="5">
            <BCard no-body class="overflow-hidden">
              <div class="bg-primary-subtle">
                <BRow>
                  <BCol cols="7">
                    <div class="text-primary p-4">
                      <h5 class="text-primary">Changer de mot de passe?</h5>
                      <p>Veuillez entrer votre email!</p>
                    </div>
                  </BCol>
                </BRow>
              </div>
              <BCardBody class="pt-0">
                <div>
                  <router-link to="/">
                    <div class="avatar-md profile-user-wid mb-4">
                      <span class="avatar-title rounded-circle bg-light">
                        <img src="@/assets/images/logo1.png" alt class="rounded-circle" height="20" />
                      </span>
                    </div>
                  </router-link>
                </div>
                <div class="p-2">
                  <BForm class="form-horizontal" @submit.prevent="sendEmail">

                    <div class="mb-3">
                      <label for="useremail">Email</label>
                      <BFormInput v-model="email" type="email" id="useremail" placeholder="Entrer votre email" />
                    </div>

                    <BRow class="mb-0">
                      <BCol cols="12" class="text-end">
                        <BButton variant="primary" class="px-4" type="submit">
                          Envoyer
                        </BButton>
                      </BCol>
                    </BRow>
                  </BForm>
                </div>
              </BCardBody>
            </BCard>

          </BCol>
        </BRow>
      </BContainer>
    </div>
  </div>
</template>
