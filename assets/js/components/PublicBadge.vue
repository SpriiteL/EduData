<template>
  <div class="public-display-container">
    <!-- Arrière-plan décoratif -->
    <div class="background-decoration">
      <div class="bg-shape bg-shape-1"></div>
      <div class="bg-shape bg-shape-2"></div>
      <div class="bg-shape bg-shape-3"></div>
      <div class="bg-pattern"></div>
    </div>

    <!-- Header avec couleurs Beaupeyrat -->
    <header class="public-header">
      <!-- Bannière bleutée derrière les textes -->
      <div class="header-banner"></div>
      
      <div class="header-content">
        <div class="logo-section">
          <div class="logo-wrapper">
            <img 
              v-if="!logoError" 
              src="/asset/logobeaup.png" 
              alt="Logo Beaupeyrat" 
              class="logo"
              @error="handleLogoError" 
            />
            <i v-else class="fas fa-graduation-cap logo-icon"></i>
          </div>
          <div class="school-info">
            <h1 class="school-name">BEAUPEYRAT</h1>
            <p class="school-subtitle">Institution Scolaire</p>
          </div>
        </div>
        
        <div class="title-section">
          <h2 class="main-title">
            <i class="fas fa-check-circle"></i>
            BADGES VALIDÉS
          </h2>
          <div class="live-indicator">
            <div class="pulse-dot"></div>
            <span>TEMPS RÉEL</span>
          </div>
        </div>

        <div class="stats-section">
          <div class="stat-card">
            <div class="stat-number">{{ badges.length }}</div>
            <div class="stat-label">Badges traités</div>
          </div>
        </div>
      </div>
    </header>

    <!-- Contenu principal -->
    <main class="public-main">
      <!-- Loading state -->
      <div v-if="loading" class="loading-display">
        <div class="big-loader"></div>
        <h3>Chargement des données...</h3>
      </div>
      
      <!-- Empty state -->
      <div v-else-if="badges.length === 0" class="empty-display">
        <i class="fas fa-clock"></i>
        <h3>En attente de validation</h3>
        <p>Les badges apparaîtront ici une fois traités</p>
      </div>
      
      <!-- Grille des badges -->
      <div v-else class="badges-grid">
        <div 
          v-for="(badge, index) in badges" 
          :key="index"
          class="badge-card"
          :style="{ '--delay': index * 0.1 + 's' }"
        >
          <div class="badge-content">
            <div class="badge-header">
              <div class="student-name">{{ badge.nom }}</div>
              <div class="student-firstname">{{ badge.prenom }}</div>
              <div class="student-class" :class="getClassColor(badge.classe)">{{ badge.classe }}</div>
              <div class="validation-status">
                <div class="validated-badge">
                  <i class="fas fa-check"></i>
                  VALIDÉ
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="public-footer">
      <div class="footer-content">
        <p>&copy; 2024 Institution Beaupeyrat - Système de gestion des badges</p>
        <div class="footer-info">
          <div class="update-time">
            Dernière mise à jour : {{ currentTime }}
          </div>
          <div v-if="!isScrolling" class="auto-scroll-indicator">
            <div class="scroll-dot"></div>
            <span>AUTO-SCROLL ACTIF</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'PublicBadge',
  setup() {
    const badges = ref([])
    const loading = ref(true)
    const logoError = ref(false)
    const currentTime = ref('')
    let refreshInterval = null
    let timeInterval = null
    let autoScrollInterval = null
    const isScrolling = ref(false)

    // Fonction pour attribuer une couleur à chaque classe
    const getClassColor = (classe) => {
      const classColors = {
        '6ème': 'class-red',
        '6EME': 'class-red',
        '5ème': 'class-orange', 
        '5EME': 'class-orange',
        '4ème': 'class-yellow',
        '4EME': 'class-yellow',
        '3ème': 'class-green',
        '3EME': 'class-green',
        'Seconde': 'class-blue',
        'SECONDE': 'class-blue',
        '2NDE': 'class-blue',
        'Première': 'class-indigo',
        'PREMIERE': 'class-indigo',
        '1ERE': 'class-indigo',
        'Terminal': 'class-purple',
        'TERMINAL': 'class-purple',
        'TERMINALE': 'class-purple',
        'TERM': 'class-purple',
        'BTS': 'class-teal',
        'Bachelor': 'class-cyan',
        'BACHELOR': 'class-cyan'
      }
      
      // Normaliser la classe pour gérer les variations de casse et d'écriture
      const normalizedClasse = classe.toString().toUpperCase().trim();
      
      // Vérifier les correspondances exactes
      if (classColors[classe]) {
        return classColors[classe];
      }
      if (classColors[normalizedClasse]) {
        return classColors[normalizedClasse];
      }
      
      // Recherche partielle pour gérer les variations (ex: "6ème A", "Seconde B", etc.)
      for (const [key, color] of Object.entries(classColors)) {
        if (normalizedClasse.includes(key.toUpperCase()) || key.toUpperCase().includes(normalizedClasse)) {
          return color;
        }
      }
      
      // Si aucune correspondance, utiliser une couleur par défaut basée sur un hash
      const defaultColors = ['class-slate', 'class-zinc', 'class-stone'];
      const hash = classe.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
      return defaultColors[hash % defaultColors.length];
    }

    // Fonction pour charger les badges traités
    const loadTreatedBadges = async () => {
      try {
        const response = await fetch('/badge/treated')
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        const data = await response.json()
        badges.value = data
        loading.value = false
        updateTime()
      } catch (error) {
        console.error('Erreur lors du chargement des badges traités:', error)
        loading.value = false
      }
    }

    // Mettre à jour l'heure
    const updateTime = () => {
      const now = new Date()
      currentTime.value = now.toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      })
    }

    // Gestion d'erreur de chargement du logo
    const handleLogoError = () => {
      logoError.value = true
    }

    // Auto-scroll automatique
    const startAutoScroll = () => {
      if (autoScrollInterval) return
      
      let direction = 1 // 1 pour descendre, -1 pour monter
      let currentPosition = 0
      const scrollSpeed = 0.2 // pixels par frame (plus lent)
      const pauseDuration = 8000 // pause en haut et en bas (8 secondes)
      let isPaused = false
      
      const smoothScroll = () => {
        if (isPaused) return
        
        const maxScroll = document.documentElement.scrollHeight - window.innerHeight
        
        // Si la page n'est pas scrollable, pas besoin d'auto-scroll
        if (maxScroll <= 0) return
        
        currentPosition += (scrollSpeed * direction)
        
        // Si on arrive en bas
        if (currentPosition >= maxScroll && direction === 1) {
          currentPosition = maxScroll
          direction = -1
          isPaused = true
          setTimeout(() => { isPaused = false }, pauseDuration)
        }
        // Si on arrive en haut
        else if (currentPosition <= 0 && direction === -1) {
          currentPosition = 0
          direction = 1
          isPaused = true
          setTimeout(() => { isPaused = false }, pauseDuration)
        }
        
        window.scrollTo({
          top: currentPosition,
          behavior: 'auto'
        })
      }
      
      autoScrollInterval = setInterval(smoothScroll, 32) // ~30fps pour plus de fluidité lente
    }

    const stopAutoScroll = () => {
      if (autoScrollInterval) {
        clearInterval(autoScrollInterval)
        autoScrollInterval = null
      }
    }

    // Détection d'interaction utilisateur pour pause auto-scroll
    const handleUserInteraction = () => {
      if (!isScrolling.value) {
        isScrolling.value = true
        stopAutoScroll()
        
        // Reprendre l'auto-scroll après 8 secondes d'inactivité
        setTimeout(() => {
          isScrolling.value = false
          startAutoScroll()
        }, 8000)
      }
    }

    // Lifecycle hooks
    onMounted(() => {
      console.log('Component PublicBadge mounted for public display')
      loadTreatedBadges()
      updateTime()
      
      // Rafraîchir les données toutes les 10 secondes
      refreshInterval = setInterval(loadTreatedBadges, 10000)
      // Mettre à jour l'heure chaque seconde
      timeInterval = setInterval(updateTime, 1000)
      
      // Démarrer l'auto-scroll après un délai pour laisser le temps au contenu de se charger
      setTimeout(() => {
        startAutoScroll()
      }, 5000) // Délai de démarrage augmenté à 5 secondes
      
      // Écouter les interactions utilisateur
      window.addEventListener('wheel', handleUserInteraction, { passive: true })
      window.addEventListener('touchstart', handleUserInteraction, { passive: true })
      window.addEventListener('keydown', handleUserInteraction)
    })

    onUnmounted(() => {
      if (refreshInterval) {
        clearInterval(refreshInterval)
      }
      if (timeInterval) {
        clearInterval(timeInterval)
      }
      stopAutoScroll()
      
      // Nettoyer les event listeners
      window.removeEventListener('wheel', handleUserInteraction)
      window.removeEventListener('touchstart', handleUserInteraction)
      window.removeEventListener('keydown', handleUserInteraction)
    })

    return {
      badges,
      loading,
      logoError,
      currentTime,
      isScrolling,
      handleLogoError,
      getClassColor
    }
  }
}
</script>

