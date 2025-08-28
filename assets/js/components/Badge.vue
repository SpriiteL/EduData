<template>
  <div>
    <div class="header-gradient"></div> <!-- Rectangle dégradé bleu en haut -->
    <div class="badge-container">
      <h1 class="page-title">Gestion des Badges</h1>

      <!-- BOUTON AFFICHER / MASQUER -->
      <button @click="showExplanation = !showExplanation" class="btn btn-secondary" style="margin-bottom: 10px;">
        {{ showExplanation ? 'Masquer les explications' : 'Afficher les explications' }}
      </button>

      <!-- BLOC D'EXPLICATION -->
      <div v-show="showExplanation" class="explication">
        Cette page vous permet de gérer les badges de l'établissement. Vous pouvez ajouter de nouveaux badges, visualiser la liste des badges existants et supprimer des entrées.
        Pour ajouter un badge, remplissez tous les champs du formulaire : nom, prénom, date de traitement, classe, état du traitement et lieu.
        Après l'ajout, le badge sera affiché dans le tableau ci-dessous. Vous pouvez trier les données par colonne si nécessaire.
        Pour supprimer un badge, cliquez sur le bouton "Supprimer" à côté de l'entrée correspondante. Une confirmation sera demandée avant la suppression.
        Les états de traitement disponibles sont : "En attente", "Traitée" et "Refusée".
        Les classes disponibles vont de la 6ème jusqu'aux formations supérieures (BTS, Bachelor).
        Si vous rencontrez des problèmes ou avez des questions, n'hésitez pas à contacter l'administrateur du système.
        Merci de votre attention et bonne gestion des badges !
      </div>

      <!-- FORMULAIRE D'AJOUT -->
      <div class="header-section">
        <div class="form-section">
          <form id="badge-form" class="badge-form">
            <div class="form-row">
              <div class="form-group">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" v-model="formData.nom">
              </div>
              <div class="form-group">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" v-model="formData.prenom">
              </div>
              <div class="form-group">
                <label for="dateTraitement" class="form-label">Date Traitement</label>
                <input type="date" class="form-control" id="dateTraitement" name="dateTraitement" v-model="formData.dateTraitement">
              </div>
              <div class="form-group">
                <label for="classe" class="form-label">Classe</label>
                <select class="form-control" id="classe" name="classe" v-model="formData.classe">
                  <option value="6ème">6ème</option>
                  <option value="5ème">5ème</option>
                  <option value="4ème">4ème</option>
                  <option value="3ème">3ème</option>
                  <option value="Seconde">Seconde</option>
                  <option value="Premiere">Première</option>
                  <option value="Terminale">Terminal</option>
                  <option value="BTS">BTS</option>
                  <option value="Bachelor">Bachelor</option>
                </select>
              </div>
              <div class="form-group">
                <label for="etatTraitement" class="form-label">État du Traitement</label>
                <select class="form-control" id="etatTraitement" name="etatTraitement" v-model="formData.etatTraitement">
                  <option value="En attente">En attente</option>
                  <option value="Traitée">Traitée</option>
                  <option value="Refusée">Refusée</option>
                </select>
              </div>
              <div class="form-group">
                <label for="lieu" class="form-label">Lieu</label>
                <input type="text" class="form-control" id="lieu" name="lieu" placeholder="Lieu" v-model="formData.lieu">
              </div>
            </div>
            
            <div class="action-buttons">
              <button 
                type="button" 
                @click="addBadge"
                :disabled="isLoading"
                class="btn btn-success"
              >
                <i class="fas fa-plus"></i>
                {{ isLoading ? 'Ajout en cours...' : 'Ajouter' }}
              </button>
              <button 
                type="button" 
                @click="resetForm"
                class="btn btn-secondary"
              >
                <i class="fas fa-eraser"></i>
                Réinitialiser
              </button>
            </div>
          </form>
        </div>

        <div v-if="message" :class="['alert', messageType === 'success' ? 'alert-success' : 'alert-error']">
          {{ message }}
        </div>
      </div>

      <!-- TABLEAU DES BADGES -->
      <div class="table-section">
        <div class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th @click="sortBy('nom')" class="sortable">
                  Nom <i :class="getSortIcon('nom')"></i>
                </th>
                <th @click="sortBy('prenom')" class="sortable">
                  Prénom <i :class="getSortIcon('prenom')"></i>
                </th>
                <th @click="sortBy('dateTraitement')" class="sortable">
                  Date Traitement <i :class="getSortIcon('dateTraitement')"></i>
                </th>
                <th @click="sortBy('classe')" class="sortable">
                  Classe <i :class="getSortIcon('classe')"></i>
                </th>
                <th @click="sortBy('etatTraitement')" class="sortable">
                  État du Traitement <i :class="getSortIcon('etatTraitement')"></i>
                </th>
                <th @click="sortBy('lieu')" class="sortable">
                  Lieu <i :class="getSortIcon('lieu')"></i>
                </th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="7" class="loading-row">
                  <i class="fas fa-spinner fa-spin"></i> Chargement…
                </td>
              </tr>
              <tr v-else-if="badges.length === 0">
                <td colspan="7" class="no-data">Aucune donnée disponible</td>
              </tr>
              <tr v-else v-for="badge in paginatedData" :key="badge.id">
                <td>{{ badge.nom }}</td>
                <td>{{ badge.prenom }}</td>
                <td>{{ formatDate(badge.dateTraitement) }}</td>
                <td>{{ badge.classe }}</td>
                <td>
                  <span :class="getStatusClass(badge.etatTraitement)">
                    {{ badge.etatTraitement }}
                  </span>
                </td>
                <td>{{ badge.lieu }}</td>
                <td>
                  <button @click="deleteBadge(badge.id)" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="badges.length > itemsPerPage" class="pagination">
          <button @click="currentPage--" :disabled="currentPage === 1" class="btn btn-secondary">
            <i class="fas fa-chevron-left"></i>
          </button>
          <span class="page-info">Page {{ currentPage }} / {{ totalPages }}</span>
          <button @click="currentPage++" :disabled="currentPage === totalPages" class="btn btn-secondary">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>

      <div class="footer-section">
        <button @click="loadBadges" class="btn btn-primary btn-fullwidth">
          <i class="fas fa-sync-alt"></i> Rafraîchir
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BadgeManager',
  data() {
    return {
      badges: [],
      isLoading: false,
      message: '',
      messageType: 'success',
      sortField: 'dateTraitement',
      sortDirection: 'desc',
      currentPage: 1,
      itemsPerPage: 10,
      showExplanation: true,
      formData: {
        nom: '',
        prenom: '',
        dateTraitement: '',
        classe: '6ème',
        etatTraitement: 'En attente',
        lieu: ''
      }
    };
  },
  computed: {
    sortedData() {
      const data = [...this.badges];
      data.sort((a, b) => {
        let aVal = a[this.sortField];
        let bVal = b[this.sortField];
        if (typeof aVal === 'string') {
          aVal = aVal.toLowerCase();
          bVal = bVal.toLowerCase();
        }
        if (this.sortDirection === 'asc') {
          return aVal < bVal ? -1 : aVal > bVal ? 1 : 0;
        } else {
          return aVal > bVal ? -1 : aVal < bVal ? 1 : 0;
        }
      });
      return data;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      return this.sortedData.slice(start, start + this.itemsPerPage);
    },
    totalPages() {
      return Math.ceil(this.badges.length / this.itemsPerPage);
    },
  },
  methods: {
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toISOString().split('T')[0];
    },
    getStatusClass(status) {
      const classes = {
        'En attente': 'status-pending',
        'Traitée': 'status-success',
        'Refusée': 'status-error'
      };
      return classes[status] || '';
    },
    sortBy(field) {
      if (this.sortField === field) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortField = field;
        this.sortDirection = 'asc';
      }
      this.currentPage = 1;
    },
    getSortIcon(field) {
      if (this.sortField !== field) return 'fas fa-sort';
      return this.sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
    },
    resetForm() {
      this.formData = {
        nom: '',
        prenom: '',
        dateTraitement: '',
        classe: '6ème',
        etatTraitement: 'En attente',
        lieu: ''
      };
    },
    async loadBadges() {
      this.isLoading = true;
      try {
        const response = await fetch('/badge/list');
        const data = await response.json();
        this.badges = data;
      } catch (error) {
        console.error('Erreur lors du chargement des badges:', error);
        this.message = 'Erreur de chargement';
        this.messageType = 'error';
        setTimeout(() => this.message = '', 5000);
      } finally {
        this.isLoading = false;
      }
    },
    async addBadge() {
      if (!this.formData.nom || !this.formData.prenom || !this.formData.lieu) {
        this.message = 'Veuillez remplir tous les champs obligatoires';
        this.messageType = 'error';
        setTimeout(() => this.message = '', 5000);
        return;
      }

      this.isLoading = true;
      try {
        const formData = new FormData();
        Object.keys(this.formData).forEach(key => {
          formData.append(key, this.formData[key]);
        });

        const response = await fetch('/badge/add', {
          method: 'POST',
          body: formData
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.error || 'Erreur serveur');
        }

        const data = await response.json();
        this.badges.push(data);
        this.resetForm();
        this.message = 'Badge ajouté avec succès';
        this.messageType = 'success';
      } catch (error) {
        console.error('Erreur lors de l\'ajout du badge:', error);
        this.message = 'Erreur lors de l\'ajout : ' + error.message;
        this.messageType = 'error';
      } finally {
        this.isLoading = false;
        setTimeout(() => this.message = '', 5000);
      }
    },
    async deleteBadge(id) {
      if (!confirm('Êtes-vous sûr de vouloir supprimer ce badge?')) return;
      
      this.isLoading = true;
      try {
        const response = await fetch(`/badge/delete/${id}`, {
          method: 'DELETE'
        });
        
        const data = await response.json();
        if (data.success) {
          this.badges = this.badges.filter(badge => badge.id !== id);
          this.message = 'Badge supprimé avec succès';
          this.messageType = 'success';
        } else {
          throw new Error('Erreur lors de la suppression');
        }
      } catch (error) {
        console.error('Erreur lors de la suppression du badge:', error);
        this.message = 'Erreur lors de la suppression';
        this.messageType = 'error';
      } finally {
        this.isLoading = false;
        setTimeout(() => this.message = '', 5000);
      }
    }
  },
  mounted() {
    this.loadBadges();
  }
};
</script>

