<template>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Statistiques d'imprimantes</h3>
      <div class="card-tools">
        <span class="badge badge-primary">Impressions</span>
      </div>
    </div>
    
    <div class="card-body">
      <div class="main-content">
        <div class="container">
          <div class="row">
            <div class="col-lg-12 d-flex align-items-stretch">
              <div class="card w-100">
                <div class="card-body p-4">
                  
                  <!-- Débogage -->
                  <div class="alert alert-info" v-if="debugInfo">
                    <strong>Info de débogage:</strong> {{ debugInfo }}
                  </div>
                  
                  <!-- Alerte de succès -->
                  <div class="alert alert-success" v-if="successMessage">
                    {{ successMessage }}
                  </div>
                  
                  <!-- Formulaire de recherche et d'import -->
                  <div class="d-flex align-items-center mb-4">
                    <form @submit.prevent="uploadCsv" enctype="multipart/form-data" ref="uploadForm" class="me-3">
                      <div class="d-flex align-items-center">
                        <div class="form-group">
                          <input type="file" name="csv" ref="fileInput" accept=".csv" class="form-control" style="max-width: 100%; width: 500px;" required>
                        </div>
                        <div class="form-group ms-3">
                          <button type="submit" class="btn btn-success" :disabled="isUploading">
                            <i class="fas fa-file-import"></i> {{ isUploading ? 'Importation en cours...' : 'Importer' }}
                          </button>
                        </div>
                      </div>
                    </form>

                    <a :href="'/printer-stats/export'" class="btn btn-export">
                      <i class="fas fa-file-export"></i> Exporter
                    </a>
                    
                    <button @click="showRawData" class="btn btn-info ms-3">
                      <i class="fas fa-eye"></i> Voir données brutes
                    </button>
                  </div>

                  <!-- Modal pour afficher les données brutes -->
                  <div class="modal fade" id="rawDataModal" tabindex="-1" role="dialog" aria-labelledby="rawDataModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="rawDataModalLabel">Données brutes</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <pre>{{ JSON.stringify(stats, null, 2) }}</pre>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Affichage simplifié des données (sans DataTables) -->
                  <div v-if="viewMode === 'simple'" class="mb-4">
                    <button @click="switchToAdvanced" class="btn btn-primary mb-3">Passer à la vue avancée</button>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Utilisateur</th>
                          <th>Total Couleur</th>
                          <th>Total Noir</th>
                          <th>Total Scan</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="stat in stats" :key="stat.id">
                          <td>{{ stat.username }}</td>
                          <td>{{ stat.totalCouleur }}</td>
                          <td>{{ stat.totalNoir }}</td>
                          <td>{{ stat.totalScan }}</td>
                          <td>
                            <form @submit.prevent="deleteStat(stat.id)">
                              <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                            </form>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  
                  <!-- Tableau de statistiques avec DataTables -->
                  <div v-if="viewMode === 'advanced'" class="divtable">
                    <button @click="switchToSimple" class="btn btn-primary mb-3">Passer à la vue simplifiée</button>
                    <div class="table-test">
                      <table id="printer-table" class="test">
                        <thead class="text-dark fs-4">
                          <tr class="colonne">
                            <th>Utilisateur</th>
                            <th>Job charge count FCL</th>
                            <th>Job charge count FCS</th>
                            <th>Total Impression Couleur</th>
                            <th>Job charge count MTL</th>
                            <th>Job charge count MTS</th>
                            <th>Job charge count MCL</th>
                            <th>Job charge count MCS</th>
                            <th>Total Copie Couleur</th>
                            <th>Job charge count MBL</th>
                            <th>Job charge count MBS</th>
                            <th>Total Copie Mono</th>
                            <th>Total Couleur</th>
                            <th>Total Noir</th>
                            <th>Scan A4</th>
                            <th>Scan A3</th>
                            <th>Total Scan</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="stat in stats" :key="stat.id">
                            <td>{{ stat.username }}</td>
                            <td>{{ stat.jobChargeCountFCL }}</td>
                            <td>{{ stat.jobChargeCountFCS }}</td>
                            <td>{{ stat.impressionTotalCouleur }}</td>
                            <td>{{ stat.jobChargeCountMTL }}</td>
                            <td>{{ stat.jobChargeCountMTS }}</td>
                            <td>{{ stat.impressionTotalMono }}</td>
                            <td>{{ stat.jobChargeCountMCL }}</td>
                            <td>{{ stat.jobChargeCountMCS }}</td>
                            <td>{{ stat.copieTotalCouleur }}</td>
                            <td>{{ stat.jobChargeCountMBL }}</td>
                            <td>{{ stat.jobChargeCountMBS }}</td>
                            <td>{{ stat.totalCouleur }}</td>
                            <td>{{ stat.totalNoir }}</td>
                            <td>{{ stat.scanA4 }}</td>
                            <td>{{ stat.scanA3 }}</td>
                            <td>{{ stat.totalScan }}</td>
                            <td>
                              <form @submit.prevent="deleteStat(stat.id)">
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                              </form>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="card-footer">
      <button @click="fetchStats" class="btn btn-primary me-2">Recharger</button>
      <button @click="clearDebug" class="btn btn-outline-secondary">Effacer débogage</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import $ from 'jquery';
import 'datatables.net-dt';