<style scoped>
/* Variables couleurs Beaupeyrat */
:root {
  --beaupeyrat-blue: #003366;
  --beaupeyrat-light-blue: #00609C;
  --beaupeyrat-navy: #001a33;
  --beaupeyrat-accent: #0066cc;
  --success-green: #28a745;
  --warning-orange: #fd7e14;
  --light-bg: #f8fafc;
  --white: #ffffff;
  --gray-100: #f1f5f9;
  --gray-200: #e2e8f0;
  --gray-600: #64748b;
  --gray-700: #374151;
  --gray-800: #1e293b;
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -2px rgb(0 0 0 / 0.05);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
}

/* Container principal */
.public-display-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  font-family: 'Inter', 'Segoe UI', sans-serif;
  color: var(--gray-800);
  position: relative;
  overflow-x: hidden;
  scroll-behavior: auto; /* Désactive le smooth scroll natif pour notre contrôle */
}

/* Arrière-plan décoratif */
.background-decoration {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 0;
  overflow: hidden;
  background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 50%, #cbd5e1 100%);
}

.bg-shape {
  position: absolute;
  border-radius: 50%;
  opacity: 0.15;
  animation: float-bg 20s ease-in-out infinite;
  filter: blur(1px);
}

.bg-shape-1 {
  width: 500px;
  height: 500px;
  background: linear-gradient(135deg, var(--beaupeyrat-blue) 0%, var(--beaupeyrat-light-blue) 100%);
  top: -150px;
  right: -150px;
  animation-delay: 0s;
}

