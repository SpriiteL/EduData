<template>
  <div class="dashboard-container">
    <!-- Logo -->
    <header class="logo">
        <img src="../../../public/asset/edu3.png" alt="Logo EduData" class="logo-image" />
    </header>

    <!-- Action cards -->
    <div class="cards">
      <a href="/printer-stats" class="card">
            <i class="fa-solid fa-print"></i>
            <span>Imprimante</span>
      </a>

      <a href="/inventory" class="card">
        <i class="fa-solid fa-box"></i>
        <span>Inventaire</span>
      </a>

      <a href="/badge" class="card">
        <i class="fa-solid fa-id-card"></i>
        <span>Badge</span>
      </a>

      <a href="/public/badges" class="card">
        <i class="fa-solid fa-desktop"></i>
        <span>Ecran</span>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: "AccueilView",
};
</script>

<style scoped>
/* full-screen container with blue gradients */
.dashboard-container {
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  background: linear-gradient(#2a73b7, #0d253f 70%);
  position: relative;
  overflow: hidden;
}
/* stylised side shapes */
.dashboard-container::before,
.dashboard-container::after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  width: 28%;
  background: rgba(255, 255, 255, 0.03);
  clip-path: polygon(0 0, 100% 0, 72% 100%, 0 100%);
}
.dashboard-container::after {
  right: 0;
  transform: scaleX(-1);
}

/* logo style */
.logo {
  margin-top: 40px;
  margin-bottom: 80px;
  font-weight: 700;
  font-size: 2.6rem;
  color: #00b4ff;
  text-shadow: 0 3px 6px rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
}
.logo-3d {
  font-size: 3rem;
  color: #26c6da;
  margin-right: 6px;
  transform: skewX(-10deg);
}

/* cards grid */
.cards {
  display: flex;
  gap: 40px;
  flex-wrap: wrap;
  justify-content: center;
}

/* single card */
.card {
  width: 145px;
  height: 165px;
  background: #03a9f4;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.35);
  color: #fff;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card i {
  font-size: 3rem;
  margin-bottom: 10px;
}
.card span {
  font-size: 1rem;
  font-weight: 500;
}
.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 14px rgba(0, 0, 0, 0.45);
}

.logo-image {
  width: 300px;
  height: auto;
}
</style>
