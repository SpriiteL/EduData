<template>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link logo-switch">
      <img src="asset/edu3.png" alt="AdminLTE Docs Logo Large" class="brand-image-xl">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item menu-open">
            <a href="#" class="nav-link bg-dark">
              <i class="nav-icon fas fa-tasks"></i>
              <p>
                Gestion
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/inventory" class="nav-link">
                  <i class=" fa-solid fa-boxes-stacked nav-icon"></i>
                  <p>Inventaire</p>
                </a>
              </li>
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/imprimante" class="nav-link">
                  <i class="fas fa-chart-bar nav-icon"></i>
                  <p>Imprimante</p>
                </a>
              </li>
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/badge" class="nav-link">
                  <i class="fa-solid fa-id-card nav-icon"></i>
                  <p>Badges</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item menu-open">
            <a href="#" class="nav-link bg-dark">
              <i class="nav-icon fa-solid fa-gear"></i>
              <p>
                Paramètres
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/logout" class="nav-link">
                  <i class="fas fa-right-from-bracket nav-icon"></i>
                  <p>Déconnexion</p>
                </a>
              </li>
            </ul>
          </li>

        </ul>
      </nav>
    </div>
  </aside>
</template>

<style scoped>
.nav-link.bg-dark {
  background-color: #ffffff !important;
  color: #a7abb8 !important;
}

.nav-link.bg-dark:hover {
  background-color: #0042FF !important;
  color: #ffffff !important;
}

.brand-link {
  display: flex;
  justify-content: center;
  align-items: center;
  height: auto !important;
  padding: 10px 0;
  text-decoration: none;
}

.brand-image-xl {
  width: auto !important;
  height: 100px !important;
  max-height: none !important;
  object-fit: contain;
}

.main-sidebar {
  background-color: rgb(255, 255, 255) !important;
}

.sidebar .nav-link {
  background-color: #ffffff !important;
  color: #a7abb8 !important;
  border-radius: 0.25rem;
  transition: background-color 0.3s, color 0.3s;
}

.sidebar .nav-link:hover {
  background-color: #0042FF !important;
  color: #ffffff !important;
}
</style>
