import axios from "axios";
import { onMounted,ref } from "vue";
let clients = ref([])
const getClients = ()=>{
    axios.get("http://127.0.0.1:8000/api/clients")
    .then(function (resp) {
        clients.value = resp.data
    })
}
onMounted(()=>{})

export { getClients };
