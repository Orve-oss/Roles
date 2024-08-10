import { useAuthStore, useAuthFakeStore } from "@/state/pinia";
export default [
  {
    path: "/activite",
    name: "default",
    meta: { title: "Dashboard", authRequired: true, role: 'Admin' },
    component: () => import("../views/dashboards/activite")
  },
  {
    path: "/archive",
    name: "archive",
    meta: { title: "Archive", authRequired: true, role: 'Admin' },
    component: () => import("../views/users/archive")
  },
  {
    path: "/activite/agent",
    name: "statistiques",
    meta: { title: "tableau", authRequired: true, role: 'Agent' },
    component: () => import("../views/dashboards/activiteAgent")
  },
  {
    path: "/activite/client",
    name: "dashboard",
    meta: { title: "dashboard", authRequired: true, role: 'Client' },
    component: () => import("../views/dashboards/activiteClient")
  },
  {
    path: "/listuser",
    name: "listUser",
    meta: { title: "listUser", authRequired: true, role: 'Admin' },
    component: () => import("../views/users/listeUser")
  },
  {
    path: "/accueil",
    name: "Accueil Client",
    meta: { title: "Accueil Client", role: 'Client',
        beforeResolve(routeTo, routeFrom, next){
            const auth = useAuthStore();
            if (auth.loggedIn) {
                next({ name: auth.redirectRouteBasedOnRole()});
            } else {
                next();
            }
        }
    },
    component: () => import("../views/clients/accueil")
  },
  {
    path: "/",
    name: "AccueilUser",
    meta: { title: "Accueil Utilisateur", role: ['Admin', 'Agent'],
        beforeResolve(routeTo, routeFrom, next){
            const auth = useAuthStore();
            if (auth.loggedIn) {
                next({ name: auth.redirectRouteBasedOnRole()});
            } else {
                next();
            }
        }
    },
    component: () => import("../views/users/accueilUser")
  },
  {
    path: "/createUser",
    name: "createUsers",
    meta: { title: "createUsers", authRequired: true, role:'Admin' },
    component: () => import("../views/users/createUser")
  },
  {
    path: "/listticket",
    name: "allTicket",
    meta: { title: "allTicket", authRequired: true, role:'Admin' },
    component: () => import("../views/tickets/all-ticket")
  },
  {
    path: "/listTicketClient",
    name: "allTicketClient",
    meta: { title: "allTicketClient", authRequired: true, role:'Client' },
    component: () => import("../views/tickets/allTicketClient")
  },
  {
    path: "/rapport/:ticketId",
    name: "Rapport",
    meta: { title: "Rapport", authRequired: true },
    component: () => import("../views/tickets/Rapport")
  },
  {
    path: "/listeRapport",
    name: "ListRapport",
    meta: { title: "ListRapport", authRequired: true },
    component: () => import("../views/tickets/listeRapport")
  },
  {
    path: "/agent/tickets",
    name: "opentickets",
    meta: { title: "opentickets", authRequired: true },
    component: () => import("../views/tickets/ticketAssign")
  },
  {
    path: "/createtickets",
    name: "CreationTicket",
    meta: { title: "CreationTicket", authRequired: true, role: 'Client' },
    component: () => import("../views/tickets/creation")
  },
  {
    path: "/listeticketclient",
    name: "Liste",
    meta: { title: "Liste", authRequired: true, role: 'Client' },
    component: () => import("../views/tickets/listticket")
  },
  {
    path: "/historiqueticket",
    name: "Historique",
    meta: { title: "Historique", authRequired: true, role: 'Client' },
    component: () => import("../views/tickets/history")
  },
  {
    path: "/listeClients",
    name: "ClientList",
    meta: { title: "ClientList", authRequired: true, role: 'Admin' },
    component: () => import("../views/clients/listClient")
  },
  {
    path: "/historiqueticket",
    name: "Historique",
    meta: { title: "Historique", authRequired: true },
    component: () => import("../views/tickets/history")
  },
  {
    path: "/profileClient",
    name: "Profil",
    meta: { title: "Profil", authRequired: true, role: 'Client' },
    component: () => import("../views/clients/profilClient")
  },
  {
    path: "/profileUser",
    name: "ProfilUser",
    meta: { title: "ProfilUser", authRequired: true,role: 'Admin' },
    component: () => import("../views/users/profilUser")
  },
  {
    path: "/ticket/detail/:id",
    name: "TicketDetail",
    meta: { title: "TicketDetail", authRequired: true, role: 'Agent' },
    component: () => import("../views/tickets/ticketDetail")
  },
  {
    path: "/show/ticket/:id",
    name: "ShowTicket",
    meta: { title: "ShowTicket", authRequired: true, role: 'Client' },
    component: () => import("../views/tickets/showticket")
  },
  {
    path: "/voir/ticket/:id",
    name: "voirTicket",
    meta: { title: "voirTicket", authRequired: true, role: 'Admin' },
    component: () => import("../views/tickets/voirTicket")
  },
  {
    path: "/reset/:token",
    name: "ResetPassword",
    meta: { title: "ResetPassword", authRequired: true },
    component: () => import("../views/sample-pages/reset"),
    // props: (route) => ({ token: route.query.token})
  },
  {
    path: "/login",
    name: "login",
    component: () => import("../views/account/login"),
    meta: {
      title: "Login",
      beforeResolve(routeTo, routeFrom, next) {
        const auth = useAuthStore();
        // If the user is already logged in
        // if (store.getters["auth/loggedIn"]) {
        if (auth.loggedIn) {
          // Redirect to the home page instead
          next({ name: "default" });
        } else {
          // Continue to the login page
          next();
        }
      }
    }
  },
  {
    path: "/register",
    name: "Register",
    component: () => import("../views/account/register"),
    meta: {
      title: "Register",
      beforeResolve(routeTo, routeFrom, next) {
        const auth = useAuthStore();
        // If the user is already logged in
        // if (store.getters["auth/loggedIn"]) {
        if (auth.loggedIn) {
          // Redirect to the home page instead
          next({ name: "default" });
        } else {
          // Continue to the login page
          next();
        }
      }
    }
  },
  {
    path: "/forgot-password",
    name: "Forgot password",
    component: () => import("../views/account/forgot-password"),
    meta: {
      title: "Forgot password",
      beforeResolve(routeTo, routeFrom, next) {
        const auth = useAuthStore();
        // If the user is already logged in
        // if (store.getters["auth/loggedIn"]) {
        if (auth.loggedIn) {
          // Redirect to the home page instead
          next({ name: "default" });
        } else {
          // Continue to the login page
          next();
        }
      }
    }
  },
  {
    path: "/logout",
    name: "logout",
    component: () => import("../views/account/logout"),
    meta: {
      title: "Logout",
      authRequired: true,
      beforeResolve(routeTo, routeFrom, next) {
        const auth = useAuthStore();
        const authFake = useAuthFakeStore();

        if (process.env.VUE_APP_DEFAULT_AUTH === "firebase") {
          auth.logOut();
        } else {
          authFake.logout();
        }
        const authRequiredOnPreviousRoute = routeFrom.matched.some((route) =>
          route.push("/login")
        );
        // Navigate back to previous page, or home as a fallback
        next(
          authRequiredOnPreviousRoute ? { name: "default" } : { ...routeFrom }
        );
      }
    }
  },
//   {
//     path: "/",
//     name: "Login sample",
//     component: () => import("../views/sample-pages/login-sample"),
//     meta: { title: "Login",
//         beforeResolve(routeTo, routeFrom, next){
//             const auth = useAuthStore();
//             if (auth.loggedIn) {
//                 next({ name: auth.redirectRouteBasedOnRole()});
//             } else {
//                 next();
//             }
//         }
//     }
//   },
  {
    path: "/auth/login-2",
    name: "Login-2-sample",
    meta: { title: "Login 2", authRequired: true },
    component: () => import("../views/sample-pages/login-2")
  },
  {
    path: "/auth/register",
    name: "Register sample",
    meta: { title: "Register", authRequired: true },
    component: () => import("../views/sample-pages/register-sample")
  },
  {
    path: "/auth/register-2",
    name: "Register-2",
    meta: { title: "Register 2", authRequired: true },
    component: () => import("../views/sample-pages/register-2")
  },
  {
    path: "/recover/:token",
    name: "Recover pwd",
    meta: { title: "Recover Password", authRequired: true },
    component: () => import("../views/sample-pages/recoverpw-sample")
  },
  {
    path: "/auth/recoverpwd-2",
    name: "Recover pwd-2",
    meta: { title: "Recover Password 2", authRequired: true },
    component: () => import("../views/sample-pages/recoverpwd-2")
  },
  {
    path: "/auth/confirm-mail",
    name: "confirm-mail",
    meta: { title: "Confirm Email", authRequired: true },
    component: () => import("../views/sample-pages/confirm-mail")
  },
  {
    path: "/auth/confirm-mail-2",
    name: "confirm-mail-2",
    meta: { title: "Confirm Email 2", authRequired: true },
    component: () => import("../views/sample-pages/confirm-mail-2")
  },
  {
    path: "/auth/email-verification",
    name: "email-verification",
    meta: { title: "Email Verification", authRequired: true },
    component: () => import("../views/sample-pages/email-verification")
  },
  {
    path: "/auth/email-verification-2",
    name: "email-verification-2",
    meta: { title: "Email Verification 2", authRequired: true },
    component: () => import("../views/sample-pages/email-verification-2")
  },
  {
    path: "/auth/two-step-verification",
    name: "two-step-verification",
    meta: { title: "Two Step Verification", authRequired: true },
    component: () => import("../views/sample-pages/two-step-verification")
  },
  {
    path: "/auth/two-step-verification-2",
    name: "two-step-verification-2",
    meta: { title: "Two Step Verification 2", authRequired: true },
    component: () => import("../views/sample-pages/two-step-verification-2")
  }
];
