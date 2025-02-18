<template>
    <div class="container mt-4">
      <ul class="nav nav-tabs">
        <li v-for="(tab, index) in locations" :key="index" class="nav-item">
          <a
            class="nav-link"
            :class="{ active: activeTab === tab }"
            @click="activeTab = tab"
          >
            {{ tab }}
          </a>
        </li>
      </ul>
  
      <div class="tab-content mt-3">
        <div v-for="(tab, index) in locations" :key="index" class="tab-pane fade" :class="{ 'show active': activeTab === tab }">
          <div class="card mt-4 mx-4 p-4">
            <div class="card-header">
              <h3 class="card-title">Imprimantes - {{ tab }}</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered" ref="datatable">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Imprimante</th>
                    <th>Copies N/B</th>
                    <th>Copies Couleur</th>
                    <th>ID Imprimante</th>
                    <th>Nom Imprimante</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="imprimante in filteredImprimantes(tab)" :key="imprimante.id">
                    <td>{{ imprimante.id }}</td>
                    <td>{{ imprimante.username }}</td>
                    <td>{{ imprimante.printer }}</td>
                    <td>{{ imprimante.nbCopyBW }}</td>
                    <td>{{ imprimante.nbCopyColor }}</td>
                    <td>{{ imprimante.idPrinter }}</td>
                    <td>{{ imprimante.namePrinter }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        locations: [
          "Saint Martial",
          "Saint Laurent",
          "Saint Aurelien",
          "Secretariat",
          "Vie Scolaire",
          "Salle des Profs 1",
          "Salle des Profs 2",
        ],
        activeTab: "Saint Martial",
        imprimantes: [],
      };
    },
    methods: {
      fetchImprimantes() {
        axios.get("/api/imprimantes").then((response) => {
          this.imprimantes = response.data;
        });
      },
      filteredImprimantes(location) {
        return this.imprimantes.filter((item) => item.printer === location);
      },
    },
    mounted() {
      this.fetchImprimantes();
    },
  };
  </script>
  
  <style scoped>
  .nav-tabs .nav-link {
    cursor: pointer;
  }
  </style>
  