export default {
  props: ['initialData'],
  data() {
    return {
      stats: [],
      dataTable: null,
      isUploading: false,
      successMessage: '',
      debugInfo: '',
      viewMode: 'simple' // 'simple' ou 'advanced'
    };
  },
  methods: {
    // Méthodes de débogage
    logDebug(message) {
      console.log(message);
      this.debugInfo = new Date().toLocaleTimeString() + ': ' + message;
    },
    clearDebug() {
      this.debugInfo = '';
    },
    showRawData() {
      // Afficher la modal avec les données brutes
      $('#rawDataModal').modal('show');
    },
    switchToAdvanced() {
      this.viewMode = 'advanced';
      this.$nextTick(() => {
        this.initDataTable();
      });
    },
    switchToSimple() {
      this.viewMode = 'simple';
    },

    // Méthodes principales
    initDataTable() {
      this.logDebug(`Initialisation de DataTable avec ${this.stats.length} entrées`);
      
      // Détruire l'instance DataTable existante si elle existe
      if (this.dataTable) {
        this.dataTable.destroy();
      }
      
      // Initialiser une nouvelle instance DataTable
      this.$nextTick(() => {
        setTimeout(() => {
          try {
            this.dataTable = $('#printer-table').DataTable({
              responsive: true,
              language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
              }
            });
            this.logDebug('DataTable initialisé avec succès');
          } catch (error) {
            this.logDebug(`Erreur DataTable: ${error.message}`);
          }
        }, 200);
      });
    },
    
    async uploadCsv() {
      this.isUploading = true;
      this.successMessage = '';
      this.logDebug('Début de l\'upload CSV');
      
      try {
        const formData = new FormData();
        const file = this.$refs.fileInput.files[0];
        formData.append('csv', file);
        this.logDebug(`Fichier sélectionné: ${file.name}`);
        
        // Envoyer le fichier au serveur
        const response = await axios.post('/printer-stats/import', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
          }
        });
        
        this.logDebug(`Réponse reçue: ${response.status}`);
        
        // Afficher le message de succès
        this.successMessage = response.data.message || 'Fichier importé avec succès';
        
        // Vérifier le contenu de la réponse
        if (response.data.stats && Array.isArray(response.data.stats)) {
          this.logDebug(`Données reçues: ${response.data.stats.length} entrées`);
          
          // Mettre à jour les stats
          this.stats = [];
          this.$nextTick(() => {
            this.stats = response.data.stats;
            this.logDebug(`Stats mises à jour: ${this.stats.length} entrées`);
          });
        } else {
          this.logDebug('Pas de données dans la réponse ou format invalide');
          // Recharger les données depuis l'API
          this.fetchStats();
        }
        
        // Reset form
        this.$refs.uploadForm.reset();
      } catch (error) {
        console.error('Erreur:', error);
        this.logDebug(`Erreur d'upload: ${error.message}`);
        alert('Une erreur est survenue lors de l\'importation du fichier');
      } finally {
        this.isUploading = false;
      }
    },
    
    async fetchStats() {
      this.logDebug('Récupération des statistiques...');
      
      try {
        const response = await axios.get('/printer-stats/api/stats');
        this.logDebug(`API stats: ${response.status}, ${response.data.length} entrées`);
        
        // Mettre à jour les stats
        this.stats = [];
        this.$nextTick(() => {
          this.stats = response.data;
          this.logDebug(`Stats mises à jour: ${this.stats.length} entrées`);
          
          if (this.viewMode === 'advanced') {
            this.initDataTable();
          }
        });
      } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        this.logDebug(`Erreur fetchStats: ${error.message}`);
      }
    },
    
    async deleteStat(id) {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette statistique ?')) {
        this.logDebug(`Suppression de la statistique ${id}`);
        
        try {
          const response = await axios.delete(`/printer-stats/api/stats/${id}`);
          this.logDebug(`Suppression: ${response.status}`);
          
          // Filtrer le tableau pour supprimer l'entrée
          this.stats = this.stats.filter(stat => stat.id !== id);
          
          if (this.viewMode === 'advanced') {
            this.initDataTable();
          }
        } catch (error) {
          console.error('Erreur lors de la suppression:', error);
          this.logDebug(`Erreur deleteStat: ${error.message}`);
        }
      }
    }
  },
  created() {
    // Initialiser les données
    if (this.initialData && Array.isArray(this.initialData) && this.initialData.length > 0) {
      this.stats = JSON.parse(JSON.stringify(this.initialData));
      this.logDebug(`Données initiales chargées: ${this.stats.length} entrées`);
    } else {
      this.logDebug('Pas de données initiales, chargement depuis l\'API');
      this.fetchStats();
    }
  },
  mounted() {
    if (this.viewMode === 'advanced') {
      this.initDataTable();
    }
  }
};
</script>

<style scoped>
/* Style spécifique au composant */
.alert {
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 4px;
}
.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.alert-info {
  background-color: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

.btn-export {
  background-color: #17a2b8;
  color: white;
}
.btn-export:hover {
  background-color: #138496;
  color: white;
}

/* Espacement entre les boutons */
.me-2 {
  margin-right: 0.5rem;
}
.me-3 {
  margin-right: 1rem;
}
.ms-3 {
  margin-left: 1rem;
}
.mb-3 {
  margin-bottom: 1rem;
}
.mb-4 {
  margin-bottom: 1.5rem;
}
</style>