.bg-shape-2 {
  width: 700px;
  height: 700px;
  background: linear-gradient(135deg, var(--beaupeyrat-light-blue) 0%, var(--beaupeyrat-accent) 100%);
  bottom: -200px;
  left: -200px;
  animation-delay: -10s;
}

.bg-shape-3 {
  width: 350px;
  height: 350px;
  background: linear-gradient(135deg, var(--success-green) 0%, var(--warning-orange) 100%);
  top: 40%;
  right: -100px;
  animation-delay: -5s;
}

.bg-pattern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    radial-gradient(circle at 20% 30%, rgba(0, 51, 102, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(0, 96, 156, 0.06) 0%, transparent 50%),
    radial-gradient(circle at 60% 20%, rgba(40, 167, 69, 0.04) 0%, transparent 50%);
  background-size: 300px 300px, 500px 500px, 400px 400px;
  animation: drift 40s linear infinite;
}

/* Header Beaupeyrat */
.public-header {
  background: linear-gradient(135deg, var(--beaupeyrat-blue) 0%, var(--beaupeyrat-light-blue) 100%);
  padding: 2rem 0;
  box-shadow: var(--shadow-xl);
  color: white;
  position: relative;
  z-index: 100;
}

/* Bannière bleutée derrière les textes */
.header-banner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 95%;
  height: 80%;
  background: linear-gradient(135deg, 
    rgba(0, 96, 156, 0.842) 0%, 
    rgba(0, 86, 172, 0.788) 30%, 
    rgba(0, 102, 204, 0.774) 70%, 
    rgba(0, 96, 156, 0.747) 100%
  );
  backdrop-filter: blur(15px);
  border-radius: 25px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  box-shadow: 
    0 25px 50px -12px rgba(0, 51, 102, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.2),
    inset 0 -1px 0 rgba(0, 51, 102, 0.3);
  z-index: 1;
  animation: bannerFloat 8s ease-in-out infinite;
}

