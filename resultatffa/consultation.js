"use strict";

window.onload = init;

/**
 * Initialisation du composant table sorter
 * Récupération des membres pour un affichage en mode tableau
 */
function init() {
    $('[data-toggle="tooltip"]').tooltip();
    $("#leTableau").tablesorter({
        headers: {
            16: {sorter: false}
        }
    });

    $.ajax({
        url: 'ajax/getlescourses.php',
        dataType: 'json',
        error: reponse => {
            msg.innerHTML = Std.genererMessage(reponse.responseText)
        },
        success: function (data) {
            for (const resultatffa of data) {
                let tr = lesLignes.insertRow();

                tr.insertCell().innerText = resultatffa.date;
                tr.insertCell().innerText = resultatffa.titre;
            }
            $("#leTableau").trigger('update');

            pied.style.visibility = 'visible';
        }

    });


}
