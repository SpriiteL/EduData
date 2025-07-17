<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

import SidenavItem from "./SidenavItem.vue";

const store = useStore();
const isRTL = computed(() => store.state.isRTL);

const route = useRoute();

const getRoute = () => {
  const routeArr = route.path.split("/");
  return routeArr[1];
};
</script>

<template>
  <div class="collapse navbar-collapse w-auto h-auto h-100" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      <!-- Gestion -->
      <li class="nav-item">
        <a href="#" class="nav-link bg-dark d-flex justify-content-between align-items-center">
          <div>
            <i class="nav-icon fas fa-tasks"></i>
            <span class="ms-2">Gestion</span>
          </div>
          <i class="fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview ps-3">
          <li class="nav-item">
            <SidenavItem
              to="/inventory"
              :class="getRoute() === 'inventory' ? 'active' : ''"
              navText="Inventaire"
            >
              <template #icon>
                <i class="fa-solid fa-boxes-stacked nav-icon"></i>
              </template>
            </SidenavItem>
          </li>

          <li class="nav-item">
            <SidenavItem
              to="/imprimante"
              :class="getRoute() === 'imprimante' ? 'active' : ''"
              navText="Imprimante"
            >
              <template #icon>
                <i class="fas fa-chart-bar nav-icon"></i>
              </template>
            </SidenavItem>
          </li>

          <li class="nav-item">
            <SidenavItem
              to="/badge"
              :class="getRoute() === 'badge' ? 'active' : ''"
              navText="Badges"
            >
              <template #icon>
                <i class="fa-solid fa-id-card nav-icon"></i>
              </template>
            </SidenavItem>
          </li>
        </ul>
      </li>

      <!-- Paramètres -->
      <li class="nav-item">
        <a href="#" class="nav-link bg-dark d-flex justify-content-between align-items-center">
          <div>
            <i class="nav-icon fa-solid fa-gear"></i>
            <span class="ms-2">Paramètres</span>
          </div>
          <i class="fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview ps-3">
          <li class="nav-item">
            <SidenavItem
              to="/logout"
              navText="Déconnexion"
            >
              <template #icon>
                <i class="fas fa-right-from-bracket nav-icon"></i>
              </template>
            </SidenavItem>
          </li>
        </ul>
      </li>

    </ul>
  </div>
</template>

<style scoped>
.nav-link.bg-dark {
  background-color: #343a40 !important; /* Même couleur que la sidebar */
  color: #c9cedc !important;
}

.nav-link.bg-dark:hover {
  background-color: #50575d !important;
  color: #ffffff;
}

.nav-treeview {
  list-style: none;
  padding-left: 0;
}

.nav-treeview .nav-item {
  margin-bottom: 0.5rem;
}
</style>
