<script>
import Layout from "../../layouts/main";
import PageHeader from "@/components/page-header";
import profile from "@/assets/images/profile-img.png"
import { avatar1 } from "../../assets/images/users/data"


/**
 * Contacts-Profile component
 */
export default {
  components: { Layout, PageHeader},
  data() {
    return {
      profile, avatar1,
    };
  }
};
</script>

<template>
  <Layout>
    <PageHeader title="Profil " pageTitle="Profil" />

    <BRow>
      <BCol xl="4">
        <BCard no-body class="overflow-hidden">
          <BCardBody class="pt-0">
            <BRow>
              <BCol sm="4">
                <div class="avatar-md profile-user-wid mb-4">
                  <img :src=avatar1 alt class="img-thumbnail rounded-circle" />
                </div>
                <h5 class="font-size-15 text-truncate">Veronique </h5>
                <p class="text-muted mb-0 text-truncate">Client </p>
              </BCol>
            </BRow>
          </BCardBody>
        </BCard>

        <BCard no-body>
          <BCardBody class="pb-0">
            <BCardTitle class="mb-4">Personal Information</BCardTitle>
            <div class="table-responsive ">
              <BTableSimple class="table-nowrap mb-0">
                <BTbody>
                  <BTr>
                    <BTh scope="row">Nom :</BTh>
                    <BTd>Véronique</BTd>
                  </BTr>
                  <BTr>
                    <BTh scope="row">E-mail :</BTh>
                    <BTd>vmagnouwai@gmail.com</BTd>
                  </BTr>
                  <BTr>
                    <BTh scope="row">Membre:</BTh>
                    <BTd>California, United States</BTd>
                  </BTr>
                </BTbody>
              </BTableSimple>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
  </Layout>
</template>
