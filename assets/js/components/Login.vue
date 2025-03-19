<template>
    <div class="login-container">
        <div class="login-card">
            <h3>Se Connecter</h3>

            <form @submit.prevent="submitForm">
                <!-- Affichage de l'erreur si elle existe -->
                <div v-if="errorMessage" class="alert alert-danger">
                    {{ errorMessage }}
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input
                        type="text"
                        id="username"
                        v-model="username"
                        class="form-control"
                        autocomplete="username"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Mot de Passe</label>
                    <input
                        type="password"
                        id="password"
                        v-model="password"
                        class="form-control"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    Se Connecter
                </button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            username: "",
            password: "",
            errorMessage: "",
        };
    },
    methods: {
        async submitForm() {
            try {
                // Récupérer le token CSRF depuis le meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Envoyer la requête avec le token CSRF dans les headers
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken, // Envoi du token CSRF dans l'en-tête
                    },
                    body: JSON.stringify({
                        username: this.username,
                        password: this.password,
                    })
                });

                const data = await response.json();
                if (response.ok) {
                    localStorage.setItem("username", this.username);
                    this.$router.push(data.redirect || '/dashboard');
                } else {
                    this.errorMessage = data.message || "Erreur de connexion";
                }
            } catch (error) {
                this.errorMessage = "Une erreur est survenue, veuillez réessayer.";
            }
        }
    }
};
</script>

<style scoped>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #f4f7fc;
}

.login-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}
</style>
