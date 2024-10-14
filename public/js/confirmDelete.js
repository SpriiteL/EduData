document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-button').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cette ligne d\'inventaire ?')) {
                window.location.href = this.getAttribute('href');
            }
        });
    });
});