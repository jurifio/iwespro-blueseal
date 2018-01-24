(function ($) {
    if (CKEDITOR.instances.preCompiledTemplate1) {
        CKEDITOR.instances.preCompiledTemplate1.destroy();
    }
    CKEDITOR.replace( 'preCompiledTemplate1', {
        height: 260,
        width:1280,
        startupMode:'source'

    } );
    /*function readSingleFile(e) {
        var file = e.target.files[0];
        if (!file) {
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            var contents = e.target.result;
            displayContents(contents);
        };
        reader.readAsText(file);
    }*/
    $("#newsletterTemplateId").change(function () {
        CKEDITOR.instances.preCompiledTemplate1.setData("");

            $("#preCompiledTemplate1").empty();
            var content1 = $(this).val();
        CKEDITOR.instances.preCompiledTemplate1.setData(content1);
            //var contentLessOccurence = content.indexOf('-');
          //  var contentPreview = content.substring(5);
       // $("#file-content").append(content);


    });

    /*function displayContents(contents) {
        var element = document.getElementById('file-content');
        element.innerHTML= contents;
        $("#preCompiledTemplate1").val(contents);

    }*/


   /* document.getElementById('preCompiledTemplate1')
        .addEventListener('change', readSingleFile, false);*/
    Pace.ignore(function () {




        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterEmailList'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#newsletterEmailListId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });
        });
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'NewsletterTemplate'
            },
            dataType: 'json'
        }).done(function (res2) {

            var select = $('#newsletterTemplateId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'template',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });

        });
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Campaign'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#campaignId');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
        });
    });
})(jQuery);

$(document).on('bs.newNewsletterUser.save', function () {
    let bsModal = new $.bsModal('Salva newsletter', {
        body: '<div><p>Premere ok per Salvare la Newsletter'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
        name: $('#name').val(),
        fromEmailAddressId : $('#fromEmailAddressId').val(),
        sendAddressDate : $('#sendAddressDate').val(),
        newsletterEmailListId : $('#newsletterEmailListId').val(),
        newsletterTemplateId:$('#newsletterTemplateId').val(),
        subject : $('#subject').val(),
        dataDescription : $('#dataDescription').val(),
        preCompiledTemplate : CKEDITOR.instances.preCompiledTemplate1.getData(),
        campaignId : $('#campaignId').val()
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/NewsletterUserManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
               // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});




