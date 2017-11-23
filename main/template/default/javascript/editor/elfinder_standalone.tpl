{% set finderFolder = _p.web ~ 'vendor/barryvdh/elfinder-builds/' %}
<!-- elFinder CSS (REQUIRED) -->
<link rel="stylesheet" type="text/css" media="screen" href="{{ finderFolder }}css/elfinder.full.css">
<link rel="stylesheet" type="text/css" media="screen" href="{{ finderFolder }}css/theme.css">

<!-- elFinder JS (REQUIRED) -->
<script type="text/javascript" src="{{ finderFolder }}js/elfinder.full.js"></script>

<!-- elFinder translation (OPTIONAL) -->
<script type="text/javascript" src="{{ finderFolder }}js/i18n/elfinder.ru.js"></script>

<script type="text/javascript" charset="utf-8">
    // Helper function to get parameters from the query string.
    function getUrlParam(paramName) {
        var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);
        return (match && match.length > 1) ? match[1] : '';
    }

    $().ready(function() {
        var funcNum = getUrlParam('CKEditorFuncNum');
        var elf = $('#elfinder').elfinder({
            url : '{{ _p.web_lib ~ 'elfinder/connectorAction.php?' }}{{ course_condition }}',  // connector URL (REQUIRED)
            getFileCallback : function(file) {
                window.opener.CKEDITOR.tools.callFunction(funcNum, file.url);
                window.close();
            },
            resizable: false
        }).elfinder('instance');
    });
</script>
<div id="elfinder"></div>



