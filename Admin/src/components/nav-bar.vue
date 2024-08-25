<script>
import axios from "axios";
// import simplebar from "simplebar-vue";
import { avatar3, avatar4, avatar1 } from "@/assets/images/users/data"
import { useAuthStore } from '@/state/pinia/auth'
import userService from "../../services/userService";
import { useRouter } from 'vue-router';
const auth = useAuthStore()

import i18n from "../i18n";

/**
 * Nav-bar Component
 */
export default {
    setup() {
        const router = useRouter();
        const auth = useAuthStore()

        const handleLogout = () => {
            auth.logOut();
            const redirectLink = getLogoutLink();
            router.push(redirectLink);
        };

        const getLogoutLink = () => {
            const role = auth.currentUser ? auth.currentUser.role[0] : null;
            console.log(role);
            if (role === 'Admin') {
                return '/';
            } else if (role === 'Client') {
                return '/accueil';
            }
            else if (role === 'Agent') {
                return '/accueil/agent';
            } else {
                return '/';
            }
        };

        return {
            handleLogout,
        };
    },

    data() {
        return {
            lang: "en",
            lan: i18n.locale,
            text: null,
            flag: null,
            value: null,
            avatar3, avatar4, avatar1,
            languages: [
                {
                    flag: require("@/assets/images/flags/us.jpg"),
                    language: "en",
                    title: "English",
                },
                {
                    flag: require("@/assets/images/flags/french.jpg"),
                    language: "fr",
                    title: "French",
                },
                {
                    flag: require("@/assets/images/flags/spain.jpg"),
                    language: "es",
                    title: "Spanish",
                },
                {
                    flag: require("@/assets/images/flags/chaina.png"),
                    language: "zh",
                    title: "Chinese",
                },
                {
                    flag: require("@/assets/images/flags/arabic.png"),
                    language: "ar",
                    title: "Arabic",
                },
            ],
            locales: ["fr", "en", "ar"],
            role: '',
            image: '',
            initial: '',
            displayName: '',
        };
    },
    //   components: { simplebar },
    //   mounted() {
    //     if (process.env.VUE_APP_I18N_LOCALE) {
    //       this.flag = this.$i18n.locale || process.env.VUE_APP_I18N_LOCALE;
    //       this.languages.forEach((item) => {
    //         if (item.language == this.flag) {
    //           document.getElementById("header-lang-img")?.setAttribute("src", item.flag);
    //         }
    //       });
    //     }
    //   },
    computed: {
        currentUser() {
            return auth.currentUser
        },

        getProfileLink() {
            // const roleName = this.role?.roles?.[0]?.name;
            // console.log(roleName);
            if (this.role[0] === 'Admin' || this.role[0] === 'Agent') {
                return '/profileUser';
            } else if (this.role[0] === 'Client') {
                return '/profileClient';
            } else {
                return '/';
            }
        },

    },
    mounted() {
        this.loadUserProfile();
        const user = JSON.parse(localStorage.getItem('user'));
        this.role = user ? user.role : '';
        console.log(this.role[0]);
    },
    methods: {
        toggleMenu() {
            this.$parent.toggleMenu();
        },
        toggleRightSidebar() {
            this.$parent.toggleRightSidebar();
        },
        initFullScreen() {
            document.body.classList.toggle("fullscreen-enable");
            if (
                !document.fullscreenElement &&
        /* alternative standard method */ !document.mozFullScreenElement &&
                !document.webkitFullscreenElement
            ) {
                // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(
                        Element.ALLOW_KEYBOARD_INPUT
                    );
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        },

        setLanguage(locale, country, flag) {
            this.lan = locale;
            this.text = country;
            this.flag = flag;
            document.getElementById("header-lang-img").setAttribute("src", flag);
            this.$i18n.locale = locale
        },

        logoutUser() {
            // eslint-disable-next-line no-unused-vars
            axios.get("http://127.0.0.1:8000/api/logout").then((res) => {
                this.$router.push({
                    name: "default",
                });
            });
        },
        loadUserProfile() {
            const role = localStorage.getItem('userRole');
            console.log(role);
            if (role === 'Client') {

                userService.fetchClientProfile().then(profile => {
                    this.image = profile.image;
                    this.initial = profile.initial;
                    this.displayName = profile.displayName;
                }).catch(err => {
                    console.error(err);
                });

            } else if (role === 'Admin' || role === 'Agent') {
                userService.fetchAdminProfile().then(profile => {
                    this.image = profile.image;
                    this.initial = profile.initial;
                    this.displayName = profile.displayName;
                }).catch(err => {
                    console.error(err);
                });

            } else {
                console.log('erreur');
            }
        }
    },
};
</script>

