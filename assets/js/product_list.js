$(document).on('bs.roulette.add', function (e, element, button) {
    window.location = '/blueseal/prodotti/roulette?roulette=' + $(element).val();
});