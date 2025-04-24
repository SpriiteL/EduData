<template>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link logo-switch">
      <img src="asset/edu2.png" alt="AdminLTE Docs Logo Large" class="brand-image-xl">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="asset/icons/avatarprofile.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Developpeur Test</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <!-- <li class="nav-item menu-open">
            <a href="#" class="nav-link bg-dark">
              <i class="nav-icon fa-solid fa-house"></i>
              <p>
                Accueil
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/dashboard" class="nav-link">
                  <i class="fa-solid fa-gauge nav-icon"></i>
                  <p>Dashboard</p>
                </a>
              </li>
            </ul>
          </li> -->
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
          <!-- <li class="nav-item menu-open">
            <a href="#" class="nav-link bg-dark">
              <i class="nav-icon fa-solid fa-user-tie"></i>
              <p>
                Administration
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/admin" class="nav-link">
                  <i class=" fa-solid fa-key nav-icon"></i>
                  <p>Admin</p>
                </a>
              </li>
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/admin/responsable" class="nav-link">
                  <i class="fas fa-key nav-icon"></i>
                  <p>Admin R</p>
                </a>
              </li>
            </ul>
          </li> -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link bg-dark">
              <i class="nav-icon fa-solid fa-gear"></i>
              <p>
                Paramètres
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <!--<ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/profil" class="nav-link">
                  <i class=" fa-solid fa-user nav-icon"></i>
                  <p>Profil</p>
                </a>
              </li>
            </ul> -->
            <!-- <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/setting" class="nav-link">
                  <i class="fas fa-toolbox nav-icon"></i>
                  <p>Préferences</p>
                </a>
              </li>
            </ul> -->
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/logout" class="nav-link">
                  <i class="fas fa-right-from-bracket nav-icon"></i>
                  <p>Déconnexion</p>
                </a>
              </li>
            </ul>
          </li>
          <!-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Simple Link
                <span class="right badge badge-danger">New</span>
              </p>
            </a>
          </li>
          <li class="nav-item" >
            <a href="{{path('app_login')}}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Test
                <span class="right badge badge-danger">New</span>
              </p>
            </a>
          </li> -->
        </ul>
      </nav>
    </div>
  </aside>
</template>

<style scoped>
/* Appliquer la couleur de fond et l'effet hover */
.nav-link.bg-dark {
  background-color: #343a40 !important; /* Même couleur que la sidebar */
  color: #c9cedc !important;
}

.nav-link.bg-dark:hover {
  background-color: #50575d !important; /* Effet hover comme "Inactive Page" */
  color: #ffffff;
}

.brand-link {
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto !important; /* Débloque la hauteur */
            padding: 10px 0; /* Ajuste l'espacement */
            }

        .brand-image-xl {
            width: auto !important;
            height: 100px !important; /* Augmente la hauteur */
            max-height: none !important; /* Supprime toute limite */
            object-fit: contain; /* Empêche la déformation */
        }
</style>