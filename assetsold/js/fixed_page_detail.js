tinymce.init({
    entity_encoding : "raw",
    selector: "textarea#page-fixed-content",
    relative_urls : false,
    document_base_url : "https://www.pickyshop.com/",
    convert_urls: false,
    allow_script_urls: true,
    height: 450,
    plugins: [
        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
    ],

    toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
    toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
    toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | visualchars visualblocks nonbreaking template pagebreak restoredraft",
    content_css: [
        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
        '//www.tinymce.com/css/codepen.min.css'],

    menubar: false,
    toolbar_items_size: 'small',

    style_formats: [{
        title: 'Bold text',
        inline: 'b'
    }, {
        title: 'Red text',
        inline: 'span',
        styles: {
            color: '#ff0000'
        }
    }, {
        title: 'Red header',
        block: 'h1',
        styles: {
            color: '#ff0000'
        }
    }, {
        title: 'Example 1',
        inline: 'span',
        classes: 'example1'
    }, {
        title: 'Example 2',
        inline: 'span',
        classes: 'example2'
    }, {
        title: 'Table styles'
    }, {
        title: 'Table row 1',
        selector: 'tr',
        classes: 'tablerow1'
    }],

    templates: [{
        title: 'Test template 1',
        content: 'Test 1'
    }, {
        title: 'Test template 2',
        content: 'Test 2'
    }],

    init_instance_callback: function () {
        window.setTimeout(function() {
            $("#div").show();
        }, 1000);
    }
});

