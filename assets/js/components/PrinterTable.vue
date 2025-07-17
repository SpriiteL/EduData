<template>
  <div class="header-gradient"></div>
    <div class="printer-usage-container">
      <div class="header-section">
        <h1 class="page-title">Statistiques d'utilisation des imprimantes</h1>
        
        <!-- BOUTON AFFICHER / MASQUER -->
        <button @click="showExplanation = !showExplanation" class="btn btn-secondary" style="margin-bottom: 10px;">
          {{ showExplanation ? 'Masquer les explications' : 'Afficher les explications' }}
        </button>

        <!-- BLOC D'EXPLICATION -->
        <div v-show="showExplanation" class="explication">
          <p>
            Cette page affiche les statistiques d'utilisation des imprimantes par utilisateur. 
            Vous pouvez importer des fichiers CSV contenant les données d'impression en sélectionnant le mois correspondant, 
            filtrer les données par mois, exporter les résultats au format CSV, et effacer les données existantes.
            Pour exporter des données, sélectionnez le mois souhaité et cliquez sur Exporter en CSV, vous obtiendrez un fichier contenant les colonnes suivantes : 
            <strong>username</strong>, <strong>Total Noir</strong>, <strong>Total Couleur</strong>, <strong>Total Scans</strong>, <strong>Mois</strong>.
          </p>
        </div>

        <!-- SECTION FILTRAGE -->
        <div class="filter-section">
          <div class="filter-wrapper">
            <label for="filterMonth">Filtrer par mois :</label>
            <select v-model="selectedFilterMonth" @change="loadData" id="filterMonth" class="month-select">
              <option value="all">Tous les mois</option>
              <option v-for="month in availableMonths" :key="month" :value="month">
                {{ getMonthLabel(month) }}
              </option>
            </select>
          </div>
        </div>

        <div class="import-section">
          <div class="upload-actions-wrapper">
            <!-- Upload file section -->
            <div class="file-upload-wrapper">
              <!-- Sélection du mois pour l'import -->
              <div class="month-selector">
                <label for="importMonth">Mois d'import :</label>
                <select v-model="selectedImportMonth" id="importMonth" class="month-select">
                  <option value="">Sélectionner un mois</option>
                  <option v-for="month in months" :key="month.value" :value="month.value">
                    {{ month.label }}
                  </option>
                </select>
              </div>

              <input 
                ref="fileInput"
                type="file" 
                accept=".csv"
                multiple
                @change="handleFileSelect"
                class="file-input"
                id="csvFile"
              />
              <label for="csvFile" class="file-label">
                <i class="fas fa-upload"></i>
                Choisir un ou plusieurs fichiers CSV
              </label>
              <span v-if="selectedFiles.length > 0" class="file-name">
                {{ selectedFiles.map(file => file.name).join(', ') }}
              </span>
            </div>

            <!-- Buttons aligned to the right -->
            <div class="action-buttons">
              <button 
                @click="importCsv" 
                :disabled="selectedFiles.length === 0 || isLoading || !selectedImportMonth"
                class="btn btn-primary"
              >
                <i class="fas fa-file-import"></i>
                {{ isLoading ? 'Import en cours...' : 'Importer' }}
              </button>

              <button 
                @click="exportCsv" 
                :disabled="isLoading || printerData.length === 0"
                class="btn btn-secondary"
              >
                <i class="fas fa-file-export"></i>
                {{ selectedFilterMonth === 'all' ? 'Exporter tous les mois' : `Exporter ${getMonthLabel(selectedFilterMonth)}` }}
              </button>

              <button 
                @click="clearData" 
                :disabled="isLoading"
                class="btn btn-danger"
              >
                <i class="fas fa-trash"></i>
                {{ selectedFilterMonth === 'all' ? 'Effacer toutes les données' : `Effacer ${getMonthLabel(selectedFilterMonth)}` }}
              </button>
            </div>
          </div>
        </div>

        <div v-if="message" :class="['alert', messageType === 'success' ? 'alert-success' : 'alert-error']">
          {{ message }}
        </div>
      </div>

      <div class="table-section">
        <div class="table-header">
          <h2>
            Résultats par utilisateur
            <span v-if="selectedFilterMonth !== 'all'" class="month-badge">{{ getMonthLabel(selectedFilterMonth) }}</span>
          </h2>
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
                <th @click="sortBy('month')" class="sortable">
                  Mois
                  <i :class="getSortIcon('month')"></i>
                </th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="6" class="loading-row">
                  <i class="fas fa-spinner fa-spin"></i>
                  Chargement des données...
                </td>
              </tr>
              <tr v-else-if="filteredData.length === 0">
                <td colspan="6" class="no-data">
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
                <td class="month-cell">
                  <span class="badge badge-month">{{ getMonthLabel(user.month) }}</span>
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
                <td class="month-cell">
                  <span class="badge badge-total-general">{{ selectedFilterMonth === 'all' ? 'TOUS' : getMonthLabel(selectedFilterMonth) }}</span>
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
      selectedFiles: [],
      isLoading: false,
      message: '',
      messageType: 'success',
      searchQuery: '',
      sortField: 'username',
      sortDirection: 'asc',
      currentPage: 1,
      itemsPerPage: 10,
      showExplanation: true,
      selectedImportMonth: '',
      selectedFilterMonth: 'all',
      availableMonths: [],
      months: [
        { value: '01', label: 'Janvier' },
        { value: '02', label: 'Février' },
        { value: '03', label: 'Mars' },
        { value: '04', label: 'Avril' },
        { value: '05', label: 'Mai' },
        { value: '06', label: 'Juin' },
        { value: '07', label: 'Juillet' },
        { value: '08', label: 'Août' },
        { value: '09', label: 'Septembre' },
        { value: '10', label: 'Octobre' },
        { value: '11', label: 'Novembre' },
        { value: '12', label: 'Décembre' }
      ]
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
        const params = {};
        if (this.selectedFilterMonth !== 'all') {
          params.month = this.selectedFilterMonth;
        }
        
        const response = await axios.get('/printer-stats/data', { params });
        this.printerData = response.data;
      } catch (error) {
        this.showMessage('Erreur lors du chargement des données', 'error');
      } finally {
        this.isLoading = false;
      }
    },
    async loadAvailableMonths() {
      try {
        const response = await axios.get('/printer-stats/months');
        this.availableMonths = response.data;
      } catch (error) {
        console.error('Erreur lors du chargement des mois disponibles', error);
      }
    },
    handleFileSelect(event) {
      this.selectedFiles = Array.from(event.target.files);
    },
    async importCsv() {
      if (this.selectedFiles.length === 0) {
        this.showMessage('Veuillez sélectionner un ou plusieurs fichiers CSV', 'error');
        return;
      }

      if (!this.selectedImportMonth) {
        this.showMessage('Veuillez sélectionner un mois pour l\'import', 'error');
        return;
      }

      // Validation des fichiers côté client
      for (let file of this.selectedFiles) {
        if (!file.name.toLowerCase().endsWith('.csv')) {
          this.showMessage(`Le fichier "${file.name}" n'est pas un fichier CSV`, 'error');
          return;
        }
        
        if (file.size === 0) {
          this.showMessage(`Le fichier "${file.name}" est vide`, 'error');
          return;
        }
        
        if (file.size > 10 * 1024 * 1024) { // 10MB max
          this.showMessage(`Le fichier "${file.name}" est trop volumineux (max 10MB)`, 'error');
          return;
        }
      }

      this.isLoading = true;
      this.message = ''; // Effacer les messages précédents

      try {
        const formData = new FormData();
        
        // Ajouter les fichiers avec le bon nom de champ
        this.selectedFiles.forEach((file, index) => {
          formData.append('csvFiles[]', file);
        });
        
        formData.append('selectedMonth', this.selectedImportMonth);

        const response = await axios.post('/printer-stats/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          timeout: 60000 // 60 secondes timeout
        });

        if (response.data.success) {
          this.showMessage(response.data.message, 'success');
          
          // Afficher des détails supplémentaires si disponibles
          if (response.data.usersProcessed) {
            console.log(`${response.data.usersProcessed} utilisateurs traités`);
          }
          if (response.data.filesProcessed) {
            console.log('Fichiers traités:', response.data.filesProcessed);
          }
          
          // Recharger les données
          await this.loadData();
          await this.loadAvailableMonths();
          
          // Réinitialiser le formulaire
          this.selectedFiles = [];
          this.selectedImportMonth = '';
          this.$refs.fileInput.value = '';
        } else {
          this.showMessage(response.data.error || 'Erreur lors de l\'import', 'error');
        }
      } catch (error) {
        console.error('Erreur import:', error);
        
        let errorMessage = 'Erreur lors de l\'import des fichiers';
        
        if (error.response) {
          // Erreur HTTP avec réponse du serveur
          if (error.response.data && error.response.data.error) {
            errorMessage = error.response.data.error;
          } else if (error.response.status === 413) {
            errorMessage = 'Fichier trop volumineux';
          } else if (error.response.status === 500) {
            errorMessage = 'Erreur interne du serveur';
          } else {
            errorMessage = `Erreur HTTP ${error.response.status}`;
          }
        } else if (error.request) {
          // Erreur réseau
          errorMessage = 'Erreur de connexion au serveur';
        } else if (error.code === 'ECONNABORTED') {
          // Timeout
          errorMessage = 'Timeout - Le traitement a pris trop de temps';
        }
        
        this.showMessage(errorMessage, 'error');
      } finally {
        this.isLoading = false;
      }
    },
    async exportCsv() {
      try {
        this.isLoading = true;
        const params = {};
        if (this.selectedFilterMonth !== 'all') {
          params.month = this.selectedFilterMonth;
        }
        
        const response = await axios.get('/printer-stats/export', {
          params,
          responseType: 'blob'
        });

        const blob = new Blob([response.data], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        
        const filename = this.selectedFilterMonth === 'all' 
          ? 'printer_stats_export_all.csv'
          : `printer_stats_export_${this.selectedFilterMonth}.csv`;
        
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      } catch (error) {
        this.showMessage('Erreur lors de l\'export', 'error');
      } finally {
        this.isLoading = false;
      }
    },
    async clearData() {
      const confirmMessage = this.selectedFilterMonth === 'all'
        ? 'Êtes-vous sûr de vouloir supprimer toutes les données ?'
        : `Êtes-vous sûr de vouloir supprimer les données du mois ${this.getMonthLabel(this.selectedFilterMonth)} ?`;
      
      if (!confirm(confirmMessage)) {
        return;
      }
      
      try {
        this.isLoading = true;
        const params = {};
        if (this.selectedFilterMonth !== 'all') {
          params.month = this.selectedFilterMonth;
        }
        
        const response = await axios.delete('/printer-stats/clear', { params });
        if (response.data.success) {
          this.showMessage(response.data.message, 'success');
          await this.loadData();
          await this.loadAvailableMonths();
        } else {
          this.showMessage(response.data.error || 'Erreur lors de la suppression', 'error');
        }
      } catch (error) {
        this.showMessage('Erreur lors de la suppression', 'error');
      } finally {
        this.isLoading = false;
      }
    },
    showMessage(message, type) {
      this.message = message;
      this.messageType = type;
      setTimeout(() => this.message = '', 5000);
    },
    sortBy(field) {
      if (this.sortField === field) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortField = field;
        this.sortDirection = 'asc';
      }
    },
    getSortIcon(field) {
      if (this.sortField !== field) return 'fas fa-sort';
      return this.sortDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
    },
    getMonthLabel(monthValue) {
      const month = this.months.find(m => m.value === monthValue);
      return month ? month.label : monthValue;
    }
  },
  mounted() {
    this.loadData();
    this.loadAvailableMonths();
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

.printer-usage-container {
  padding: 20px;
  max-width: 1500px;
  margin: 0 auto;
  margin-left: 300px;
  position: relative;
  z-index: 1;
}

.header-section {
  margin-bottom: 30px;
}

.page-title {
  color: #ffffff;
  margin-bottom: 20px;
  font-size: 2rem;
}

.filter-section {
  background: #e8f4f8;
  padding: 15px 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  border-left: 4px solid #007bff;
}

.filter-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-wrapper label {
  font-weight: 600;
  color: #333;
  white-space: nowrap;
}

.month-select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  min-width: 150px;
}

.import-section {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.upload-actions-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.file-upload-wrapper {
  display: flex;
  align-items: center;
  gap: 15px;
  flex-grow: 1;
  flex-wrap: wrap;
}

.month-selector {
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.month-selector label {
  font-weight: 600;
  color: #333;
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

.action-buttons {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.action-buttons button {
  min-width: 140px;
}

.btn {
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #0056b3;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #545b62;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: #c82333;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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
  display: flex;
  align-items: center;
  gap: 10px;
}

.month-badge {
  background: #007bff;
  color: white;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
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

.data-table th.sortable:hover {
  background-color: #f8f9fa;
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

.month-cell {
  text-align: center;
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
  background-color: #d63535;
}

.badge-color {
  background-color: #28a745;
}

.badge-info {
  background-color: #17a2b8;
}

.badge-month {
  background-color: #6f42c1;
}

.badge-total {
  background-color: #ffc107;
}

.badge-total-general {
  background-color: #343a40;
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