.public-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    linear-gradient(45deg, rgba(255, 255, 255, 0.05) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(255, 255, 255, 0.05) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(255, 255, 255, 0.05) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, 0.05) 75%);
  background-size: 60px 60px;
  background-position: 0 0, 0 30px, 30px -30px, -30px 0px;
  opacity: 0.1;
  z-index: 0;
}

.header-content {
  max-width: 95%;
  margin: 0 auto;
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 3rem;
  position: relative;
  z-index: 2;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.logo-wrapper {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid rgba(255, 255, 255, 0.3);
  box-shadow: var(--shadow-lg);
  animation: float 6s ease-in-out infinite;
}

.logo {
  width: 75px;
  height: 75px;
  border-radius: 50%;
  object-fit: cover;
}

.logo-icon {
  font-size: 2.5rem;
  color: rgba(255, 255, 255, 0.9);
}

.school-info {
  text-align: left;
}

.school-name {
  font-size: 3rem;
  font-weight: 800;
  margin: 0;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
  letter-spacing: 1px;
  animation: slideInLeft 1s ease-out;
}

.school-subtitle {
  font-size: 1.2rem;
  opacity: 0.9;
  margin: 0.5rem 0 0 0;
  font-weight: 400;
}

.title-section {
  text-align: center;
}

.main-title {
  font-size: 3rem;
  font-weight: 700;
  margin: 0;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.main-title i {
  color: var(--success-green);
  filter: drop-shadow(0 0 8px rgba(40, 167, 69, 0.3));
}

.live-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.8rem;
  margin-top: 1rem;
  font-size: 1rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.9);
}

.pulse-dot {
  width: 10px;
  height: 10px;
  background: var(--success-green);
  border-radius: 50%;
  animation: pulse-dot 2s infinite;
  box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
}

.stats-section {
  text-align: center;
}

.stat-card {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem 1.5rem;
  border: 2px solid rgba(255, 255, 255, 0.2);
  box-shadow: var(--shadow-lg);
  animation: slideInRight 1s ease-out;
}

.stat-number {
  font-size: 3rem;
  font-weight: 800;
  color: var(--warning-orange);
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.stat-label {
  font-size: 1rem;
  font-weight: 500;
  opacity: 0.9;
  margin-top: 0.5rem;
}

/* Main content */
.public-main {
  padding: 3rem 2rem;
  min-height: 60vh;
  position: relative;
  z-index: 10;
}

/* Loading state */
.loading-display {
  text-align: center;
  padding: 4rem 0;
  color: var(--gray-600);
}

.big-loader {
  width: 60px;
  height: 60px;
  border: 4px solid var(--gray-200);
  border-top: 4px solid var(--beaupeyrat-light-blue);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 2rem auto;
}

.loading-display h3 {
  font-size: 2rem;
  font-weight: 600;
  margin: 0;
  color: var(--beaupeyrat-blue);
}

/* Empty state */
.empty-display {
  text-align: center;
  padding: 4rem 0;
  color: var(--gray-600);
}

.empty-display i {
  font-size: 4rem;
  margin-bottom: 2rem;
  color: var(--beaupeyrat-light-blue);
  opacity: 0.6;
}

.empty-display h3 {
  font-size: 2.5rem;
  font-weight: 600;
  margin: 0 0 1rem 0;
  color: var(--beaupeyrat-blue);
}

.empty-display p {
  font-size: 1.3rem;
  margin: 0;
}

/* Grille des badges */
.badges-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
  gap: 2rem;
  max-width: 95%;
  margin: 0 auto;
}

/* Carte de badge redessinée */
.badge-card {
  background: rgba(255, 255, 255, 0.98);
  border-radius: 16px;
  box-shadow: var(--shadow-xl);
  border: 2px solid rgba(0, 96, 156, 0.1);
  border-left: 6px solid var(--beaupeyrat-blue);
  transform: translateY(30px);
  opacity: 0;
  animation: slideInCard 0.6s ease-out var(--delay) forwards;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(15px);
  min-height: 120px;
}

