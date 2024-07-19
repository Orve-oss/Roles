<script>
// import { MetisMenu } from 'metismenujs';

import { menuItems } from "./menu";
import { filterMenuByRole } from "./filterMenu";

/**
 * Sidenav component
 */
// export default {
//   data() {
//     return {
//       menuItems: menuItems,
//       menuData: null,
//     };
//   },
//   mounted: function () {
//     if (document.getElementById("side-menu")) new MetisMenu("#side-menu");
//     var links = document.getElementsByClassName("side-nav-link-ref");
//     var matchingMenuItem = null;
//     const paths = [];

//     for (var i = 0; i < links.length; i++) {
//       paths.push(links[i]["pathname"]);
//     }
//     var itemIndex = paths.indexOf(window.location.pathname);
//     if (itemIndex === -1) {
//       const strIndex = window.location.pathname.lastIndexOf("/");
//       const item = window.location.pathname.substr(0, strIndex).toString();
//       matchingMenuItem = links[paths.indexOf(item)];
//     } else {
//       matchingMenuItem = links[itemIndex];
//     }
//     if (matchingMenuItem) {
//       matchingMenuItem.classList.add("active");
//       var parent = matchingMenuItem.parentElement;


//       if (parent) {
//         parent.classList.add("mm-active");
//         const parent2 = parent.parentElement.closest("ul");
//         if (parent2 && parent2.id !== "side-menu") {
//           parent2.classList.add("mm-show");

//           const parent3 = parent2.parentElement;
//           if (parent3) {
//             parent3.classList.add("mm-active");
//             var childAnchor = parent3.querySelector(".has-arrow");
//             var childDropdown = parent3.querySelector(".has-dropdown");
//             if (childAnchor) childAnchor.classList.add("mm-active");
//             if (childDropdown) childDropdown.classList.add("mm-active");

//             const parent4 = parent3.parentElement;
//             if (parent4 && parent4.id !== "side-menu") {
//               parent4.classList.add("mm-show");
//               const parent5 = parent4.parentElement;
//               if (parent5 && parent5.id !== "side-menu") {
//                 parent5.classList.add("mm-active");
//                 const childanchor = parent5.querySelector(".is-parent");
//                 if (childanchor && parent5.id !== "side-menu") {
//                   childanchor.classList.add("mm-active");
//                 }
//               }
//             }
//           }
//         }
//       }
//     }
//   },
//   methods: {

//     hasItems(item) {
//       return item.subItems !== undefined ? item.subItems.length > 0 : false;
//     },

//     toggleMenu(event) {
//       event.currentTarget.nextElementSibling.classList.toggle("mm-show");
//     },
//   },
// };
export default {
  data() {
    return {
      menuItems: menuItems,
      userRole: null, // Will be dynamically set based on the authenticated user
    };
  },
  computed: {
    filteredMenuItems() {
      return filterMenuByRole(this.menuItems, this.userRole);
    },
  },
  created() {
    const userRole = localStorage.getItem('userRole'); // Assurez-vous d'avoir stocké le rôle de l'utilisateur lors de la connexion
    if (userRole) {
      this.userRole = userRole;
    }
  },
  methods: {
    hasItems(item) {
      return item.subItems && item.subItems.length > 0;
    },
  },
};
</script>

