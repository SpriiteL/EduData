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
              @input="currentPage = 1"
              type="text"
              placeholder="Rechercher un utilisateur..."
              class="search-input"
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
            <tr v-else-if="filteredData.length === 0 && !isLoading">
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

      // Filtrage par recherche
      if (this.searchQuery) {
        filtered = filtered.filter(user =>
          user.username.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      }

      // Tri
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
          headers: {
            'Content-Type': 'multipart/form-data'
          }
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
  watch: {
    searchQuery() {
      this.currentPage = 1;
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
  transition: background-color 0.2s;
}

.file-label:hover {
  background: #0056b3;
}

.file-name {
  color: #666;
  font-style: italic;
}

.action-buttons {
  display: flex;
  gap: 10px;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 14px;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background: #28a745;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #218838;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: #c82333;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #5a6268;
}

.alert {
  padding: 12px 16px;
  border-radius: 5px;
  margin-bottom: 20px;
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
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  overflow: hidden;
}

.table-header {
  background: #f8f9fa;
  padding: 20px;
  border-bottom: 1px solid #dee2e6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.table-header h2 {
  margin: 0;
  color: #333;
}

.table-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.total-users {
  color: #666;
  font-size: 14px;
}

.search-wrapper {
  position: relative;
}

.search-input {
  padding: 8px 35px 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  width: 250px;
}

.search-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
}

.table-wrapper {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  background: #f8f9fa;
  padding: 15px;
  text-align: left;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #dee2e6;
}

.data-table th.sortable {
  cursor: pointer;
  user-select: none;
  transition: background-color 0.2s;
}

.data-table th.sortable:hover {
  background: #e9ecef;
}

.data-table td {
  padding: 12px 15px;
  border-bottom: 1px solid #dee2e6;
}

.data-table tr:hover {
  background: #f8f9fa;
}

.username-cell {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
}

.number-cell {
  text-align: center;
}

.total-cell {
  font-weight: 600;
}

.badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
}

.badge-dark {
  background: #343a40;
  color: white;
}

.badge-color {
  background: #17a2b8;
  color: white;
}

.badge-info {
  background: #6f42c1;
  color: white;
}

.badge-total {
  background: #28a745;
  color: white;
}

.loading-row, .no-data {
  text-align: center;
  padding: 40px;
  color: #666;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background: #f8f9fa;
  border-top: 1px solid #dee2e6;
}

.page-info {
  color: #666;
  font-size: 14px;
}

@media (max-width: 768px) {
  .table-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }

  .table-info {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }

  .search-input {
    width: 200px;
  }

  .action-buttons {
    flex-direction: column;
  }
}
</style>
