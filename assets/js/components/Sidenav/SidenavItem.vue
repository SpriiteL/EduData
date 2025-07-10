<script setup>
import { computed } from "vue";
import { useStore } from "vuex";

const store = useStore();
const isRTL = computed(() => store.state.isRTL);

const sidebarMinimize = () => store.commit("sidebarMinimize");

const minimizeSidebar = () => {
  if (window.innerWidth < 1200) {
    sidebarMinimize();
  }
};

defineProps({
  to: {
    type: String,
    required: true,
  },
  navText: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <router-link
    :to="to"
    class="nav-link d-flex align-items-center"
    @click="minimizeSidebar"
  >
    <div
      class="icon icon-shape icon-sm text-center d-flex align-items-center justify-content-center"
    >
      <slot name="icon"></slot>
    </div>
    <span class="nav-link-text" :class="isRTL ? 'me-1' : 'ms-1'">{{ navText }}</span>
  </router-link>
</template>

<style scoped>
.nav-link {
  color: #c9cedc;
  transition: background-color 0.3s, color 0.3s;
}

.nav-link.active,
.nav-link:hover {
  background-color: #50575d;
  color: #fff;
}

.icon {
  width: 24px;
}
</style>
