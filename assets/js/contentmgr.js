var contentManager = (function($) {

    var config = {
        sectionList: "#sectionList",
        contentContainer: "#content-wrapper",
        tools: ".actions",
        panel: ".email-content-wrapper",
        noContentSelected: ".no-email"
    };

    function get(key)
    {
        return config[key];
    }

    function set(key, value)
    {
        config[key] = value;
    }

    return {

        loadSections: function(name)
        {
            $(get('sectionList'))
                .html(this.getLoader())
                .load('/blueseal/xhr/ContentManagerSectionLoader', {section:name});

            if ($(get('tools')).is(':visible')) {
                $(get('tools')).slideUp();
                $(get('panel')).fadeOut();
                $(get('noContentSelected')).html($(document).data('noContentSelected')).show();
            }
        },
        loadSectionForm: function(section, type, id)
        {
            $(get('contentContainer')).fadeOut(100, function() {
                $(document).data('noContentSelected', $(get('noContentSelected')).html(contentManager.getLoader()));
                $(get('noContentSelected')).html("");

                $(get('contentContainer')).load('/blueseal/xhr/ContentManagerContentForm', {section:section,type:type,'id':id}, function() {
                    $(get('noContentSelected')).hide();
                    $(get('contentContainer')).show();
                    $(get('panel')).fadeIn();
                    $(get('tools')).slideDown();
                });
            });
        },
        getLoader: function()
        {
            return "<p style='width:100%;height:120px;background:url(http://www.pickyshop.com/blueseal/pages/img/progress/progress-circle-success.svg) no-repeat center'>&nbsp;</p>";
        },
        submitChanges: function()
        {

        }
    };

})(jQuery);

var cmgr = contentManager;

$(".section-selector").on('click',function() {
    $(".email-sidebar ul li").removeClass('active');
    $(this).parent().addClass('active');
    cmgr.loadSections($(this).data('section'));
});

$(document).on('click', '.item', function() {
    $(".item").removeClass("active");
    $(this).addClass("active");
    var data = $(this).data();
    cmgr.loadSectionForm(data.section, data.type, data.id);
});
