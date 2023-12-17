document.addEventListener('DOMContentLoaded', function () {

    // Fonction pour générer une couleur aléatoire en rgb
    function getRandomColor() {
        var color = 'rgb(';
        var nbr = 0;
        for (var i = 0; i < 3; i++) {
            // Génération d'un nombre aléatoire entre 0 et 255 pour chaque composant RGB
            nbr = Math.floor(Math.random() * 200);
            color += nbr;
            if (i < 2) {
                color += ', ';
            }
            else color += ')';
        }


        return color;
    }

    // Fonction pour changer la couleur des liens
    function changeLinkColors() {
        var links = document.querySelectorAll('.lienArticles');
        var color = getRandomColor();
        links.forEach(function (link) {
            link.style.color = color;
        });

    }

    // Appel au chargement de la page pour générer une première couleur aléatoire
    changeLinkColors();

    // Changement de la couleur au clic de la pagination
    var paginationClic = document.getElementById('paginationArticles');
    paginationClic.addEventListener('click', function () {
        changeLinkColors();
    });
});