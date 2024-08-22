import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("authClient", {
    state: () => ({
        currentUser: null,
        loggedIn: false,
    }),
    getters: {
        isLoggedIn() {
            return !!this.currentUser;
        }
    },
    actions: {
        async logIn({ email, password, role }) {
            try {
                const response = await axios.post("http://127.0.0.1:8000/api/login", { email, password, role });
                if (role !== 'Client') {
                    throw new Error('Vous n\'êtes pas autorisé à vous connecter ici');
                }
                if (response.data.status === "success") {
                    this.authSucces = response.data.message;
                    this.isAuthSucces = true;
                    this.setUser(response.data.user);
                    localStorage.setItem('authToken', response.data.access_token);
                    axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
                    localStorage.setItem('user', JSON.stringify(response.data.user));
                    localStorage.setItem("userRole", response.data.user.role[0]);
                    const redirectRoute = this.redirectRouteBasedOnRole();
                    return redirectRoute;
                } else {
                    throw new Error(response.data.message);
                }
            } catch (error) {
                console.error("Login error:", error);
                throw error;
            }
        },
        setUser(user) {
            this.currentUser = user;
            this.loggedIn = true;
            this.saveState("authClient.currentUser", user);
        },
        saveState(key, state) {
            window.sessionStorage.setItem(key, JSON.stringify(state));
        },
        logOut() {
            this.currentUser = null;
            this.loggedIn = false;
            localStorage.removeItem("authToken");
            localStorage.removeItem("userRole");
        },
        redirectRouteBasedOnRole() {
            if (this.currentUser && this.currentUser.role) {
                const role = this.currentUser.role[0]; // Assurez-vous d'accéder directement à la propriété
                switch (role) {

                    case 'Client':
                        return 'Accueil Client';
                    case 'Admin':
                        return 'page403';
                    default:
                        return 'page403';
                }
            }else{
                return 'page403';
            }
            // return 'page403'; // Par défaut, redirigez vers 'default'
        }
    }
});



// import { defineStore } from "pinia";

// export const useAuthStore = defineStore('auth', {
//     state: () => ({
//         user: null,
//         loggedIn: false,
//     }),
//     actions: {
//         login(user) {
//             this.user = user;
//             this.loggedIn = true;
//         },
//         logout(){
//             this.user = null;
//             this.loggedIn = false;
//         },
//         redirectRouteBasedOnRole(){
//             if (this.user && this.user.role) {
//                 const role = this.user.role[0]; // Utilisez le premier rôle du tableau
//                 switch (role) {
//                     case 'Admin':
//                         return 'listUser';
//                     case 'Agent':
//                         return 'allTicket';
//                     default:
//                         return 'default';
//                 }
//             }
//             return 'default';
//         }
//     }
// })
// import { getFirebaseBackend } from '../../authUtils'

// export const useAuthStore = defineStore("auth", {
//     state: () => ({
//         currentUser: sessionStorage.getItem('authUser'),
//     }),
//     getters: {
//         loggedIn() {
//             return !!this.currentUser
//         }
//     }
//     ,
//     actions: {
//         logIn({ email, password }) {
//             return getFirebaseBackend().loginUser(email, password).then(() => {
//                 this.validate()
//             })
//         },
//         validate() {
//             if (!this.currentUser) {
//                 return Promise.resolve({})
//             }
//             const user = getFirebaseBackend().getAuthenticatedUser()
//             this.setUser(user)

//             return Promise.resolve(user)

//         },
//         setUser(user) {
//             this.currentUser = user
//             this.saveState('auth.currentUser', user)


//         },
//         saveState(key, state) {
//             window.sessionStorage.setItem(key, JSON.stringify(state))
//         },
//         register({ username, email, password } = {}) {

//             return getFirebaseBackend().registerUser(username, email, password).then((response) => {
//                 const user = response
//                 // this.setUser(user)
//                 this.validate()
//                 return user
//             });
//         },
//         resetPassword({ email } = {}) {

//             return getFirebaseBackend().forgetPassword(email).then((response) => {
//                 const message = response.data
//                 this.validate()

//                 return message
//             });
//         },
//         logOut() {
//             this.setUser(null)
//             return new Promise((resolve, reject) => {
//                 getFirebaseBackend().logout().then(() => {
//                     resolve(true);
//                 }).catch((error) => {
//                     reject(this._handleError(error));
//                 })
//             });
//         },
//     },
// })
