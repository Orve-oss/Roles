import axios from "axios";
export default {
    async fetchAdminProfile() {
        const token = localStorage.getItem('authToken');
        if (token) {
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }

        try {
          const response = await axios.get('http://127.0.0.1:8000/api/user-profile', {
            headers: {
              Authorization: `Bearer ${token}`
            }
          });
          const user = response.data.user;
          return {
            image: user.image || '',
            initial: user.name.charAt(0).toUpperCase(),
            displayName: user.name
          };
        } catch (error) {
          console.error('Error fetching client profile:', error);
          throw error; // Propager l'erreur pour être gérée au niveau du composant
        }
    },


    async fetchClientProfile() {
        const token = localStorage.getItem('authToken');
        if (token) {
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }

        try {
          const response = await axios.get('http://127.0.0.1:8000/api/client-profile', {
            headers: {
              Authorization: `Bearer ${token}`
            }
          });
          const client = response.data.client;
          return {
            image: client.image || '',
            initial: client.nom_clt.charAt(0).toUpperCase(),
            displayName: client.nom_clt
          };
        } catch (error) {
          console.error('Error fetching client profile:', error);
          throw error; // Propager l'erreur pour être gérée au niveau du composant
        }
    }
}
