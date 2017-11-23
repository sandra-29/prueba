<meta charset="{{ system_charset }}" />
<link href="https://chamilo.org/chamilo-lms/" rel="help" />
<link href="https://chamilo.org/the-association/" rel="author" />
<link href="https://chamilo.org/the-association/" rel="copyright" />
<!-- Force latest IE rendering engine or ChromeFrame if installed -->
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<![endif]-->
{{ prefetch }}
{{ favico }}
<link rel="apple-touch-icon" href="{{ _p.web }}apple-touch-icon.png" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="generator" content="{{ _s.software_name }} {{ _s.system_version|slice(0,1) }}" />
{#  Use the latest engine in ie8/ie9 or use google chrome engine if available  #}
{#  Improve usability in portal devices #}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ title_string }}</title>
{{ social_meta }}
{{ css_static_file_to_string }}
{{ js_file_to_string }}
{{ extra_headers }}
<script>

/* Global chat variables */
var ajax_url = '{{ _p.web_ajax }}chat.ajax.php';
var online_button = '{{ online_button }}';
var offline_button = '{{ offline_button }}';
var connect_lang = '{{ "ChatConnected"|get_lang }}';
var disconnect_lang = '{{ "ChatDisconnected"|get_lang }}';
</script>

{% include template ~ '/layout/header.js.tpl' %}

{{ css_custom_file_to_string }}
{{ css_style_print }}
{{ header_extra_content }}
