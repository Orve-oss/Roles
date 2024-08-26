<script>
import Layout from "../../layouts/main";
import axios from "axios";
import Swal from "sweetalert2";

/**
 * Starter component
 */
export default {
    components: { Layout },
    data() {
        return {
            image: '',
            user: {
                name: '',
                email: '',
                role: '',
            },
            newProfileImage: null,
            profileImageInitial: '',

        };
    },
    mounted() {
        this.fetchUserProfile();
    },
    methods: {
        fetchUserProfile() {
            const token = localStorage.getItem('authToken');
            if (token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }

            axios.get(`http://127.0.0.1:8000/api/user-profile`, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
                .then((res) => {
                    this.user = res.data.user;
                    console.log(this.user);
                    this.image = res.data.image || '';
                    this.profileImageInitial = this.user.name.charAt(0).toUpperCase();//récupérer la première lettre du nom en majuscule

                })
                .catch((err) => {
                    console.log(err);
                });
        },
        handleImage(){
            this.$refs.profileImageInput.click();
        },
        onFileChange(event){
            this.newProfileImage = event.target.files[0];
            const reader = new FileReader();
            reader.onload = e =>{
                this.image = e.target.result;
            };
            reader.readAsDataURL(this.newProfileImage);
        },
        updateUserProfile(){
            const formData = new FormData();
            formData.append('name', this.user.name);
            formData.append('email', this.user.email);
            formData.append('role', this.user.role);
            if (this.newProfileImage) {
                formData.append('image', this.newProfileImage);
            }
            axios.post(`http://127.0.0.1:8000/api/updateUser/profil`, formData)
            .then(response => {
                Swal.fire(
                    'Mis à jour!',
                    response.data.message,
                    'success'
                );
                this.fetchUserProfile();
            })
            .catch(error => {
                console.error(error);
            });
        }
    }
};
</script>
<template>
    <Layout>
        <BRow>
            <BCol lg="12">
                <BCard no-body class="mx-n0 mt-n4">
                    <div class="text-center mb-4">
                        <div v-if="image" @click="handleImage">
                            <img :src="image" alt="" class="avatar-md rounded-circle mx-auto d-block"
                            >
                        </div>
                        <div v-else @click="handleImage">
                            <span class="profile-initial-wrapper avatar-md rounded-circle mx-auto d-block">{{ profileImageInitial }}</span>
                        </div>

                        <!-- <i class="fa fa-camera" aria-hidden="true" @click="handleImage"></i> -->
                        <h5 class="mt-3 mb-1">{{ user.name }}</h5>
                        <p class="text-muted mb-3">{{ user.role }}</p>
                        <input type="file" ref="profileImageInput" style="display: none;" @change="onFileChange">
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
                                    <i class='bx bx-user font-size-18 text-primary'></i>
                                    <div class="ms-3">
                                        <h6 class="mb-1 fw-semibold">Membre depuis le:</h6>
                                        {{new Date(user.created_at).toLocaleDateString()}}
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
                        <BForm @submit.prevent="updateUserProfile">
                            <BRow>
                                <BCol sm="10">
                                    <div class="mb-3">
                                        <BFormGroup label="Nom">
                                            <BFormInput type="text" v-model="user.name" placeholder="Entrez un nom" />
                                        </BFormGroup>
                                    </div>
                                    <div class="mb-3">
                                        <BFormGroup label="Email">
                                            <BFormInput type="email" v-model="user.email" placeholder="Entrez un mail" />
                                        </BFormGroup>
                                    </div>
                                    
                                </BCol>
                            </BRow>

                            <div class="mt-2">
                                <BButton variant="primary" class="me-1">
                                    Sauvegarder
                                </BButton>
                            </div>
                        </BForm>
                    </BCardBody>
                </BCard>
            </BCol>
        </BRow>
    </Layout>
</template>
<style scoped>
.profile-image-wrapper img {
    cursor: pointer;
}

.profile-initial-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
    color: #555;
    margin-top: 20px;
    font-size: 36px; /* Augmenter la taille de la lettre */
    font-weight: bold;
    cursor: pointer;
    width: 60px; /* Ajuster selon la taille souhaitée */
    height: 60px; /* Ajuster selon la taille souhaitée */
    border-radius: 50%; /* Pour un effet circulaire */
    text-align: center;
}

.profile-initial-wrapper span {
    display: block;
}
</style>
