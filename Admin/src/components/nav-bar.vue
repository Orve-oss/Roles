<script>
import axios from "axios";
import simplebar from "simplebar-vue";
import { avatar3, avatar4, avatar1 } from "@/assets/images/users/data"
import { useAuthStore } from '@/state/pinia'
const auth = useAuthStore()

import i18n from "../i18n";

/**
 * Nav-bar Component
 */
export default {
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
      locales: ["fr", "en", "ar"]
    };
  },
  components: { simplebar },
  mounted() {
    if (process.env.VUE_APP_I18N_LOCALE) {
      this.flag = this.$i18n.locale || process.env.VUE_APP_I18N_LOCALE;
      this.languages.forEach((item) => {
        if (item.language == this.flag) {
          document.getElementById("header-lang-img")?.setAttribute("src", item.flag);
        }
      });
    }
  },
  computed: {
    currentUser() {
      return auth.currentUser
    }
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
              <img src="@/assets/images/logo.svg" alt height="22" />
            </span>
            <span class="logo-lg">
              <img src="@/assets/images/logo-dark.png" alt height="17" />
            </span>
          </router-link>

          <router-link to="/" class="logo logo-light">
            <span class="logo-sm">
              <img src="@/assets/images/logo-light.svg" alt height="22" />
            </span>
            <!-- <span class="logo-lg">
              <img src="@/assets/images/logo-light.png" alt height="19" />
            </span> -->
          </router-link>
        </div>

        <BButton variant="white" id="vertical-menu-btn" type="button" class="btn btn-sm px-3 font-size-16 header-item" @click="toggleMenu">
          <i class="fa fa-fw fa-bars"></i>
        </BButton>

        <!-- App Search-->
        <BForm class="app-search d-none d-lg-block">
          <div class="position-relative">
            <input type="text" class="form-control" :placeholder="$t('navbar.search.text')" />
            <span class="bx bx-search-alt"></span>
          </div>
        </BForm>

       ;
      </div>

      <div class="d-flex">
        <BDropdown class="d-inline-block d-lg-none ms-2 job-list-dropdown" variant="black" menu-class="dropdown-menu-lg p-0" toggle-class="header-item noti-icon" right>
          <template v-slot:button-content>
            <i class="mdi mdi-magnify"></i>
          </template>

          <BForm class="p-3">
            <div class="form-group m-0">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username" />
                <div class="input-group-append">
                  <BButton variant="primary" type="submit">
                    <i class="mdi mdi-magnify"></i>
                  </BButton>
                </div>
              </div>
            </div>
          </BForm>
        </BDropdown>

        <BDropdown right menu-class="dropdown-menu-lg p-0 dropdown-menu-end" toggle-class="header-item noti-icon" variant="black">
          <template v-slot:button-content>
            <i class="bx bx-bell bx-tada"></i>
            <span class="badge bg-danger rounded-pill">{{
              $t("navbar.dropdown.notification.badge")
            }}</span>
          </template>

          <div class="p-3">
            <BRow class="align-items-center">
              <BCol>
                <h6 class="m-0">
                  {{ $t("navbar.dropdown.notification.text") }}
                </h6>
              </BCol>
              <div class="col-auto">
                <BLink href="#" class="small">{{
                  $t("navbar.dropdown.notification.subtext")
                }}</BLink>
              </div>
            </BRow>
          </div>
          <simplebar style="max-height: 230px">
            <BLink href="javascript: void(0);" class="text-reset notification-item">
              <div class="d-flex">
                <div class="avatar-xs me-3">
                  <span class="avatar-title bg-primary rounded-circle font-size-16">
                    <i class="bx bx-cart"></i>
                  </span>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mt-0 mb-1">
                    {{ $t("navbar.dropdown.notification.order.title") }}
                  </h6>
                  <div class="font-size-12 text-muted">
                    <p class="mb-1">
                      {{ $t("navbar.dropdown.notification.order.text") }}
                    </p>
                    <p class="mb-0">
                      <i class="mdi mdi-clock-outline"></i>
                      {{ $t("navbar.dropdown.notification.order.time") }}
                    </p>
                  </div>
                </div>
              </div>
            </BLink>
            <BLink href="javascript: void(0);" class="text-reset notification-item">
              <div class="d-flex">
                <img :src=avatar3 class="me-3 rounded-circle avatar-xs" alt="user-pic" />
                <div class="flex-grow-1">
                  <h6 class="mt-0 mb-1">
                    {{ $t("navbar.dropdown.notification.james.title") }}
                  </h6>
                  <div class="font-size-12 text-muted">
                    <p class="mb-1">
                      {{ $t("navbar.dropdown.notification.james.text") }}
                    </p>
                    <p class="mb-0">
                      <i class="mdi mdi-clock-outline"></i>
                      {{ $t("navbar.dropdown.notification.james.time") }}
                    </p>
                  </div>
                </div>
              </div>
            </BLink>
            <BLink href="javascript: void(0);" class="text-reset notification-item">
              <div class="d-flex">
                <div class="avatar-xs me-3">
                  <span class="avatar-title bg-success rounded-circle font-size-16">
                    <i class="bx bx-badge-check"></i>
                  </span>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mt-0 mb-1">
                    {{ $t("navbar.dropdown.notification.item.title") }}
                  </h6>
                  <div class="font-size-12 text-muted">
                    <p class="mb-1">
                      {{ $t("navbar.dropdown.notification.item.text") }}
                    </p>
                    <p class="mb-0">
                      <i class="mdi mdi-clock-outline"></i>
                      {{ $t("navbar.dropdown.notification.item.time") }}
                    </p>
                  </div>
                </div>
              </div>
            </BLink>
            <BLink href="javascript: void(0);" class="text-reset notification-item">
              <div class="d-flex">
                <img :src=avatar4 class="me-3 rounded-circle avatar-xs" alt="user-pic" />
                <div class="flex-grow-1">
                  <h6 class="mt-0 mb-1">
                    {{ $t("navbar.dropdown.notification.salena.title") }}
                  </h6>
                  <div class="font-size-12 text-muted">
                    <p class="mb-1">
                      {{ $t("navbar.dropdown.notification.salena.text") }}
                    </p>
                    <p class="mb-0">
                      <i class="mdi mdi-clock-outline"></i>
                      {{ $t("navbar.dropdown.notification.salena.time") }}
                    </p>
                  </div>
                </div>
              </div>
            </BLink>
          </simplebar>
          <div class="p-2 border-top d-grid">
            <BLink class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
              <i class="mdi mdi-arrow-right-circle me-1"></i>
              {{ $t("navbar.dropdown.notification.button") }}
            </BLink>
          </div>
        </BDropdown>

        <BDropdown right variant="black" toggle-class="header-item" menu-class="dropdown-menu-end">
          <template v-slot:button-content>
            <img class="rounded-circle header-profile-user" :src="avatar1" alt="Header Avatar" />
            <span class="d-none d-xl-inline-block ms-1">
              <div v-if="currentUser">
                {{ currentUser.displayName }}
              </div>
              <div v-else>Orve</div>
            </span>
            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
          </template>
          <BDropdownItem>
            <router-link to="/contacts/profile" v-slot="{ navigate }">
              <span @click="navigate" @keypress.enter="navigate" class="text-body">
                <i class="bx bx-user font-size-16 align-middle me-1"></i>
                {{ $t("navbar.dropdown.henry.list.profile") }}
              </span>
            </router-link>
          </BDropdownItem>
          <BDropdownDivider></BDropdownDivider>
          <a href="/logout" class="dropdown-item text-danger">
            <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
            {{ $t("navbar.dropdown.henry.list.logout") }}
          </a>
        </BDropdown>

        <div class="dropdown d-inline-block">
          <BButton variant="white" type="button" class="btn header-item noti-icon right-bar-toggle toggle-right" @click="toggleRightSidebar">
            <i class="bx bx-cog bx-spin toggle-right"></i>
          </BButton>
        </div>
      </div>
    </div>
  </header>
</template>
