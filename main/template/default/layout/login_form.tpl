{% if _u.logged  == 0 %}
{% if login_form %}
    <div id="login_block" class="panel panel-default">
        <div class="panel-body">
        {{ login_language_form }}

        {% if plugin_login_top is not null %}
            <div id="plugin_login_top">
                {{ plugin_login_top }}
            </div>
        {% endif %}

        {{ login_failed }}
        {{ login_form }}

        {% if "allow_lostpassword" | get_setting == 'true' or "allow_registration" | get_setting == 'true' %}
            <ul class="nav nav-pills nav-stacked">
                {% if "allow_registration" | get_setting != 'false' %}
                    <li><a href="{{ _p.web_main }}auth/inscription.php"> {{ 'SignUp' | get_lang }} </a></li>
                {% endif %}

                {% if "allow_lostpassword" | get_setting == 'true' %}
                    <li><a href="{{ _p.web_main }}auth/lostPassword.php"> {{ 'LostPassword' | get_lang }} </a></li>
                {% endif %}
            </ul>
        {% endif %}

        {% if plugin_login_bottom is not null %}
            <div id="plugin_login_bottom">
                {{ plugin_login_bottom }}
            </div>
        {% endif %}
        </div>
    </div>
{% endif %}
{% endif %}