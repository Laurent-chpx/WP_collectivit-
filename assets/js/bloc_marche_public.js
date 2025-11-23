jQuery(document).ready(function($) {
    $('.bloc-marche-public--btnLireAvisComplet').click(function () {
        avisComplet = $(this).closest('.bloc-marche-public').find('.bloc-marche-public--avisComplet');
        avisCompletAffiche = !avisComplet.is(':visible');
        avisComplet.slideToggle();
        $(this).html(avisCompletAffiche ? 'Masquer les informations supplémentaires' : 'En savoir plus');
    });
});
