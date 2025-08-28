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
import Footer from './components/Footer.vue';
import CGU from './components/CGU.vue';
import PublicBadge from './components/PublicBadge.vue';
import GestionUserAdmin from './components/GestionUserAdmin.vue';
import 'font-awesome/css/font-awesome.css';
import "/node_modules/flag-icons/css/flag-icons.min.css";
import '@fortawesome/fontawesome-free/css/all.min.css';

// Fonction pour monter les composants de manière sécurisée
function mountComponent(component, selector) {
    const element = document.querySelector(selector);
    if (element) {
        createApp(component).mount(selector);
    }
}

// Vérifier que le DOM est chargé avant de monter les composants
document.addEventListener('DOMContentLoaded', () => {
    mountComponent(Login, '#login');
    mountComponent(Sidebar, '#sidebar');
    mountComponent(Header, '#header');
    mountComponent(Badge, '#badge');
    mountComponent(Dashboard, '#dashboard');
    mountComponent(Inventory, '#inventory');
    mountComponent(Imprimante, '#imprimante');
    mountComponent(PrinterTable, '#vue-table');
    mountComponent(Home, '#home');
    mountComponent(Sidebar2, '#sidebar2');
    mountComponent(Footer, '#footer');
    mountComponent(CGU, '#cgu');
    mountComponent(PublicBadge, '#publicbadge');
    mountComponent(GestionUserAdmin, '#gestion-user-admin');
});