<template>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Inventaire</h3>
        <div class="card-tools">
          <span class="badge badge-primary">Label</span>
        </div>
      </div>
      <!-- /.card-header -->
      
      <div class="card-body">
        <!-- Tableau d'inventaire -->
        <div class="main-content">
          <div class="container">
            <div class="row">
              <!-- Inventory table section -->
              <div class="col-lg-12 d-flex align-items-stretch">
                <div class="card w-100">
                  <div class="card-body p-4">
                    
                    <!-- Formulaire de recherche et d'import -->
                    <div class="d-flex align-items-center mb-4">
                      <form method="post" action="/inventory/import" enctype="multipart/form-data" class="me-3">
                        <div class="d-flex align-items-center">
                          <div class="form-group">
                            <input type="file" name="file" accept=".csv" class="form-control" style="max-width: 100%; width: 500px;" required>
                          </div>
                          <div class="form-group ms-3">
                            <button type="submit" class="btn btn-success"><i class="fas fa-file-import"></i> Importer</button>
                          </div>
                        </div>
                      </form>
  
                      <form method="post" action="/inventory/export">
                        <div class="form-group">
                          <button type="submit" class="btn btn-export"><i class="fas fa-file-export"></i> Exporter</button>
                        </div>
                      </form>
                    </div>
  
                    <!-- Tableau d'inventaire -->
                    <div class="divtable">
                      <div class="table-test">
                        <table id="inventory-table" class="test">
                          <thead class="text-dark fs-4">
                            <tr class="colonne">
                              <th>Type Actif</th>
                              <th>Fournisseur</th>
                              <th>Date d'arrivée</th>
                              <th>Numéro de Série</th>
                              <th>Numéro de Facture</th>
                              <th>Numéro Facture Interne</th>
                              <th>Prix Neuf</th>
                              <th>Numéro de produit de la série</th>
                              <th>Nombre total de produits dans le lot</th>
                              <th>Nom de la Salle</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="inventory in inventories" :key="inventory.id">
                              <td>{{ inventory.activeType }}</td>
                              <td>{{ inventory.provider }}</td>
                              <td>{{ formatDate(inventory.dateEntry) }}</td>
                              <td>{{ inventory.numSerie }}</td>
                              <td>{{ inventory.numInvoice }}</td>
                              <td>{{ inventory.numInvoiceIntern }}</td>
                              <td>{{ inventory.price }}</td>
                              <td>{{ inventory.numProductSerie }}</td>
                              <td>{{ inventory.totalProductLot }}</td>
                              <td>{{ inventory.nameRoom }}</td>
                              <td>
                                <form @submit.prevent="deleteInventory(inventory.id)">
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
      <!-- /.card-body -->
      
      <div class="card-footer">
        <button @click="fetchInventories" class="btn btn-primary">Recharger</button>
      </div>
      <!-- /.card-footer -->
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        inventories: [] // Stocke les données d'inventaire
      };
    },
    methods: {
      formatDate(dateString) {
        if (!dateString) return ''; // Vérifie si la date existe
        const date = new Date(dateString);
        return date.toISOString().split('T')[0]; // Format YYYY-MM-DD
      },
      async fetchInventories() {
        try {
          const response = await axios.get('/inventory/api/inventories'); // Change l'URL selon ton backend
          this.inventories = response.data;
        } catch (error) {
          console.error('Erreur lors du chargement des inventaires:', error);
        }
      },
      async deleteInventory(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
          try {
            await axios.delete(`/inventory/api/inventories/${id}`);
            this.inventories = this.inventories.filter(inv => inv.id !== id);
          } catch (error) {
            console.error('Erreur lors de la suppression:', error);
          }
        }
      }
    },
    created() {
      this.fetchInventories();
    }
  };
  </script>
  
  <style scoped>
  /* Style spécifique au composant */
  </style>
  