<style scoped>
.header-gradient {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 500px;
  background: linear-gradient(90deg, #003cff, #00aaff);
  z-index: 0;
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
}

.badge-container {
  max-width: 1500px;
  margin: 0 auto 0 0;
  padding-top: 320px;
  position: relative;
  z-index: 100;
  margin-top: -250px;
  margin-left: 300px;
}

.header-section {
  margin-bottom: 20px;
  position: relative;
  z-index: 100;
}

.page-title {
  font-size: 2rem;
  color: #ffffff;
  margin-bottom: 15px;
}

.form-section {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 15px;
  position: relative;
  z-index: 100;
}

.badge-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-label {
  font-weight: 600;
  color: #333;
  margin-bottom: 5px;
}

.form-control {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
  transition: border-color 0.3s;
}

.form-control:focus {
  outline: none;
  border-color: #1b60eb;
  box-shadow: 0 0 0 2px rgba(27, 96, 235, 0.2);
}

.action-buttons {
  display: flex;
  gap: 10px;
  justify-content: flex-start;
}

.action-buttons .btn {
  min-width: 140px;
}

.alert {
  margin-top: 10px;
  padding: 10px 15px;
  border-radius: 5px;
  font-weight: 600;
  position: relative;
  z-index: 100;
}

.alert-success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert-error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.table-section {
  background: white;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
  position: relative;
  z-index: 100;
}

.table-wrapper {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}

.data-table th, .data-table td {
  padding: 12px;
  border-bottom: 1px solid #e2e2e2;
  text-align: left;
}

.data-table th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #333;
}

