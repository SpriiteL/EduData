<script setup>
import { computed } from "vue";
import { useStore } from "vuex";
import SidenavList from "./SidenavList.vue";

const store = useStore();
const isRTL = computed(() => store.state.isRTL);
const layout = computed(() => store.state.layout);
const sidebarType = computed(() => store.state.sidebarType);
const darkMode = computed(() => store.state.darkMode);
</script>

<template>
  <aside
    style="
      width: 250px;
      height: 100vh;
      background-color: #222;
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      padding: 20px;
      overflow-y: auto;
      z-index: 10000;
    "
    :class="{
      'me-3 rotate-caret fixed-end': isRTL,
      'fixed-start ms-3': !isRTL,
      'bg-transparent shadow-none': layout === 'landing',
      [sidebarType]: true
    }"
    id="sidenav-main"
  >
    <router-link to="/">
      <img
        src="/asset/logo-ct.png"
        alt="Logo"
        style="width: 150px; margin-bottom: 20px; object-fit: contain;"
      />
    </router-link>

    <div
      class="user-panel"
      style="display: flex; align-items: center; margin-bottom: 20px;"
    >
      <img
        src="/asset/icons/avatarprofile.jpg"
        alt="User"
        style="
          width: 40px;
          height: 40px;
          border-radius: 50%;
          object-fit: cover;
          border: 2px solid white;
        "
      />
      <span style="margin-left: 10px; font-weight: bold;">Developpeur Test</span>
    </div>

    <nav>
      <ul style="list-style: none; padding: 0; margin: 0;">
        <li style="margin-bottom: 10px;">
          <router-link to="/" style="color: white; text-decoration: none;">
            Accueil
          </router-link>
        </li>
        <li style="margin-bottom: 10px;">
          <router-link to="/inventory" style="color: white; text-decoration: none;">
            Inventory
          </router-link>
        </li>
        <li style="margin-bottom: 10px;">
          <router-link to="/dashboard" style="color: white; text-decoration: none;">
            Dashboard
          </router-link>
        </li>
      </ul>
    </nav>

    <!-- Inclure SidenavList si tu veux la liste dynamique -->
    <!-- <SidenavList /> -->
  </aside>
</template>

<style scoped>
/* Optionnel: un peu de style pour le hover sur les liens */
nav ul li a:hover {
  color: #4caf50;
}
</style>
