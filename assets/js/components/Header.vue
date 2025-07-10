<template>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand" style="background: linear-gradient(90deg,#003cff, #00aaff);">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button" style="color: white;">
          <i class="fas fa-bars"></i>
        </a>
      </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-auto mr-3 search-form">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-navbar" type="submit" aria-label="Search button">
          <i class="fas fa-search"></i>
        </button>
      </div>
    </form>

    <!-- Right navbar content (User Profile) -->
    <div class="navbar-user d-flex align-items-center pr-3">
      <img src="asset/icons/avatarprofile.jpg" alt="User" class="rounded-circle mr-2" style="height: 36px; width: 36px; object-fit: cover;">
      <span class="text-white font-weight-bold">Développeur Test</span>
    </div>
  </nav>
  <!-- /.navbar -->
</template>

<script></script>

<style scoped>
.search-form .input-group {
  position: relative;
  width: 250px;
}

.search-form .form-control-navbar {
  background-color: transparent !important;
  border: 1.5px solid white !important;
  color: white !important;
  border-radius: 9999px !important;
  padding-left: 40px !important;
  height: 38px;
  box-shadow: none !important;
}

.search-form .form-control-navbar::placeholder {
  color: rgba(255, 255, 255, 0.7);
}

.search-form button.btn-navbar {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent !important;
  border: none !important;
  color: white !important;
  padding: 0;
  cursor: pointer;
  z-index: 10;
}

.search-form button.btn-navbar:focus {
  outline: none;
  box-shadow: none;
}

.search-form button.btn-navbar i {
  font-size: 16px;
}

.navbar-user {
  display: flex;
  align-items: center;
}
</style>