.badge-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 30px -5px rgba(0, 51, 102, 0.2);
  border-left-color: var(--beaupeyrat-accent);
}

.badge-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--beaupeyrat-blue) 0%, var(--beaupeyrat-light-blue) 50%, var(--beaupeyrat-accent) 100%);
}

.badge-content {
  padding: 2rem;
  position: relative;
  z-index: 2;
  height: 100%;
  display: flex;
  align-items: center;
}

.badge-header {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr auto;
  align-items: center;
  gap: 2rem;
  width: 100%;
}

.student-name {
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--beaupeyrat-blue);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  line-height: 1.2;
}

.student-firstname {
  font-size: 1.6rem;
  font-weight: 600;
  color: var(--gray-700);
  text-transform: capitalize;
  line-height: 1.2;
}

.student-class {
  font-size: 1.3rem;
  font-weight: 700;
  padding: 0.8rem 1.5rem;
  border-radius: 25px;
  border: 2px solid transparent;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  box-shadow: var(--shadow-lg);
  transition: all 0.3s ease;
}

/* Classes avec couleurs différenciées */
.class-red {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  border-color: rgba(239, 68, 68, 0.3);
}

.class-orange {
  background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
  border-color: rgba(249, 115, 22, 0.3);
}

.class-yellow {
  background: linear-gradient(135deg, #ca8a04 0%, #eab308 100%);
  border-color: rgba(234, 179, 8, 0.3);
}

.class-green {
  background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
  border-color: rgba(34, 197, 94, 0.3);
}

.class-teal {
  background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
  border-color: rgba(20, 184, 166, 0.3);
}

.class-blue {
  background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
  border-color: rgba(59, 130, 246, 0.3);
}

.class-indigo {
  background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
  border-color: rgba(99, 102, 241, 0.3);
}

.class-purple {
  background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
  border-color: rgba(139, 92, 246, 0.3);
}

.class-pink {
  background: linear-gradient(135deg, #db2777 0%, #ec4899 100%);
  border-color: rgba(236, 72, 153, 0.3);
}

.class-rose {
  background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%);
  border-color: rgba(244, 63, 94, 0.3);
}

.class-cyan {
  background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
  border-color: rgba(6, 182, 212, 0.3);
}

.class-emerald {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  border-color: rgba(16, 185, 129, 0.3);
}

.class-violet {
  background: linear-gradient(135deg, #7c2d12 0%, #a855f7 100%);
  border-color: rgba(168, 85, 247, 0.3);
}

.class-amber {
  background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
  border-color: rgba(245, 158, 11, 0.3);
}

.class-lime {
  background: linear-gradient(135deg, #65a30d 0%, #84cc16 100%);
  border-color: rgba(132, 204, 22, 0.3);
}

.class-sky {
  background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
  border-color: rgba(14, 165, 233, 0.3);
}

.class-fuchsia {
  background: linear-gradient(135deg, #c026d3 0%, #d946ef 100%);
  border-color: rgba(217, 70, 239, 0.3);
}

.class-slate {
  background: linear-gradient(135deg, #475569 0%, #64748b 100%);
  border-color: rgba(100, 116, 139, 0.3);
}

.class-zinc {
  background: linear-gradient(135deg, #52525b 0%, #71717a 100%);
  border-color: rgba(113, 113, 122, 0.3);
}

.class-stone {
  background: linear-gradient(135deg, #57534e 0%, #78716c 100%);
  border-color: rgba(120, 113, 108, 0.3);
}

.validation-status {
  display: flex;
  align-items: center;
  justify-content: center;
}

.validated-badge {
  background: linear-gradient(135deg, var(--success-green) 0%, #20c997 100%);
  border-radius: 30px;
  padding: 1rem 2rem;
  font-weight: 800;
  font-size: 1.1rem;
  color: white;
  display: flex;
  align-items: center;
  gap: 0.8rem;
  box-shadow: var(--shadow-lg);
  border: 2px solid rgba(255, 255, 255, 0.3);
  white-space: nowrap;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.validated-badge i {
  font-size: 1.3rem;
  filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.5));
}

/* Footer */
.public-footer {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(15px);
  border-top: 3px solid var(--beaupeyrat-light-blue);
  padding: 1.5rem 0;
  margin-top: auto;
  position: relative;
  z-index: 100;
}

.footer-content {
  max-width: 95%;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--gray-600);
  font-size: 1rem;
}

.footer-content p {
  margin: 0;
  font-weight: 500;
}

.footer-info {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.update-time {
  font-weight: 600;
  color: var(--beaupeyrat-blue);
  background: rgba(255, 255, 255, 0.8);
  padding: 0.5rem 1rem;
  border-radius: 20px;
  box-shadow: var(--shadow-lg);
  border: 1px solid rgba(0, 96, 156, 0.1);
}

.auto-scroll-indicator {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--success-green);
  background: rgba(40, 167, 69, 0.15);
  padding: 0.7rem 1.3rem;
  border-radius: 25px;
  border: 2px solid rgba(40, 167, 69, 0.3);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.scroll-dot {
  width: 10px;
  height: 10px;
  background: var(--success-green);
  border-radius: 50%;
  animation: pulse-scroll 3s infinite;
}

/* Animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

@keyframes bannerFloat {
  0%, 100% { 
    transform: translate(-50%, -50%) scale(1); 
    opacity: 0.9; 
  }
  50% { 
    transform: translate(-50%, -50%) scale(1.02); 
    opacity: 1; 
  }
}

@keyframes float-bg {
  0%, 100% { 
    transform: translateY(0px) rotate(0deg); 
    opacity: 0.03; 
  }
  33% { 
    transform: translateY(-20px) rotate(120deg); 
    opacity: 0.05; 
  }
  66% { 
    transform: translateY(10px) rotate(240deg); 
    opacity: 0.02; 
  }
}

@keyframes drift {
  0% { transform: translateX(0) translateY(0); }
  25% { transform: translateX(-100px) translateY(-50px); }
  50% { transform: translateX(-200px) translateY(0); }
  75% { transform: translateX(-300px) translateY(50px); }
  100% { transform: translateX(-400px) translateY(0); }
}

@keyframes pulse-dot {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.3); opacity: 0.7; }
}

@keyframes slideInLeft {
  from { transform: translateX(-80px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes slideInRight {
  from { transform: translateX(80px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes slideInCard {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes pulse-scroll {
  0%, 100% { 
    transform: scale(1); 
    opacity: 1; 
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
  }
  50% { 
    transform: scale(1.3); 
    opacity: 0.6; 
    box-shadow: 0 0 15px rgba(40, 167, 69, 0.6);
  }
}

/* Responsive pour grands écrans */
@media (min-width: 1920px) {
  .school-name {
    font-size: 4rem;
  }
  
  .main-title {
    font-size: 4rem;
  }
  
  .student-name {
    font-size: 2.2rem;
  }
  
  .badges-grid {
    grid-template-columns: repeat(auto-fit, minmax(550px, 1fr));
    gap: 2.5rem;
  }
}

/* Pour écrans moyens */
@media (max-width: 1200px) {
  .header-content {
    grid-template-columns: 1fr;
    gap: 2rem;
    text-align: center;
  }
  
  .badges-grid {
    grid-template-columns: 1fr;
  }
  
  .badge-header {
    grid-template-columns: 1fr;
    gap: 1rem;
    text-align: center;
  }
  
  .student-name, .student-firstname {
    text-align: center;
  }
  
  .student-class {
    max-width: 200px;
    margin: 0 auto;
  }
}

@media (max-width: 768px) {
  .school-name {
    font-size: 2.2rem;
  }
  
  .main-title {
    font-size: 2.2rem;
  }
  
  .badges-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .badge-card {
    margin: 0 1rem;
  }
  
  .badge-content {
    padding: 1.5rem;
  }
  
  .badge-header {
    grid-template-columns: 1fr;
    gap: 1.5rem;
    text-align: center;
  }
  
  .student-name {
    font-size: 1.5rem;
  }
  
  .footer-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .footer-info {
    flex-direction: column;
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .badges-grid {
    grid-template-columns: 1fr;
    margin: 0 0.5rem;
  }
  
  .badge-card {
    margin: 0;
  }
}
</style>