<template>
    <div id="sidebar-menu">
      <ul id="side-menu" class="metismenu list-unstyled">
        <template v-for="item in filteredMenuItems" :key="item.id">
          <li class="menu-title" v-if="item.isTitle">
            {{ $t(item.label) }}
          </li>
          <li v-if="!item.isTitle && !item.isLayout">
            <BLink v-if="hasItems(item)" href="javascript:void(0);" class="is-parent"
              :class="{ 'has-arrow': !item.badge, 'has-dropdown': item.badge }">
              <i :class="`bx ${item.icon}`" v-if="item.icon"></i>
              <span>{{ $t(item.label) }}</span>
              <span :class="`badge rounded-pill bg-${item.badge.variant} float-end`" v-if="item.badge">{{ $t(item.badge.text) }}</span>
            </BLink>

            <router-link :to="item.link" v-if="!hasItems(item)" class="side-nav-link-ref">
              <i :class="`bx ${item.icon}`" v-if="item.icon"></i>
              <span>{{ $t(item.label) }}</span>
              <span :class="`badge rounded-pill bg-${item.badge.variant} float-end`" v-if="item.badge">{{ $t(item.badge.text) }}</span>
            </router-link>

            <ul v-if="hasItems(item)" class="sub-menu" aria-expanded="false">
              <li v-for="(subitem, index) in item.subItems" :key="index">
                <router-link :to="subitem.link" v-if="!hasItems(subitem)" class="side-nav-link-ref">
                  <span>{{ $t(subitem.label) }}</span>
                  <span :class="`badge rounded-pill bg-${subitem.badge.variant} float-end`" v-if="subitem.badge">{{ $t(subitem.badge.text) }}</span>
                </router-link>
                <BLink v-if="hasItems(subitem)" class="side-nav-link-a-ref has-arrow" href="javascript:void(0);">{{ $t(subitem.label) }}</BLink>
                <ul v-if="hasItems(subitem)" class="sub-menu mm-collapse" aria-expanded="false">
                  <li v-for="(subSubitem, index) in subitem.subItems" :key="index">
                    <router-link :to="subSubitem.link" class="side-nav-link-ref">{{ $t(subSubitem.label) }}</router-link>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
        </template>
      </ul>
    </div>
  </template>

<!-- <template>

  <div id="sidebar-menu">

    <ul id="side-menu" class="metismenu list-unstyled">
      <template v-for="item in menuItems">
        <li class="menu-title" v-if="item.isTitle" :key="item.id">
          {{ $t(item.label) }}
        </li>
        <li v-if="!item.isTitle && !item.isLayout" :key="item.id">
          <BLink v-if="hasItems(item)" href="javascript:void(0);" class="is-parent"
            :class="{ 'has-arrow': !item.badge, 'has-dropdown': item.badge }">
            <i :class="`bx ${item.icon}`" v-if="item.icon"></i>
            <span>{{ $t(item.label) }}</span>
            <span :class="`badge rounded-pill bg-${item.badge.variant} float-end`" v-if="item.badge">{{
              $t(item.badge.text) }}</span>
          </BLink>

          <router-link :to="item.link" v-if="!hasItems(item)" class="side-nav-link-ref">
            <i :class="`bx ${item.icon}`" v-if="item.icon"></i>
            <span>{{ $t(item.label) }}</span>
            <span :class="`badge rounded-pill bg-${item.badge.variant} float-end`" v-if="item.badge">{{
              $t(item.badge.text) }}</span>
          </router-link>

          <ul v-if="hasItems(item)" class="sub-menu" aria-expanded="false">
            <li v-for="(subitem, index) of item.subItems" :key="index">
              <router-link :to="subitem.link" v-if="!hasItems(subitem)" class="side-nav-link-ref">

                <span>{{ $t(subitem.label) }}</span>
                <span :class="`badge rounded-pill bg-${subitem.badge.variant} float-end`" v-if="subitem.badge">{{
                  $t(subitem.badge.text) }}</span>

              </router-link>
              <BLink v-if="hasItems(subitem)" class="side-nav-link-a-ref has-arrow" href="javascript:void(0);">{{
                $t(subitem.label) }}</BLink>
              <ul v-if="hasItems(subitem)" class="sub-menu mm-collapse" aria-expanded="false">
                <li v-for="(subSubitem, index) of subitem.subItems" :key="index">
                  <router-link :to="subSubitem.link" class="side-nav-link-ref">{{ $t(subSubitem.label) }}</router-link>
                </li>
              </ul>
            </li>
          </ul>
        </li>
      </template>
    </ul>
  </div>

</template> -->
