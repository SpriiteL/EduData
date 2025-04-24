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