<template>
    <div class="login-container">
        <div class="login-card">
            <a href="/" class="logo">
                <img src="/asset/edu3.png" width="180" alt="Logo">
            </a>
            <form @submit.prevent="submitForm" class="login-form">
                <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
                <div v-if="user" class="mb-3">
                    Vous êtes connecté en tant que {{ user }}, <a href="/logout">Déconnexion</a>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" v-model="username" id="username" class="form-control" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Mot de Passe</label>
                    <input type="password" v-model="password" id="password" class="form-control" required>
                </div>
                <button class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2" type="submit">Se Connecter</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        return {
            username: "",
            password: "",
            errorMessage: "",
            user: null
        };
    },
    methods: {
        async submitForm() {
            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ username: this.username, password: this.password })
                });
                const data = await response.json();
                if (response.ok) {
                    window.location.href = '/'; // Redirige après connexion réussie
                } else {
                    this.errorMessage = data.message || "Erreur de connexion";
                }
            } catch (error) {
                this.errorMessage = "Une erreur est survenue";
            }
        }
    }
};
</script>

<style>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #f4f7fc;
}
.login-card {
    background: white;
    width: 50vh;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}
.logo img {
    margin-bottom: 1rem;
}
</style>
