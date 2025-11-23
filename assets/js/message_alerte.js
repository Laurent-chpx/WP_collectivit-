jQuery(document).ready(function($){
    let bandeauTexteWPCollectivites = $('.wpcollectivites-bandeau--texte');
    temps = bandeauTexteWPCollectivites.width() / 150;
    bandeauTexteWPCollectivites.css('animation-duration', temps+'s');
    bandeauTexteWPCollectivites.hover(function(){
        $(this).css('animation-play-state', 'paused');
    }, function(){
        $(this).css('animation-play-state', 'running');
    })
    bandeauTexteWPCollectivites.css('display', 'flex');

    let popupWPCollectivites = $('#wpcollectivites-popup');
    if(popupWPCollectivites.length > 0 && typeof Cookies.get('wpcollectivites-popup') == "undefined"){
        popupWPCollectivites.css('display', 'flex');

        $('.wpcollectivites-popup--fermeture').click(function(){
            popupWPCollectivites.remove();
            Cookies.set('wpcollectivites-popup', '1', { expires: 1 })
        });
    }
});
