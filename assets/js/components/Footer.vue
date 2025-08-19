<template>
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section footer-contact">
        <h3>Contact</h3>
        <div class="contact-item">
          <span class="icon">📍</span>
          <span>9Ter Rue Pétinaud Beaupeyrat,<br />87036 Limoges Cedex</span>
        </div>
        <div class="contact-item">
          <span class="icon">📞</span>
          <a href="tel:+33555458100">+33 5 55 45 81 00</a>
        </div>
        <div class="contact-item">
          <span class="icon">✉️</span>
          <a href="mailto:contact@beaupeyrat.com">contact@beaupeyrat.com</a>
        </div>
      </div>

      <div class="footer-section footer-links">
        <h3>Liens utiles</h3>
        <ul>
          <li>
            <a href="/cgu" target="_blank" rel="noopener">Conditions Générales d'Utilisation</a>
          </li>
        </ul>
      </div>

      <div class="footer-section footer-social">
        <h3>Suivez-nous</h3>
        <div class="social-links">
          <a
            href="https://www.facebook.com/Beaupeyrat.limoges/"
            target="_blank"
            rel="noopener"
            aria-label="Facebook"
            class="social-icon"
          >
            <svg width="22" height="22" viewBox="0 0 50 50" fill="currentColor"><path d="M22 25h-5v8h5v17h10V33h7.063l.937-8H32v-4.5c0-2 .4-2.5 2.5-2.5H40V10.063C39.234 9.977 35.962 9.75 32.344 9.75 25.905 9.75 22 13.18 22 19.031V25z"/></svg>
          </a>
          <a
            href="https://www.instagram.com/campus_beaupeyrat/"
            target="_blank"
            rel="noopener"
            aria-label="Instagram"
            class="social-icon"
          >
            <svg width="22" height="22" viewBox="0 0 50 50" fill="currentColor"><circle cx="25" cy="25" r="12" /><circle cx="37" cy="13" r="2" /><rect x="7" y="7" width="36" height="36" rx="10" ry="10" fill="none" stroke="currentColor" stroke-width="2"/></svg>
          </a>
          <a
            href="https://www.youtube.com/channel/UCQ2Iy1GN2P68UsgN640tf-Q"
            target="_blank"
            rel="noopener"
            aria-label="YouTube"
            class="social-icon"
          >
            <svg width="22" height="22" viewBox="0 0 50 50" fill="currentColor"><path d="M44.8 16.5s-.4-3.2-1.6-4.6c-1.5-1.7-3.2-1.7-4-1.8C35 10 25 10 25 10h0s-10 0-14.2.1c-.8.1-2.5.1-4 1.8-1.2 1.4-1.6 4.6-1.6 4.6S5 19.4 5 22.2v2.2c0 2.8.2 5.7.2 5.7s.4 3.2 1.6 4.6c1.5 1.7 3.5 1.6 4.4 1.8 3.2.3 13.8.3 13.8.3s10 0 14.2-.1c.8-.1 2.5-.1 4-1.8 1.2-1.4 1.6-4.6 1.6-4.6s.2-2.9.2-5.7V22c0-2.8-.2-5.7-.2-5.7zM20 29V19l9 5-9 5z"/></svg>
          </a>
        </div>
      </div>
    </div>
    <div class="footer-divider"></div>
    <div class="footer-bottom">
      &copy; 2024 <a href="https://edudata.beaupeyrat.com" target="_blank" rel="noopener">edudata.beaupeyrat.com</a> – Tous droits réservés.
    </div>
  </footer>
</template>

<script setup>
// Pas de logique spécifique ici pour l'instant
</script>

<style scoped>
.footer {
  background: linear-gradient(135deg, #181e2a 0%, #232b3e 100%);
  color: #f4f6fa;
  padding: 48px 20px 24px 20px;
  font-family: 'Inter', Arial, sans-serif;
  box-shadow: 0 -2px 16px 0 rgba(0,0,0,0.10);
  z-index: 1030 !important;
}

.footer-content {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 36px 48px;
  max-width: 1200px;
  margin: 0 auto;
  text-align: right;
}

.footer-section {
  flex: 1 1 260px;
  min-width: 220px;
}

.footer-section h3 {
  margin-bottom: 16px;
  font-size: 1.25rem;
  color: #7bb1fa;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.footer-contact .contact-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 8px;
  font-size: 1rem;
  flex-direction: row-reverse;
  text-align: right;
}

.footer-contact .icon {
  margin-left: 8px;
  margin-right: 0;
  font-size: 1.2rem;
  min-width: 22px;
  color: #0063FD;
}

.footer-contact a {
  color: #bbdaff;
  text-decoration: none;
  transition: color 0.2s;
  word-break: break-all;
}
.footer-contact a:hover {
  color: #7bb1fa;
}

.footer-links ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 8px;
}

.footer-links a {
  color: #bbdaff;
  text-decoration: none;
  border-bottom: 1px dashed #7bb1fa33;
  padding-bottom: 2px;
  transition: color 0.2s, border-bottom 0.2s;
}
.footer-links a:hover {
  color: #7bb1fa;
  border-bottom: 1px solid #7bb1fa;
}

.footer-social .social-links {
  display: flex;
  gap: 16px;
  margin-top: 8px;
  justify-content: flex-end;
}

.social-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #232b3e;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  color: #7bb1fa;
  font-size: 1.3rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: background 0.2s, color 0.2s, transform 0.15s;
  border: 1.5px solid #7bb1fa22;
}
.social-icon:hover {
  background: #7bb1fa;
  color: #232b3e;
  transform: translateY(-2px) scale(1.07);
  border-color: #7bb1fa;
}

.footer-divider {
  width: 100%;
  height: 1px;
  background: #394050;
  margin: 32px 0 16px 0;
  opacity: 0.2;
}

.footer-bottom {
  text-align: right;
  font-size: 1rem;
  color: #b0b8c9;
  letter-spacing: 0.2px;
}

.footer-bottom a {
  color: #7bb1fa;
  text-decoration: none;
  transition: color 0.2s;
}
.footer-bottom a:hover {
  color: #bbdaff;
}

@media (max-width: 900px) {
  .footer-content {
    flex-direction: column;
    gap: 32px;
    align-items: flex-end;
    text-align: right;
  }
  .footer-section {
    min-width: 0;
    width: 100%;
  }
  .footer-divider {
    margin: 28px 0 12px 0;
  }
  .footer-bottom {
    text-align: center;
  }
}
</style>