<template>
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <router-link to="/" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="@/assets/images/logo1.png" alt height="22" />
                        </span>
                        <span class="logo-lg">
                            <img src="@/assets/images/logo1.png" alt height="17" />
                        </span>
                    </router-link>
                    <router-link to="/" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="@/assets/images/logo1.png" alt height="22" />
                        </span>
                        <span class="logo-lg">
                            <img src="@/assets/images/logo1.png" alt height="30" />
                        </span>
                    </router-link>


                </div>

                <BButton variant="white" id="vertical-menu-btn" type="button"
                    class="btn btn-sm px-3 font-size-16 header-item" @click="toggleMenu">
                    <i class="fa fa-fw fa-bars"></i>
                </BButton>



            </div>

            <div class="d-flex">
                <BDropdown class="d-inline-block d-lg-none ms-2 job-list-dropdown" variant="black"
                    menu-class="dropdown-menu-lg p-0" toggle-class="header-item noti-icon" right>
                    <template v-slot:button-content>
                        <i class="mdi mdi-magnify"></i>
                    </template>

                    <BForm class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..."
                                    aria-label="Recipient's username" />
                                <div class="input-group-append">
                                    <BButton variant="primary" type="submit">
                                        <i class="mdi mdi-magnify"></i>
                                    </BButton>
                                </div>
                            </div>
                        </div>
                    </BForm>
                </BDropdown>

                <BDropdown right variant="black" toggle-class="header-item" menu-class="dropdown-menu-end">
                    <template v-slot:button-content>
                        <div>
                            <img v-if="image" class="rounded-circle header-profile-user" :src="image"
                                alt="Header Avatar" />
                            <div v-else class="rounded-circle header-profile-user profile-initial-wrapper"> {{ initial
                                }}</div>
                        </div>


                        <span class="d-none d-xl-inline-block ms-1">
                            <div v-if="currentUser">
                                {{ currentUser.displayName }}
                            </div>
                            <!-- <div v-else>Orve</div> -->
                        </span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </template>
                    <BDropdownItem>
                        <router-link :to="getProfileLink" v-slot="{ navigate }">
                            <span @click="navigate" @keypress.enter="navigate" class="text-body">
                                <i class="bx bx-user font-size-16 align-middle me-1"></i>
                                {{ $t("navbar.dropdown.henry.list.profile") }}
                            </span>
                        </router-link>
                    </BDropdownItem>
                    <BDropdownDivider></BDropdownDivider>
                    <BDropdownItem @click="handleLogout" class="dropdown-item text-danger">
                        <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                        {{ $t("navbar.dropdown.henry.list.logout") }}
                    </BDropdownItem>
                </BDropdown>

                <!-- <div class="dropdown d-inline-block">
          <BButton variant="white" type="button" class="btn header-item noti-icon right-bar-toggle toggle-right" @click="toggleRightSidebar">
            <i class="bx bx-cog bx-spin toggle-right"></i>
          </BButton>
        </div> -->
            </div>
        </div>
    </header>
</template>
<style>
.profile-initial-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
    color: #555;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 24px;
    font-weight: bold;
    text-align: center;
}
</style>
