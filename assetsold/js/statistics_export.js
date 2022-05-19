'use strict';
$(document).on('click', '.create-statistics', function(){
    var messageDiv = $('.export-message');
    var message = messageDiv.html();
    var date = $('.export-max-date');
    var downloadLink = message.find(a);
    var genButton = $('.create-statistics');

    genButton.prop('disabled', true);
    messageDiv.html('Sto generando i file aggiornati. Attendi qualche istante...');
    $.ajax({
        url: '/blueseal/xhr/GenerateStatistiscFile',
        method: 'GET',
        data: {}
    }).done(function(res){
        message.find(media);

        messageDiv.html(message);
    }).fail(function(res){

    }).always(function(){
        genButton.prop('disabled', false);
    });
    //$this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal')
});