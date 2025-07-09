<template>
  <div>
    <div class="header-gradient"></div> <!-- Rectangle dégradé bleu en haut -->
    <div class="inventory-container">
      <h1 class="page-title">Gestion de l'inventaire</h1>
      <div class="explication">
      Cette page vous permet de gérer l'inventaire des actifs de l'établissement. Vous pouvez importer des données depuis un fichier CSV, exporter les données actuelles, et supprimer des entrées.
      Pour importer un fichier CSV, assurez-vous qu'il respecte le format requis. Le fichier doit contenir les colonnes suivantes : Type Actif, Fournisseur, Date d'arrivée, Numéro de Série, Numéro de Facture, N° Facture Interne, Prix Neuf, Lot, Quantité, Salle.
      Après l'importation, les données seront ajoutées à l'inventaire et vous pourrez les visualiser dans le tableau ci-dessous. Vous pouvez également trier les données par colonne en cliquant sur les en-têtes.
      Pour exporter les données actuelles, cliquez sur le bouton "Exporter". Un fichier CSV sera téléchargé contenant toutes les entrées de l'inventaire.
      Pour supprimer une entrée, cliquez sur l'icône de la corbeille à côté de l'entrée correspondante. Une confirmation sera demandée avant la suppression.
      Utilisez les boutons de pagination pour naviguer entre les pages si l'inventaire contient de nombreuses entrées.
      Si vous rencontrez des problèmes ou avez des questions, n'hésitez pas à contacter l'administrateur du système.
      Merci de votre attention et bonne gestion de l'inventaire !
    </div>
      <div class="header-section">
        <h1 class="page-title">Inventaire</h1>
        
        <div class="import-export-section">
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
              class="btn btn-success"
            >
              <i class="fas fa-file-import"></i>
              {{ isLoading ? 'Import en cours...' : 'Importer' }}
            </button>
            <button 
              @click="exportCsv"
              :disabled="isLoading"
              class="btn btn-secondary"
            >
              <i class="fas fa-file-export"></i>
              Exporter
            </button>
          </div>
        </div>

        <div v-if="message" :class="['alert', messageType === 'success' ? 'alert-success' : 'alert-error']">
          {{ message }}
        </div>
      </div>

      <div class="table-section">
        <div class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th @click="sortBy('activeType')" class="sortable">
                  Type Actif <i :class="getSortIcon('activeType')"></i>
                </th>
                <th @click="sortBy('provider')" class="sortable">
                  Fournisseur <i :class="getSortIcon('provider')"></i>
                </th>
                <th @click="sortBy('dateEntry')" class="sortable">
                  Date d'arrivée <i :class="getSortIcon('dateEntry')"></i>
                </th>
                <th>Numéro de Série</th>
                <th>Numéro de Facture</th>
                <th>N° Facture Interne</th>
                <th @click="sortBy('price')" class="sortable">
                  Prix Neuf <i :class="getSortIcon('price')"></i>
                </th>
                <th>Lot</th>
                <th>Quantité</th>
                <th>Salle</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="11" class="loading-row">
                  <i class="fas fa-spinner fa-spin"></i> Chargement…
                </td>
              </tr>
              <tr v-else-if="inventories.length === 0">
                <td colspan="11" class="no-data">Aucune donnée disponible</td>
              </tr>
              <tr v-else v-for="inv in paginatedData" :key="inv.id">
                <td>{{ inv.activeType }}</td>
                <td>{{ inv.provider }}</td>
                <td>{{ formatDate(inv.dateEntry) }}</td>
                <td>{{ inv.numSerie }}</td>
                <td>{{ inv.numInvoice }}</td>
                <td>{{ inv.numInvoiceIntern }}</td>
                <td>{{ formatPrice(inv.price) }}</td>
                <td>{{ inv.numProductSerie }}</td>
                <td>{{ inv.totalProductLot }}</td>
                <td>{{ inv.nameRoom }}</td>
                <td>
                  <button @click="deleteInventory(inv.id)" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="inventories.length > itemsPerPage" class="pagination">
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
        <button @click="fetchInventories" class="btn btn-primary">
          <i class="fas fa-sync-alt"></i> Rafraîchir
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'InventoryManager',
  data() {
    return {
      inventories: [],
      selectedFile: null,
      isLoading: false,
      message: '',
      messageType: 'success',
      sortField: 'dateEntry',
      sortDirection: 'desc',
      currentPage: 1,
      itemsPerPage: 10,
    };
  },
  computed: {
    sortedData() {
      const data = [...this.inventories];
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
      return Math.ceil(this.inventories.length / this.itemsPerPage);
    },
  },
  methods: {
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toISOString().split('T')[0];
    },
    formatPrice(p) {
      return p != null ? `${p.toFixed(2)} €` : '';
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
    handleFileSelect(e) {
      this.selectedFile = e.target.files[0];
    },
    async importCsv() {
      if (!this.selectedFile) return;
      this.isLoading = true;
      const form = new FormData();
      form.append('file', this.selectedFile);
      try {
        const { data } = await axios.post('/inventory/import', form, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        this.message = data.success ? `Importé ${data.count} items` : data.error;
        this.messageType = data.success ? 'success' : 'error';
        this.selectedFile = null;
        this.$refs.fileInput.value = '';
        await this.fetchInventories();
      } catch (e) {
        this.message = 'Erreur lors de l’import';
        this.messageType = 'error';
      } finally {
        this.isLoading = false;
        setTimeout(() => this.message = '', 5000);
      }
    },
    async exportCsv() {
      this.isLoading = true;
      try {
        const resp = await axios.post('/inventory/export', {}, { responseType: 'blob' });
        const url = URL.createObjectURL(new Blob([resp.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'inventaire.csv');
        document.body.appendChild(link);
        link.click();
        link.remove();
      } catch {
        this.message = 'Erreur lors de l’export';
        this.messageType = 'error';
        setTimeout(() => this.message = '', 5000);
      } finally {
        this.isLoading = false;
      }
    },
    async fetchInventories() {
      this.isLoading = true;
      try {
        const { data } = await axios.get('/inventory/api/inventories');
        this.inventories = data;
      } catch {
        this.message = 'Erreur de chargement';
        this.messageType = 'error';
        setTimeout(() => this.message = '', 5000);
      } finally {
        this.isLoading = false;
      }
    },
    async deleteInventory(id) {
      if (!confirm('Voulez-vous supprimer cet élément ?')) return;
      this.isLoading = true;
      try {
        await axios.delete(`/inventory/api/inventories/${id}`);
        this.inventories = this.inventories.filter(inv => inv.id !== id);
      } catch {
        this.message = 'Erreur lors de la suppression';
        this.messageType = 'error';
        setTimeout(() => this.message = '', 5000);
      } finally {
        this.isLoading = false;
      }
    },
  },
  mounted() {
    this.fetchInventories();
  }
};
</script>

<style scoped>
.header-gradient {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 500px; /* Encore plus large */
  background: linear-gradient(90deg,#00aaff, #003cff);
  z-index: 0; /* derrière tout */
}
.inventory-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding-top: 320px; /* espace pour le grand gradient */
  position: relative;
  z-index: 100; /* au-dessus du gradient */
  margin-top: -250px;
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
.import-export-section {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 15px;
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  position: relative;
  z-index: 100;
}
.file-upload-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
}
.file-input {
  display: none;
}
.file-label {
  padding: 8px 16px;
  background: #1b60eb; /* couleur verte */
  /* #28a745; couleur verte*/
  color: white;
  border-radius: 5px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
}
.file-label:hover {
  background: #1749ab;
  /* #218838; couleur verte */
}
.file-name {
  font-weight: 600;
  color: #555;
}
.action-buttons .btn {
  min-width: 120px;
  margin-right: 10px;
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
}
.alert-error {
  background: #f8d7da;
  color: #721c24;
}
.table-section {
  background: white;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 0 10px rgba(0,0,0,0.05);
  position: relative;
  z-index: 100; /* au-dessus du dégradé */
}
.data-table {
  width: 100%;
  border-collapse: collapse;
}
.data-table th, .data-table td {
  padding: 10px;
  border-bottom: 1px solid #e2e2e2;
  text-align: left;
}
.data-table th.sortable {
  cursor: pointer;
}
.data-table th.sortable i {
  margin-left: 8px;
  color: #888;
}
.loading-row, .no-data {
  text-align: center;
  color: #666;
  font-style: italic;
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
.btn-primary {
  background: #007bff;
  color: white;
}
.btn-success {
  background: #1b60eb;
  color: white;
}
.btn-secondary {
  background: #6c757d;
  color: white;
}
.btn-danger {
  background: #dc3545;
  color: white;
}
.btn-sm {
  padding: 4px 8px;
}

:global(body) {
  margin: 0;
  background-color: #eeeeee; /* ou une autre couleur cohérente */
  font-family: 'Segoe UI', sans-serif;
}

.explication {
  margin-bottom: 20px;
  font-size: 0.70rem;
  line-height: 1.5;
  background-color: white;
  border-radius: 8px;
  padding: 4px 8px;
}
</style>
