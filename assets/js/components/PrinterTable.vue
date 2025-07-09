<template>
  <div class="printer-usage-container">
    <div class="header-section">
      <h1 class="page-title">Statistiques d'utilisation des imprimantes</h1>
      
      <div class="import-section">
        <div class="file-upload-wrapper">
          <input 
            ref="fileInput"
            type="file" 
            accept=".csv"
            @change="handleFileSelect"
            class="file-input"
            id="csvFile"
          />
          <label for="csvFile" class="file-label">
            <i class="fas fa-upload"></i>
            Choisir un fichier CSV
          </label>
          <span v-if="selectedFile" class="file-name">{{ selectedFile.name }}</span>
        </div>
        
        <div class="action-buttons">
          <button 
            @click="importCsv" 
            :disabled="!selectedFile || isLoading"
            class="btn btn-primary"
          >
            <i class="fas fa-file-import"></i>
            {{ isLoading ? 'Import en cours...' : 'Importer' }}
          </button>
          
          <button 
            @click="clearData" 
            :disabled="isLoading"
            class="btn btn-danger"
          >
            <i class="fas fa-trash"></i>
            Effacer les données
          </button>
        </div>
      </div>

      <div v-if="message" :class="['alert', messageType === 'success' ? 'alert-success' : 'alert-error']">
        {{ message }}
      </div>
    </div>

    <div class="table-section">
      <div class="table-header">
        <h2>Résultats par utilisateur</h2>
        <div class="table-info">
          <span class="total-users">{{ filteredData.length }} utilisateur(s)</span>
          <div class="search-wrapper">
            <input 
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher un utilisateur..."
              class="search-input"
              @input="currentPage = 1"
            />
            <i class="fas fa-search search-icon"></i>
          </div>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th @click="sortBy('username')" class="sortable">
                Utilisateur
                <i :class="getSortIcon('username')"></i>
              </th>
              <th @click="sortBy('totalBlack')" class="sortable">
                Total Noir
                <i :class="getSortIcon('totalBlack')"></i>
              </th>
              <th @click="sortBy('totalColor')" class="sortable">
                Total Couleur
                <i :class="getSortIcon('totalColor')"></i>
              </th>
              <th @click="sortBy('totalScans')" class="sortable">
                Total Scans
                <i :class="getSortIcon('totalScans')"></i>
              </th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="5" class="loading-row">
                <i class="fas fa-spinner fa-spin"></i>
                Chargement des données...
              </td>
            </tr>
            <tr v-else-if="filteredData.length === 0">
              <td colspan="5" class="no-data">
                {{ searchQuery ? 'Aucun utilisateur trouvé' : 'Aucune donnée disponible' }}
              </td>
            </tr>
            <tr v-else v-for="user in paginatedData" :key="user.id">
              <td class="username-cell">
                <i class="fas fa-user"></i>
                {{ user.username }}
              </td>
              <td class="number-cell">
                <span class="badge badge-dark">{{ user.totalBlack }}</span>
              </td>
              <td class="number-cell">
                <span class="badge badge-color">{{ user.totalColor }}</span>
              </td>
              <td class="number-cell">
                <span class="badge badge-info">{{ user.totalScans }}</span>
              </td>
              <td class="number-cell total-cell">
                <span class="badge badge-total">{{ user.totalBlack + user.totalColor + user.totalScans }}</span>
              </td>
            </tr>
          </tbody>
          <tfoot v-if="filteredData.length > 0">
            <tr>
              <td><strong>Total général</strong></td>
              <td class="number-cell">
                <span class="badge badge-total-general">{{ totalBlack }}</span>
              </td>
              <td class="number-cell">
                <span class="badge badge-total-general">{{ totalColor }}</span>
              </td>
              <td class="number-cell">
                <span class="badge badge-total-general">{{ totalScans }}</span>
              </td>
              <td class="number-cell total-cell">
                <span class="badge badge-total-general">{{ totalBlack + totalColor + totalScans }}</span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div v-if="filteredData.length > itemsPerPage" class="pagination">
        <button 
          @click="currentPage--" 
          :disabled="currentPage === 1"
          class="btn btn-secondary"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        
        <span class="page-info">
          Page {{ currentPage }} sur {{ totalPages }}
        </span>
        
        <button 
          @click="currentPage++" 
          :disabled="currentPage === totalPages"
          class="btn btn-secondary"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PrinterUsage',
  data() {
    return {
      printerData: [],
      selectedFile: null,
      isLoading: false,
      message: '',
      messageType: 'success',
      searchQuery: '',
      sortField: 'username',
      sortDirection: 'asc',
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  computed: {
    filteredData() {
      let filtered = this.printerData;

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(user =>
          user.username.toLowerCase().includes(query)
        );
      }

      filtered.sort((a, b) => {
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

      return filtered;
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredData.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredData.length / this.itemsPerPage);
    },
    // Calcul des totaux généraux
    totalBlack() {
      return this.filteredData.reduce((sum, user) => sum + user.totalBlack, 0);
    },
    totalColor() {
      return this.filteredData.reduce((sum, user) => sum + user.totalColor, 0);
    },
    totalScans() {
      return this.filteredData.reduce((sum, user) => sum + user.totalScans, 0);
    }
  },
  methods: {
    async loadData() {
      this.isLoading = true;
      try {
        const response = await axios.get('/printer-stats/data');
        this.printerData = response.data;
      } catch (error) {
        this.showMessage('Erreur lors du chargement des données', 'error');
      } finally {
        this.isLoading = false;
      }
    },
    handleFileSelect(event) {
      this.selectedFile = event.target.files[0];
    },
    async importCsv() {
      if (!this.selectedFile) {
        this.showMessage('Veuillez sélectionner un fichier CSV', 'error');
        return;
      }

      this.isLoading = true;
      const formData = new FormData();
      formData.append('csvFile', this.selectedFile);

      try {
        const response = await axios.post('/printer-stats/import', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.success) {
          this.showMessage(`Import réussi! ${response.data.usersProcessed} utilisateur(s) traité(s)`, 'success');
          await this.loadData();
          this.selectedFile = null;
          this.$refs.fileInput.value = '';
        } else {
          this.showMessage(response.data.error || 'Erreur lors de l\'import', 'error');
        }
      } catch (error) {
        this.showMessage('Erreur lors de l\'import du fichier', 'error');
      } finally {
        this.isLoading = false;
      }
    },
    async clearData() {
      if (!confirm('Êtes-vous sûr de vouloir effacer toutes les données ?')) {
        return;
      }

      this.isLoading = true;
      try {
        const response = await axios.delete('/printer-stats/clear');
        if (response.data.success) {
          this.showMessage('Données effacées avec succès', 'success');
          this.printerData = [];
        } else {
          this.showMessage(response.data.error || 'Erreur lors de la suppression', 'error');
        }
      } catch (error) {
        this.showMessage('Erreur lors de la suppression', 'error');
      } finally {
        this.isLoading = false;
      }
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
      if (this.sortField !== field) {
        return 'fas fa-sort';
      }
      return this.sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
    },
    showMessage(text, type) {
      this.message = text;
      this.messageType = type;
      setTimeout(() => {
        this.message = '';
      }, 5000);
    }
  },
  mounted() {
    this.loadData();
  }
};
</script>

<style scoped>
.printer-usage-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.header-section {
  margin-bottom: 30px;
}

.page-title {
  color: #333;
  margin-bottom: 20px;
  font-size: 2rem;
}

.import-section {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.file-upload-wrapper {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
}

.file-input {
  display: none;
}

.file-label {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #007bff;
  color: white;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.file-label:hover {
  background-color: #0056b3;
}

.file-name {
  font-weight: 600;
  color: #555;
}

.action-buttons button {
  margin-right: 10px;
  min-width: 140px;
}

.alert {
  padding: 10px 15px;
  border-radius: 5px;
  margin-top: 10px;
  font-weight: 600;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
}

.alert-error {
  background-color: #f8d7da;
  color: #721c24;
}

.table-section {
  background: white;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 10px;
}

.table-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #333;
}

.table-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.total-users {
  font-weight: 600;
  color: #666;
}

.search-wrapper {
  position: relative;
  width: 250px;
}

.search-input {
  width: 100%;
  padding: 8px 35px 8px 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.search-icon {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #aaa;
}

.table-wrapper {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
  color: #444;
}

.data-table th,
.data-table td {
  padding: 10px 12px;
  border-bottom: 1px solid #e2e2e2;
  text-align: left;
}

.data-table th.sortable {
  cursor: pointer;
  user-select: none;
  white-space: nowrap;
}

.data-table th.sortable i {
  margin-left: 8px;
  color: #888;
}

.username-cell i {
  margin-right: 8px;
  color: #007bff;
}

.number-cell {
  text-align: right;
  white-space: nowrap;
}

.badge {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 15px;
  font-weight: 600;
  font-size: 0.875rem;
  color: white;
  min-width: 40px;
  text-align: center;
}

.badge-dark {
  background-color: #343a40;
}

.badge-color {
  background-color: #28a745;
}

.badge-info {
  background-color: #17a2b8;
}

.badge-total {
  background-color: #ffc107;
  color: #212529;
}

/* Nouveau style pour les bulles du total général */
.badge-total-general {
  background-color: #6c757d; /* gris */
  color: white;
}

.loading-row {
  text-align: center;
  color: #666;
  font-style: italic;
}

.no-data {
  text-align: center;
  color: #999;
  font-style: italic;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
  gap: 15px;
}

.pagination button {
  min-width: 40px;
  padding: 6px 10px;
}

.page-info {
  font-weight: 600;
  color: #555;
}
</style>