.data-table th.sortable {
  cursor: pointer;
  user-select: none;
  transition: background-color 0.2s;
}

.data-table th.sortable:hover {
  background-color: #e9ecef;
}

.data-table th.sortable i {
  margin-left: 8px;
  color: #888;
}

.data-table tbody tr:hover {
  background-color: #f8f9fa;
}

.loading-row, .no-data {
  text-align: center;
  color: #666;
  font-style: italic;
  padding: 20px;
}

.status-pending {
  background: #fff3cd;
  color: #856404;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.status-success {
  background: #d4edda;
  color: #155724;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.status-error {
  background: #f8d7da;
  color: #721c24;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 15px;
  gap: 10px;
}

.page-info {
  font-weight: 600;
  color: #555;
}

.footer-section {
  text-align: center;
  margin-top: 20px;
  position: relative;
  z-index: 100;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.3s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #0056b3;
}

.btn-success {
  background: #1b60eb;
  color: white;
}

.btn-success:hover:not(:disabled) {
  background: #1749ab;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #5a6268;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: #c82333;
}

.btn-sm {
  padding: 4px 8px;
  font-size: 12px;
}

.btn-fullwidth {
  width: 100%;
  max-width: 1500px;
  margin-left: 0;
  font-size: 1.2rem;
  padding: 12px;
  border-radius: 8px;
  display: block;
  margin-bottom: 20px;
}

:global(body) {
  margin: 0;
  background-color: #eeeeee;
  font-family: 'Segoe UI', sans-serif;
}

.explication {
  margin-bottom: 20px;
  font-size: 0.70rem;
  line-height: 1.5;
  background-color: white;
  color: #6c757d;
  border-radius: 8px;
  padding: 20px 25px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
</style>