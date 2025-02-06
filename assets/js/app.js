import { createApp } from 'vue';
import Login from './components/Login.vue';
import Sidebar from './components/Sidebar.vue';
import Header from './components/Header.vue';
import Badge from './components/Badge.vue';
import Dashboard from './components/Dashboard.vue';
import 'font-awesome/css/font-awesome.css';
import "/node_modules/flag-icons/css/flag-icons.min.css";
import '@fortawesome/fontawesome-free/css/all.min.css';



createApp(Login).mount('#login');
createApp(Sidebar).mount('#sidebar');
createApp(Header).mount('#header');
createApp(Badge).mount('#badge');
createApp(Dashboard).mount('#dashboard');