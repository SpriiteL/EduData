<template>
    <div class="card mt-4 mx-4 p-4">
        <div class="card-header">
            <h3 class="card-title">Badge</h3>
            <div class="card-tools">
                <span class="badge badge-primary">Label</span>
            </div>
        </div>

        <form id="badge-form" class="mb-4">
            <div class="row" style="margin: 0;">
                <div class="col-md-2">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom">
                </div>
                <div class="col-md-2">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom">
                </div>
                <div class="col-md-2">
                    <label for="dateTraitement" class="form-label">Date Traitement</label>
                    <input type="date" class="form-control" id="dateTraitement" name="dateTraitement">
                </div>
                <div class="col-md-2">
                    <label for="classe" class="form-label">Classe</label>
                    <select class="form-select" id="classe" name="classe">
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
                <div class="col-md-2">
                    <label for="etatTraitement" class="form-label">État du Traitement</label>
                    <select class="form-select" id="etatTraitement" name="etatTraitement">
                        <option value="En attente">En attente</option>
                        <option value="Traitée">Traitée</option>
                        <option value="Refusée">Refusée</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="lieu" class="form-label">Lieu</label>
                    <input type="text" class="form-control" id="lieu" name="lieu" placeholder="Lieu">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="add-badge" class="btn btn-primary w-100">Ajouter</button>
                </div>
            </div>
        </form>

        <div class="divtable">
            <div class="table-test">
                <table id="badge-table" class="test">
                    <thead class="text-dark fs-4">
                        <tr class="colonne">
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Date Traitement</th>
                            <th>Classe</th>
                            <th>État du Traitement</th>
                            <th>Lieu</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Les lignes seront générées dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            The footer of the card
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            badges: []
        };
    },
    mounted() {
        this.loadBadges();
        this.setupEventListeners();
        // Si vous utilisez jQuery et DataTables, décommentez ces lignes
        // $(document).ready(() => {
        //     $('#badge-table').DataTable();
        // });
    },
    methods: {
        setupEventListeners() {
            document.getElementById('add-badge').addEventListener('click', this.addBadge);
        },
        loadBadges() {
            fetch('/badge/list')
                .then(response => response.json())
                .then(data => {
                    this.badges = data;
                    this.renderBadgeTable();
                })
                .catch(error => console.error('Erreur lors du chargement des badges:', error));
        },
        addBadge() {
            const form = document.getElementById('badge-form');
            const formData = new FormData(form);

            fetch('/badge/add', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                if (!response.ok) {
                    const errorData = await response.json();
                    alert('Erreur : ' + (errorData.error || 'Erreur serveur'));
                    throw new Error('Erreur HTTP');
                }
                return response.json();
            })
            .then(data => {
                this.badges.push(data);
                this.renderBadgeTable();
                form.reset();
            })
            .catch(error => {
                console.error('Erreur lors de l\'ajout du badge:', error);
            });
        },
        renderBadgeTable() {
            // Si vous utilisez DataTables, décommentez ces lignes
            // if ($.fn.DataTable.isDataTable('#badge-table')) {
            //     $('#badge-table').DataTable().destroy();
            // }
            
            const tbody = document.querySelector('#badge-table tbody');
            tbody.innerHTML = '';
            
            this.badges.forEach(badge => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${badge.nom}</td>
                    <td>${badge.prenom}</td>
                    <td>${badge.dateTraitement}</td>
                    <td>${badge.classe}</td>
                    <td>${badge.etatTraitement}</td>
                    <td>${badge.lieu}</td>
                    <td>
                        <button class="btn btn-sm btn-danger delete-badge" data-id="${badge.id}">Supprimer</button>
                    </td>
                `;
                
                tbody.appendChild(tr);
                
                // Ajouter l'écouteur d'événements pour le bouton de suppression
                const deleteButton = tr.querySelector('.delete-badge');
                deleteButton.addEventListener('click', () => this.deleteBadge(badge.id));
            });
            
            // Si vous utilisez DataTables, décommentez ces lignes
            // $('#badge-table').DataTable({
            //     // Vos options DataTables ici
            // });
        },
        deleteBadge(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce badge?')) {
                fetch(`/badge/delete/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.badges = this.badges.filter(badge => badge.id !== id);
                        this.renderBadgeTable();
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la suppression du badge:', error);
                });
            }
        }
    }
};
</script>