(function ($) {

    function setTargetColor(target, min, max) {
        if ($(target).val().length < min || $(target).val().length > max) {
            $(target).css('color', 'red')
        } else {
            $(target).css('color', 'green')
        }
    }


    Dropzone.autoDiscover = false;
    $(document).ready(function () {

        let dropzone = new Dropzone("#dropzoneModal",{
            url: "/blueseal/xhr/ManageFixedPagePhotoAjaxController",
            maxFilesize: 5,
            maxFiles: 5,
            parallelUploads: 5,
            acceptedFiles: "image/*",
            dictDefaultMessage: "Trascina qui le foto",
            uploadMultiple: true,
            sending: function(file, xhr, formData) {
                formData.append("fixedPagePopupId", $('#popupUse').attr('data-idpopup'));
            }
        });

        dropzone.on('queuecomplete',function(){
            window.location.reload();
            $(document).trigger('bs.load.photo');
        });

        setTargetColor('#titleTag', 50, 60);
        setTargetColor('#metaDescription', 50, 300);
        if($('#fixedPageType').val() == 3) {
            populateCouponList();
            $('#selectTemplate').append(
                `
                <strong>Seleziona il template</strong>
                <select class="full-width" id="templateFixedPage">
                </select>
                `
            );

            $.ajax({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'FixedPageTemplate'
                },
                dataType: 'json'
            }).done(function (tempFixPage) {
                let selectTemplate = $('#templateFixedPage');
                if (selectTemplate.length > 0 && typeof selectTemplate[0].selectize != 'undefined') selectTemplate[0].selectize.destroy();
                selectTemplate.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: tempFixPage,
                });
            });

        }
    });

    $('#titleTag').on('keyup', function (e) {
        setTargetColor(this, 50, 60)
    });

    $('#metaDescription').on('keyup', function (e) {
        setTargetColor(this, 50, 300)
    });

    $(document).on('bs.fixedPageSave', function () {
        let params = window.location.href.split('/');
        let fixedPageId = params[params.length - 3];
        // let fixedLangId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

        let method = fixedPageId === ':id' ? 'post' : 'put';

        tinyMCE.triggerSave();
        let title = $('#title').val();
        let subTitle = $('#subTitle').val();
        let slug = $('#slug').val();
        let text = encodeURIComponent($('#page-fixed-content').val());
        let titleTag = $('#titleTag').val();
        let metaDescription = $('#metaDescription').val();
        let fixedPageTypeId = $('#fixedPageType').val();

        let popupUse = !!$('#popupUse').is(':checked');
        let popupTitle = $('#popupTitle').val();
        let popupSubTitle = $('#popupSubTitle').val();
        let popupText = $('#popupText').val();
        let couponEvent = $('#couponSelect').val();

        let popupTitleSub = $('#popupTitleSub').val();
        let popupSubTitleSub = $('#popupSubTitleSub').val();
        let popupTextSub = $('#popupTextSub').val();
        if (
            (title === "" && fixedPageTypeId != 3) ||
            (subTitle === "" && fixedPageTypeId != 3) ||
            slug === "" ||
            text === "" ||
            titleTag === "" ||
            metaDescription === "" ||
            (
                popupUse &&
                (
                    popupTitle.trim() === ''
                )
            )
        ) {
            new Alert({
                type: "danger",
                message: "Inserisci tutti i dati"
            }).open();
            return false;
        }

        const data = {
            id: fixedPageId,
            lang: $('#lang').val(),
            fixedPageTypeId: fixedPageTypeId,
            title: title,
            subtitle: subTitle,
            slug: slug,
            text: text,
            titleTag: titleTag,
            metaDescription: metaDescription,
            popupUse: popupUse,
            popupSubTitle: popupSubTitle,
            popupTitle: popupTitle,
            popupText: popupText,
            couponEvent: couponEvent,
            popupId: $('#popupUse').attr('data-idpopup'),
            popupTitleSub: popupTitleSub,
            popupSubTitleSub: popupSubTitleSub,
            popupTextSub: popupTextSub
        };

        $.ajax({
            method: method,
            url: '/blueseal/xhr/ManageFixedPageAjaxController',
            data: data
        }).done(function (res) {
            if (res === 'put') {
                new Alert({
                    type: "success",
                    message: "Dati salvati con successo"
                }).open();
                return false;
            } else {
                let fp = JSON.parse(res);
                window.location.href = `/blueseal/manage-fixed-page/${fp['id']}/${fp['langId']}/${fp['fixedPageTypeId']}`;
            }
        }).fail(function (res) {
            new Alert({
                type: "danger",
                message: "Errore durante il salvataggio dei dati, contattare l'assistanza tecnica"
            }).open();
            return false;
        });

    });

    $(document).on('change', '#fixedPageType', function () {

        let hideDiv = $('#optionalPart');
        let leadSection = $('#lead-section');
        if ($('#fixedPageType').val() == 3) {

            populateCouponList();

            let params = window.location.href.split('/');
            let fixedPageId = params[params.length - 3];

            if (fixedPageId === ':id') $('#photoPopup').hide();

            $('#selectTemplate').append(
                `
                <strong>Seleziona il template</strong>
                <select class="full-width" id="templateFixedPage">
                </select>
                `
            );

            $.ajax({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'FixedPageTemplate'
                },
                dataType: 'json'
            }).done(function (tempFixPage) {
                let selectTemplate = $('#templateFixedPage');
                if (selectTemplate.length > 0 && typeof selectTemplate[0].selectize != 'undefined') selectTemplate[0].selectize.destroy();
                selectTemplate.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: tempFixPage,
                });
            });

            hideDiv.hide();
            leadSection.show();
        } else {
            hideDiv.show();
            leadSection.hide();
        }
    });


    $(document).on('change', '#templateFixedPage', function () {

        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'FixedPageTemplate',
                condition: {
                    id: $('#templateFixedPage').val()
                }
            },
            dataType: 'json'
        }).done(function (tempFixPage) {
            $('#page-fixed-content').empty();
            tinymce.get('page-fixed-content').setContent(tempFixPage[0].template);
        });
    });

    function populateCouponList() {

        let params = window.location.href.split('/');
        let fixedPageId = params[params.length - 3];

        if(fixedPageId !== ':id'){
            $.ajax({
                method: 'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'FixedPagePopup',
                    condition: {
                        fixedPageId: fixedPageId,
                        isActive: 1
                    }
                },
                dataType: 'json'
            }).done(function (res) {
                let couponEventId = res[0]['couponEventId'];
                $.ajax({
                    method: 'GET',
                    url: '/blueseal/xhr/GetTableContent',
                    data: {
                        table: 'CouponEvent'
                    },
                    dataType: 'json'
                }).done(function (res) {
                    let select = $('#couponSelect');
                    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                    select.selectize({
                        valueField: 'id',
                        labelField: ['name'],
                        searchField: ['name'],
                        options: res
                    });
                    select.selectize()[0].selectize.setValue(couponEventId);
                });
            });
        } else {
            $.ajax({
                method: 'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'CouponEvent'
                },
                dataType: 'json'
            }).done(function (res) {
                let select = $('#couponSelect');
                if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: ['name'],
                    searchField: ['name'],
                    options: res
                });
            });
        }
    }

})(jQuery);


