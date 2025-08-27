<template>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/inventory" class="brand-link logo-switch">
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
                <a href="/home/cards" class="nav-link">
                  <i class=" fa-solid fa-home nav-icon"></i>
                  <p>Accueil</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/inventory" class="nav-link">
                  <i class=" fa-solid fa-boxes-stacked nav-icon"></i>
                  <p>Inventaire</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/printer-stats" class="nav-link">
                  <i class="fas fa-chart-bar nav-icon"></i>
                  <p>Imprimante</p>
                </a>
              </li>
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
  color: #000000 !important;
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
  border-bottom: none !important;
  margin-bottom: 0 !important;
}

.brand-image-xl {
  width: auto !important;
  height: 100px !important;
  max-height: none !important;
  object-fit: contain;
}

.sidebar {
  padding-top: 0 !important;
  margin-top: 0 !important;
  border-top: none !important;
}

.sidebar .nav {
  margin-top: 0 !important;
  padding-top: 0 !important;
}

.main-sidebar {
  background-color: rgb(255, 255, 255) !important;
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  height: 100vh !important;
  overflow-y: hidden !important;
  z-index: 1000 !important;
  width: 250px !important; /* Ajustez la largeur selon vos besoins */
}

.sidebar {
  height: calc(100vh - 120px) !important; /* Soustrait la hauteur du logo */
  overflow-y: hidden !important;
}

.sidebar .nav-link {
  background-color: #ffffff !important;
  color: #000000 !important;
  border-radius: 0.25rem;
  transition: background-color 0.3s, color 0.3s;
}

.sidebar .nav-link:hover {
  background-color: #0042FF !important;
  color: #ffffff !important;
}

.sidebar .nav-link i {
  color: #1016c8 !important;
}

.sidebar .nav-link:hover i {
  color: #ffffff !important;
}

/* Empêcher le collapse de la sidebar */
.main-sidebar {
  min-width: 250px !important;
  max-width: 250px !important;
}

/* Masquer les boutons de toggle de la sidebar s'ils existent */
.nav-link[data-widget="pushmenu"] {
  display: none !important;
}

[data-widget="pushmenu"] {
  display: none !important;
}
</style>