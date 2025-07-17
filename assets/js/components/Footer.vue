<template>
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section">
        <h3>Contact</h3>
        <p>📍 9Ter Rue Pétinaud Beaupeyrat,</p>
        <p>   87036 Limoges Cedex</p>
        <p>📞 +33 5 55 45 81 00</p>
        <p>✉️ contact@beaupeyrat.com</p>
      </div>

      <div class="footer-section">
        <h3>Liens utiles</h3>
        <ul>
          <li><a href="/cgu">Conditions Générales d'Utilisation</a></li>
          <!-- <li><a href="/confidentialite">Politique de confidentialité</a></li> -->
          <li><a href="/mentions-legales">Mentions légales</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h3>Suivez-nous</h3>
        <div class="social-links">
          <a href="https://www.facebook.com/Beaupeyrat.limoges/" target="_blank">Facebook</a>
          <a href="https://www.instagram.com/campus_beaupeyrat/" target="_blank">Instagram</a>
          <a href="https://www.youtube.com/channel/UCQ2Iy1GN2P68UsgN640tf-Q" target="_blank">Youtube</a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      &copy; 2024 edudata.beaupeyrat.com – Tous droits réservés.
    </div>
  </footer>
</template>

<script setup>
// Pas de logique spécifique ici pour l'instant
</script>

<style scoped>
.footer {
  background-color: #1e1e1e;
  color: #ffffff;
  padding: 40px 20px;
  font-family: Arial, sans-serif;
}
.footer-content {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 30px;
  max-width: 1200px;
  margin: 0 auto;
}
.footer-section {
  flex: 1 1 250px;
}
.footer-section h3 {
  margin-bottom: 10px;
  color: #0063FD;
}
.footer-section a {
  color: #cccccc;
  text-decoration: none;
}
.footer-section a:hover {
  text-decoration: underline;
}
.social-links a {
  display: inline-block;
  margin-right: 10px;
}
.footer-bottom {
  text-align: center;
  margin-top: 30px;
  font-size: 0.9rem;
  color: #888;
}
</style>
