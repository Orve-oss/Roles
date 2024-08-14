import { createWebHistory, createRouter } from "vue-router";
import { useAuthStore } from "@/state/pinia";
import routes from "./routes";


const router = createRouter({
    history: createWebHistory(),
    routes,
    // Use the HTML5 history API (i.e. normal-looking routes)
    // instead of routes with hashes (e.g. example.com/#/about).
    // This may require some server configuration in production:
    // https://router.vuejs.org/en/essentials/history-mode.html#example-server-configurations
    // mode: "history",
    // Simulate native-like scroll behavior when navigating to a new
    // route and using back/forward buttons.
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { x: 0, y: 0 };
        }
    }
});

// Before each route evaluates...
router.beforeEach((routeTo, routeFrom, next) => {
    const auth = useAuthStore();

    if (process.env.VUE_APP_DEFAULT_AUTH === "firebase") {
        // Check if auth is required on this route
        // (including nested routes).
        const authRequired = routeTo.matched.some(
            (route) => route.meta.authRequired
        );

        // If auth isn't required for the route, just continue.
        if (!authRequired) return next();

        // If auth is required and the user is logged in...
        if (auth.loggedIn) {
            // Validate the local user token...
            // return store.dispatch('auth/validate').then((validUser) => {
            return auth.validate().then((validUser) => {
                // Then continue if the token still represents a valid user,
                // otherwise redirect to login.
                validUser ? next() : redirectToLogin();
            });
        }

        // If auth is required and the user is NOT currently logged in,
        // redirect to login.
        redirectToLogin();

        // eslint-disable-next-line no-unused-vars
        // eslint-disable-next-line no-inner-declarations
        function redirectToLogin() {
            // Pass the original route to the login component
            next({ name: "Login sample", query: { redirectFrom: routeTo.fullPath } });
        }
    } else {
        const publicPages = ["/", "/accueil", "/forgot-password"];
        const authpage = !publicPages.includes(routeTo.path);
        const loggeduser = JSON.parse(localStorage.getItem("user"));
        const userRole = loggeduser ? loggeduser.role[0] : null;
        console.log(userRole);

        if (authpage && !loggeduser) {

            return next("/");
        }
        else if (publicPages === "/accueil" && userRole !== "Client") {
            return next("/page/403");
        }


        next();
    }
});

// router.beforeEach((routeTo, routeFrom, next) => {
//     const auth = useAuthStore();

//     if (auth.loggedIn) {
//         auth.validate().then((validUser) => {
//             validUser ? next() : next({ name: "login", query: { redirectFrom: routeTo.fullPath } });
//         });
//     } else {
//         const publicPages = ["/login", "/register", "/forgot-password"];
//         const authpage = !publicPages.includes(routeTo.path);
//         const loggeduser = localStorage.getItem("user");

//         if (authpage && !loggeduser) {
//             next("/login");
//         } else {
//             next();
//         }
//     }
// });


router.beforeResolve(async (to, from, next) => {
    try {
        // Traitement de chaque route correspondant
        for (const route of to.matched) {
            if (route.meta && typeof route.meta.beforeResolve === 'function') {
                // Appel du hook beforeResolve s'il est défini
                await new Promise((resolve, reject) => {
                    route.meta.beforeResolve(to, from, (redirectTo) => {
                        if (redirectTo) {
                            // Redirection si spécifié dans le hook
                            next(redirectTo);
                            reject(new Error("Redirected"));
                        } else {
                            resolve();
                        }
                    });
                });
            }
        }

        // Mise à jour du titre de la page à partir des métadonnées de la route
        if (to.meta.title) {
            document.title = to.meta.title;
        }

        // Poursuite de la résolution de la route
        next();
    } catch (error) {
        // Gestion des erreurs éventuelles
        console.error("beforeResolve error:", error);
        next(false); // Annule la navigation en cas d'erreur
    }
});





export default router;
