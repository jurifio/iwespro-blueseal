
var summer = $('textarea.summer');
summer.summernote({
    lang: "it-IT",
    height: 300,
    fontNames: [
        'Arial',
        'Arial Black',
        'Comic Sans MS',
        'Courier',
        'Courier New',
        'Helvetica',
        'Impact',
        'Lucida Grande',
        'Raleway',
        'Serif',
        'Sans',
        'Sacramento',
        'Tahoma',
        'Times New Roman',
        'Verdana'
    ],
    onImageUpload: function() {},
    fontNamesIgnoreCheck: ['Raleway']
});
$.ajax({
    method:'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'Shop',
        condition :{hasEcommerce:1}
    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#shopId');
    if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: res2,
    });
});

summer.on('summernote.image.upload', function(we, files) {
    we.preventDefault();
    we.stopPropagation();
    var data = new FormData();
    data.append("file", files[0]);
    $.ajaxForm({
        url: '/blueseal/xhr/BlogPostPhotoUploadAjaxController',
        type: 'POST',
        formAutofill: false
    },data).done(function (result) {
        var i = new Image;
        i.src = result;
        summer.summernote('insertNode', i);
    }).fail(function (result) {
        console.log('fail');
    });
});

$('[data-toggle="popover"]').popover();

$('#cover').on('click',function() {
    $('[data-json="PostTranslation.coverImage"]').click();
});

$('[data-json="PostTranslation.coverImage"]').on('change', function(){
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cover').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
    }
});
(function ($) {
    Pace.ignore(function () {


    });
})
(jQuery);

$(document).on('bs.newEmailTemplate.save', function () {
    let bsModal = new $.bsModal('Salva Template', {
        body: '<div><p>Premere ok per Salvare il Template'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

       var template= $('#template').val();
        var langarray=$('#langarray').val();
          var arraytemplate=[];
        var larray = langarray.split("-");
        $.each(larray, function( index, value ) {
            var field='#'+value;
            arraytemplate.push({id:value,template:$(field).val()});

        });


        const data = {
            name : $('#name').val(),
            shopId:$('#shopId').val(),
            description:$('#description').val(),
            subject:$('#subject').val(),
            scope:$('#scope').val(),
            isActive:$('#isActive').val(),
            arraytemplate:arraytemplate,
            template: template,
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/EmailTemplateManage',
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