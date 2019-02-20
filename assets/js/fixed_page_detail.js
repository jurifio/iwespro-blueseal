tinymce.init({
    selector: 'textarea#page-fixed-content',
    entity_encoding: "raw",
    plugins: "code"
});

(function ($) {

    function setTargetColor(target, min, max) {
        if ($(target).val().length < min || $(target).val().length > max) {
            $(target).css('color', 'red')
        } else {
            $(target).css('color', 'green')
        }
    }

    $(document).ready(function () {
        setTargetColor('#titleTag', 50, 60);
        setTargetColor('#metaDescription', 50, 300);
    });

    $('#titleTag').on('keyup', function (e) {
        setTargetColor(this, 50, 60)
    });

    $('#metaDescription').on('keyup', function (e) {
        setTargetColor(this, 50, 300)
    });

    $(document).on('bs.fixedPageSave', function () {
        let params = window.location.href.split('/');
        let fixedPageId = params[params.length - 2];
        // let fixedLangId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

        let method = fixedPageId === ':id' ? 'post' : 'put';

        tinyMCE.triggerSave();
        let title = $('#title').val();
        let subTitle = $('#subTitle').val();
        let slug = $('#slug').val();
        let text = encodeURIComponent($('#page-fixed-content').val());
        let titleTag = $('#titleTag').val();
        let metaDescription = $('#metaDescription').val();

        if (
            title === "" ||
            subTitle === "" ||
            slug === "" ||
            text === "" ||
            titleTag === "" ||
            metaDescription === ""
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
            title: title,
            subtitle: subTitle,
            slug: slug,
            text: text,
            titleTag: titleTag,
            metaDescription: metaDescription
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
                window.location.href = `/blueseal/manage-fixed-page/${fp['id']}/${fp['langId']}`;
            }
        }).fail(function (res) {
            new Alert({
                type: "danger",
                message: "Errore durante il salvataggio dei dati, contattare l'assistanza tecnica"
            }).open();
            return false;
        });

    });

})(jQuery);


