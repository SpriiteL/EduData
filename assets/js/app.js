import { createApp } from 'vue';
import Login from './components/Login.vue';
import Sidebar from './components/Sidebar.vue';
import Header from './components/Header.vue';
import Badge from './components/Badge.vue';
import Dashboard from './components/Dashboard.vue';
import Inventory from './components/Inventory.vue';
import Imprimante from './components/Imprimante.vue';
import PrinterTable from './components/PrinterTable.vue';
import Home from './components/Home.vue';
import Sidebar2 from './components/Sidenav/index.vue';
import 'font-awesome/css/font-awesome.css';
import "/node_modules/flag-icons/css/flag-icons.min.css";
import '@fortawesome/fontawesome-free/css/all.min.css';



createApp(Login).mount('#login');
createApp(Sidebar).mount('#sidebar');
createApp(Header).mount('#header');
createApp(Badge).mount('#badge');
createApp(Dashboard).mount('#dashboard');
createApp(Inventory).mount('#inventory');
createApp(Imprimante).mount('#imprimante');
createApp(PrinterTable).mount('#vue-table');
createApp(Home).mount('#home');
createApp(Sidebar2).mount('#sidebar2');
