<?php

/* default/agenda/month.tpl */
class __TwigTemplate_ca7eaa0211385193c1fd85f22a516dd337134f1b3252c3e781419228d4969e41 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<script>
function checkLength( o, n, min, max ) {
    if ( o.val().length > max || o.val().length < min ) {
        o.addClass( \"ui-state-error\" );
        /*updateTips( \"Length of \" + n + \" must be between \" +
            min + \" and \" + max + \".\" );*/
        return false;
    } else {
        return true;
    }
}
function clean_user_select() {
    //Cleans the selected attr
    \$(\"#users_to_send\").val('').trigger(\"chosen:updated\");
    /*\$('#users_to_send')
        .find('option')
        .removeAttr('selected')
        .end();*/
}

var region_value = '";
        // line 21
        echo (isset($context["region_value"]) ? $context["region_value"] : null);
        echo "';

\$(document).ready(function() {
\tvar date = new Date();

    // Reset button.
    \$(\"button[type=reset]\").click(function() {
        \$(\"#session_id\").find('option').removeAttr(\"selected\");
    });

\t\$(\"#dialog-form\").dialog({
\t\tautoOpen: false,
\t\tmodal\t: false,
\t\twidth\t: 580,
\t\theight\t: 480,
        zIndex: 20000 // added because of qtip2
   \t});

    \$(\"#simple-dialog-form\").dialog({
\t\tautoOpen: false,
\t\tmodal\t: false,
\t\twidth\t: 580,
\t\theight\t: 480,
        zIndex: 20000 // added because of qtip2
   \t});

\tvar title = \$(\"#title\"),
\tcontent = \$( \"#content\" ),
\tallFields = \$( [] ).add( title ).add( content ), tips = \$(\".validateTips\");

    \$(\"#select_form_id_search\").change(function() {
        var temp =\"&user_id=\"+\$(\"#select_form_id_search\").val();
        var position =String(window.location).indexOf(\"&user\");
        var url_length = String(window.location).length;
        var url = String(window.location).substring(0,position)+temp;
        if (position > 0) {
            window.location.replace(url);
        } else {
            url = String(window.location)+temp;
            window.location.replace(url);
        }
    });

    \$.datepicker.setDefaults( \$.datepicker.regional[region_value] );

\tvar calendar = \$('#calendar').fullCalendar({
\t\theader: {
\t\t\tleft: 'today prev,next',
\t\t\tcenter: 'title',
\t\t\tright: 'month,agendaWeek,agendaDay'
\t\t},
        ";
        // line 72
        if (((isset($context["use_google_calendar"]) ? $context["use_google_calendar"] : null) == 1)) {
            // line 73
            echo "            eventSources: [
                // if you want to add more just add URL in this array
                '";
            // line 75
            echo (isset($context["google_calendar_url"]) ? $context["google_calendar_url"] : null);
            echo "',
                {
                    className: 'gcal-event' // an option!
                }
            ],
        ";
        }
        // line 81
        echo "
        defaultView:    '";
        // line 82
        echo (isset($context["default_view"]) ? $context["default_view"] : null);
        echo "',
\t\tbuttonText: \t";
        // line 83
        echo (isset($context["button_text"]) ? $context["button_text"] : null);
        echo ",
\t\tmonthNames: \t";
        // line 84
        echo (isset($context["month_names"]) ? $context["month_names"] : null);
        echo ",
\t\tmonthNamesShort:";
        // line 85
        echo (isset($context["month_names_short"]) ? $context["month_names_short"] : null);
        echo ",
\t\tdayNames: \t\t";
        // line 86
        echo (isset($context["day_names"]) ? $context["day_names"] : null);
        echo ",
\t\tdayNamesShort: \t";
        // line 87
        echo (isset($context["day_names_short"]) ? $context["day_names_short"] : null);
        echo ",
        firstHour: 8,
        firstDay: 1,
\t\tselectable\t: true,
\t\tselectHelper: true,
        viewDisplay: function(view) {
            /* When changing the view update the qtips */
            /*var api = \$('.qtip').qtip('api'); // Access the API of the first tooltip on the page
            if (api) {
                api.destroy();
                //api.render();
            }*/
        },
\t\t// Add event
\t\tselect: function(start, end, jsEvent, view) {
            var start_date = start.format(\"YY-MM-DD\");
            var end_date = end.format(\"YY-MM-DD\");

            var allDay = true;
            if (end.hasTime()) {
                allDay = false;
            }

\t\t\t\$('#visible_to_input').show();
\t\t\t\$('#add_as_announcement_div').show();
\t\t\t\$('#visible_to_read_only').hide();

\t\t\t// Cleans the selected attr
\t\t    clean_user_select();

            // Sets the 1st item selected by default
            \$('#users_to_send option').eq(0).attr('selected', 'selected');

\t\t\t// Update chz-select
\t\t\t//\$(\"#users_to_send\").trigger(\"chosen:updated\");

\t\t\tif (";
        // line 123
        echo (isset($context["can_add_events"]) ? $context["can_add_events"] : null);
        echo " == 1) {
\t\t\t\tvar url = '";
        // line 124
        echo (isset($context["web_agenda_ajax_url"]) ? $context["web_agenda_ajax_url"] : null);
        echo "&a=add_event&start='+start.format('YYYY-MM-DD 00:00:00')+'&end='+end.format('YYYY-MM-DD 00:00:00')+'&all_day='+allDay+'&view='+view.name;
                var start_date_value = start.format('";
        // line 125
        echo (isset($context["js_format_date"]) ? $context["js_format_date"] : null);
        echo "');
                var end_date_value = end.format('";
        // line 126
        echo (isset($context["js_format_date"]) ? $context["js_format_date"] : null);
        echo "');

                \$('#start_date').html(start_date_value);

                if (start_date_value == end_date_value) {
                    \$('#end_date').html(' - ' + end_date_value);
                } else {
                    \$('#start_date').html('');
                    \$('#end_date').html(start_date_value+\" - \" + end_date_value);
                }

\t\t\t\t\$('#color_calendar').html('";
        // line 137
        echo (isset($context["type_label"]) ? $context["type_label"] : null);
        echo "');
\t\t\t\t\$('#color_calendar').removeClass('group_event');
\t\t\t\t\$('#color_calendar').addClass('label_tag');
\t\t\t\t\$('#color_calendar').addClass('";
        // line 140
        echo (isset($context["type_event_class"]) ? $context["type_event_class"] : null);
        echo "');

                //It shows the CKEDITOR while Adding an Event
                \$('#cke_content').show();
                //It Fixing a minor bug with textarea ckeditor.remplace
                \$('#content').css('display','none');
                //Reset the CKEditor content that persist in memory
                CKEDITOR.instances['content'].setData('');

\t\t\t\tallFields.removeClass(\"ui-state-error\");
\t\t\t\t\$(\"#dialog-form\").dialog(\"open\");
\t\t\t\t\$(\"#dialog-form\").dialog({
\t\t\t\t\tbuttons: {
\t\t\t\t\t\t'";
        // line 153
        echo get_lang("Add");
        echo "' : function() {
\t\t\t\t\t\t\tvar bValid = true;
\t\t\t\t\t\t\tbValid = bValid && checkLength(title, \"title\", 1, 255);

                            //Update the CKEditor Instance to the remplaced textarea, ready to be serializable
                            for ( instance in CKEDITOR.instances ) {
                                CKEDITOR.instances[instance].updateElement();
                            }

\t\t\t\t\t\t\tvar params = \$(\"#add_event_form\").serialize();

\t\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\t\turl: url+'&'+params,
\t\t\t\t\t\t\t\tsuccess:function(data) {
\t\t\t\t\t\t\t\t\tvar user = \$('#users_to_send').val();
                                    if (user) {
                                        if (user.length > 1) {
                                            user = 0;
                                        } else {
                                            user = user[0];
                                        }
                                        var user_length = String(user).length;
                                        if (String(user).substring(0,1) == 'G') {
                                            var user_id = String(user).substring(6,user_length);
                                            var user_id = \"G:\"+user_id;
                                        } else {
                                            var user_id = String(user).substring(5,user_length);
                                        }
                                        var temp = \"&user_id=\"+user_id;
                                        var position = String(window.location).indexOf(\"&user\");
                                        var url_length = String(window.location).length;
                                        var url = String(window.location).substring(0, position)+temp;
                                        /*if (position > 0) {
                                            window.location.replace(url);
                                        } else {
                                            url = String(window.location)+temp;
                                            window.location.replace(url);
                                        }*/
                                    } else {
                                \t   /* calendar.fullCalendar(\"refetchEvents\");
\t\t\t\t\t\t\t\t\t    calendar.fullCalendar(\"rerenderEvents\");*/
                                    }

                                    \$(\"#title\").val('');
                                    \$(\"#content\").val('');
                                    \$(\"#comment\").val('');

                                    calendar.fullCalendar(\"refetchEvents\");
                                    calendar.fullCalendar(\"rerenderEvents\");

\t\t\t\t\t\t\t\t\t\$(\"#dialog-form\").dialog(\"close\");
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t});
\t\t\t\t\t\t}
\t\t\t\t\t},
\t\t\t\t\tclose: function() {
                        \$(\"#title\").val('');
                        \$(\"#content\").val('');
                        \$(\"#comment\").val('');
\t\t\t\t\t}
\t\t\t\t});

\t\t\t\tcalendar.fullCalendar('unselect');
                //Reload events
                calendar.fullCalendar(\"refetchEvents\");
                calendar.fullCalendar(\"rerenderEvents\");
\t\t\t}
\t\t},
\t\teventRender: function(event, element) {
            if (event.attachment) {
                /*element.qtip({
                    hide: {
                        delay: 2000
                    },
\t\t            content: event.attachment,
\t\t            position: { at:'top right' , my:'bottom right'}
\t\t        }).removeData('qtip'); // this is an special hack to add multiple qtip in the same target
\t\t        */
            }
\t\t\tif (event.description) {
                var comment = '';
                if (event.comment) {
                    comment = event.comment;
                }

\t\t\t\telement.qtip({
                    hide: {
                        delay: 2000
                    },
\t\t            content: event.description + ' ' + comment,
\t\t            position: { at:'top left' , my:'bottom left'}
\t\t        });
\t\t\t}
\t    },
\t\teventClick: function(calEvent, jsEvent, view) {
            if (!calEvent.end) {
                calEvent.end = calEvent.start;
            }

            var start_date = calEvent.start.format(\"YY-MM-DD\");

            if (calEvent.allDay == 1) {
                var end_date \t= '';
            } else {
                var end_date \t= '';
                if (calEvent.end && calEvent.end != '') {
                    var end_date  = calEvent.end.format(\"YY-MM-DD\");
                }
            }

\t\t\t// Edit event.
\t\t\tif (calEvent.editable) {

\t\t\t\t\$('#visible_to_input').hide();
                \$('#add_as_announcement_div').hide();

                ";
        // line 269
        if (((isset($context["type"]) ? $context["type"] : null) != "admin")) {
            // line 270
            echo "                    \$('#visible_to_read_only').show();
                    \$(\"#visible_to_read_only_users\").html(calEvent.sent_to);
\t\t\t\t";
        }
        // line 273
        echo "
                \$('#color_calendar').html('";
        // line 274
        echo (isset($context["type_label"]) ? $context["type_label"] : null);
        echo "');
                \$('#color_calendar').addClass('label_tag');
                \$('#color_calendar').removeClass('course_event');
                \$('#color_calendar').removeClass('personal_event');
                \$('#color_calendar').removeClass('group_event');
                \$('#color_calendar').addClass(calEvent.type+'_event');

                //It hides the CKEDITOR while clicking an existing Event
                \$('#cke_content').hide();

                \$('#start_date').html(calEvent.start.format(\"YY-MM-DD\"));
                if (calEvent.end) {
                    \$('#end_date').html(' - '+calEvent.end.format(\"YY-MM-DD\"));
                }

                if (\$(\"#title\").parent().find('#title_edit').length == 0) {
                    \$(\"#title\").parent().append('<div id=\"title_edit\"></div>');
                }

                \$(\"#title_edit\").html(calEvent.title);

                if (\$(\"#content\").parent().find('#content_edit').length == 0) {
                    \$(\"#content\").parent().append('<div id=\"content_edit\"></div>');
                }
                \$(\"#content_edit\").html(calEvent.description);

                if (\$(\"#comment\").parent().find('#comment_edit').length == 0) {
                    \$(\"#comment\").parent().append('<div id=\"comment_edit\"></div>');
                }

                if (calEvent.course_name) {
                    \$(\"#calendar_course_info\").html(
                        '<div class=\"form-group\"><label class=\"col-sm-2 control-label\">";
        // line 306
        echo get_lang("Course");
        echo "</label>' +
                        '<div class=\"class=\"col-sm-8\">' + calEvent.course_name+\"</div></div>\"
                    );
                } else {
                    \$(\"#calendar_course_info\").html('');
                }

                if (calEvent.session_name) {
                    \$(\"#calendar_session_info\").html(
                        '<div class=\"form-group\"><label class=\"col-sm-2 control-label\">";
        // line 315
        echo get_lang("Session");
        echo "</label>'+
                        '<div class=\"class=\"col-sm-8\">' + calEvent.session_name+\"</div></div>\"
                    );
                } else {
                    \$(\"#calendar_session_info\").html('');
                }

                \$(\"#comment_edit\").html(calEvent.comment);

                \$(\"#title_edit\").show();
                \$(\"#content_edit\").show();
                \$(\"#comment_edit\").show();

                \$(\"#title\").hide();
                \$(\"#content\").hide();
                \$(\"#comment\").hide();

\t\t\t\tallFields.removeClass( \"ui-state-error\" );

\t\t\t\t\$(\"#dialog-form\").dialog(\"open\");

\t\t\t\tvar url = '";
        // line 336
        echo (isset($context["web_agenda_ajax_url"]) ? $context["web_agenda_ajax_url"] : null);
        echo "&a=edit_event&id='+calEvent.id+'&start='+calEvent.start.unix()+'&end='+calEvent.end.unix()+'&all_day='+calEvent.allDay+'&view='+view.name;
\t\t\t\tvar delete_url = '";
        // line 337
        echo (isset($context["web_agenda_ajax_url"]) ? $context["web_agenda_ajax_url"] : null);
        echo "&a=delete_event&id='+calEvent.id;

\t\t\t\t\$(\"#dialog-form\").dialog({
\t\t\t\t\tbuttons: {
                        '";
        // line 341
        echo get_lang("ExportiCalConfidential");
        echo "' : function() {
                            url =  \"";
        // line 342
        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
        echo "calendar/ical_export.php?id=\" + calEvent.id+'&course_id='+calEvent.course_id+\"&class=confidential\";
                            window.location.href = url;
\t\t\t\t\t\t},
\t\t\t\t\t\t'";
        // line 345
        echo get_lang("ExportiCalPrivate");
        echo "': function() {
                            url =  \"";
        // line 346
        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
        echo "calendar/ical_export.php?id=\" + calEvent.id+'&course_id='+calEvent.course_id+\"&class=private\";
                            window.location.href = url;
\t\t\t\t\t\t},
                        '";
        // line 349
        echo get_lang("ExportiCalPublic");
        echo "': function() {
                            url =  \"";
        // line 350
        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
        echo "calendar/ical_export.php?id=\" + calEvent.id+'&course_id='+calEvent.course_id+\"&class=public\";
                            window.location.href = url;
\t\t\t\t\t\t},
                        ";
        // line 353
        if (((isset($context["type"]) ? $context["type"] : null) == "not_available")) {
            // line 354
            echo "\t\t\t\t\t\t'";
            echo get_lang("Edit");
            echo "' : function() {
\t\t\t\t\t\t\tvar bValid = true;
\t\t\t\t\t\t\tbValid = bValid && checkLength( title, \"title\", 1, 255 );

\t\t\t\t\t\t\tvar params = \$(\"#add_event_form\").serialize();
\t\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\t\turl: url+'&'+params,
\t\t\t\t\t\t\t\tsuccess:function() {
\t\t\t\t\t\t\t\t\tcalEvent.title = \$(\"#title\").val();
\t\t\t\t\t\t\t\t\tcalEvent.start = calEvent.start;
\t\t\t\t\t\t\t\t\tcalEvent.end = calEvent.end;
\t\t\t\t\t\t\t\t\tcalEvent.allDay = calEvent.allDay;
\t\t\t\t\t\t\t\t\tcalEvent.description = \$(\"#content\").val();
\t\t\t\t\t\t\t\t\tcalendar.fullCalendar('updateEvent',
                                        calEvent,
                                        true // make the event \"stick\"
\t\t\t\t\t\t\t\t\t);
\t\t\t\t\t\t\t\t\t\$(\"#dialog-form\").dialog(\"close\");
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t});
\t\t\t\t\t\t},
                        ";
        }
        // line 376
        echo "
                        '";
        // line 377
        echo get_lang("Edit");
        echo "' : function() {
                            url =  \"";
        // line 378
        echo $this->getAttribute((isset($context["_p"]) ? $context["_p"] : null), "web_main", array());
        echo "calendar/agenda.php?action=edit&type=fromjs&id=\" + calEvent.id+'&course_id='+calEvent.course_id+\"\";
                            window.location.href = url;
                            \$(\"#dialog-form\").dialog( \"close\" );
                        },
\t\t\t\t\t\t'";
        // line 382
        echo get_lang("Delete");
        echo "': function() {

                            if (calEvent.parent_event_id || calEvent.has_children != '') {
                                var newDiv = \$('<div>');
                                newDiv.dialog({
                                    modal: true,
                                    title: \"";
        // line 388
        echo get_lang("DeleteThisItem");
        echo "\",
                                    buttons: []
                                });

                                var buttons = newDiv.dialog(\"option\", \"buttons\");

                                if (calEvent.has_children == '0') {
                                    buttons.push({
                                        text: '";
        // line 396
        echo get_lang("DeleteThisItem");
        echo "',
                                        click: function() {
                                            \$.ajax({
                                                url: delete_url,
                                                success:function() {
                                                    calendar.fullCalendar('removeEvents',
                                                        calEvent
                                                    );
                                                    calendar.fullCalendar(\"refetchEvents\");
                                                    calendar.fullCalendar(\"rerenderEvents\");
                                                    \$(\"#dialog-form\").dialog(\"close\");
                                                    newDiv.dialog( \"destroy\" );
                                                }
                                            });
                                        }
                                    });
                                    newDiv.dialog(\"option\", \"buttons\", buttons);
                                }

                                var buttons = newDiv.dialog(\"option\", \"buttons\");
                                buttons.push({
                                    text: '";
        // line 417
        echo get_lang("DeleteAllItems");
        echo "',
                                    click: function() {
                                        \$.ajax({
                                            url: delete_url+'&delete_all_events=1',
                                            success:function() {
                                                calendar.fullCalendar('removeEvents',
                                                    calEvent
                                                );
                                                calendar.fullCalendar(\"refetchEvents\");
                                                calendar.fullCalendar(\"rerenderEvents\");
                                                \$(\"#dialog-form\").dialog( \"close\" );
                                                newDiv.dialog( \"destroy\" );
                                            }
                                        });
                                    }
                                });
                                newDiv.dialog(\"option\", \"buttons\", buttons);

                                return true;
                            }

\t\t\t\t\t\t\t\$.ajax({
\t\t\t\t\t\t\t\turl: delete_url,
\t\t\t\t\t\t\t\tsuccess:function() {
\t\t\t\t\t\t\t\t\tcalendar.fullCalendar('removeEvents',
\t\t\t\t\t\t\t\t\t\tcalEvent
\t\t\t\t\t\t\t\t\t);
\t\t\t\t\t\t\t\t\tcalendar.fullCalendar(\"refetchEvents\");
\t\t\t\t\t\t\t\t\tcalendar.fullCalendar(\"rerenderEvents\");
\t\t\t\t\t\t\t\t\t\$(\"#dialog-form\").dialog( \"close\" );
\t\t\t\t\t\t\t\t}
\t\t\t\t\t\t\t});
\t\t\t\t\t\t}
\t\t\t\t\t},
\t\t\t\t\tclose: function() {
                        \$(\"#title_edit\").hide();
                        \$(\"#content_edit\").hide();
                        \$(\"#comment_edit\").hide();

                        \$(\"#title\").show();
                        \$(\"#content\").show();
                        \$(\"#comment\").show();

\t\t\t\t\t\t\$(\"#title_edit\").html('');
\t\t\t\t\t\t\$(\"#content_edit\").html('');
                        \$(\"#comment_edit\").html('');

                        \$(\"#title\").val('');
                        \$(\"#content\").val('');
                        \$(\"#comment\").val('');
\t\t\t\t\t}
\t\t\t\t});
\t\t\t} else {
\t\t\t    // Simple form
                \$('#simple_start_date').html(calEvent.start.format(\"YY-MM-DD\"));

                if (end_date != '') {
                    \$('#simple_start_date').html(calEvent.start.format(\"YY-MM-DD\"));
                    \$('#simple_end_date').html(' ' + calEvent.end.format(\"YY-MM-DD\"));
                }
                if (calEvent.course_name) {
                    \$(\"#calendar_course_info_simple\").html(
                        '<div class=\"form-group\"><label class=\"col-sm-3 control-label\">";
        // line 479
        echo get_lang("Course");
        echo "</label>' +
                        '<div class=\"col-sm-9\">' + calEvent.course_name+\"</div></div>\"
                    );
                } else {
                    \$(\"#calendar_course_info_simple\").html('');
                }

                if (calEvent.session_name) {
                    \$(\"#calendar_session_info\").html(
                        '<div class=\"form-group\"><label class=\"col-sm-3 control-label\">";
        // line 488
        echo get_lang("Session");
        echo "</label>' +
                        '<div class=\"col-sm-9\">' + calEvent.session_name+\"</div></div>\"
                    );

                } else {
                    \$(\"#calendar_session_info\").html('');
                }

                \$(\"#simple_title\").html(calEvent.title);
                \$(\"#simple_content\").html(calEvent.description);
                \$(\"#simple_comment\").html(calEvent.comment);

                \$(\"#simple-dialog-form\").dialog(\"open\");
                \$(\"#simple-dialog-form\").dialog({
\t\t\t\t\tbuttons: {
\t\t\t\t\t\t'";
        // line 503
        echo get_lang("ExportiCalConfidential");
        echo "' : function() {
                            url =  \"ical_export.php?id=\" + calEvent.id+'&course_id='+calEvent.course_id+\"&class=confidential\";
                            window.location.href = url;
\t\t\t\t\t\t},
\t\t\t\t\t\t'";
        // line 507
        echo get_lang("ExportiCalPrivate");
        echo "': function() {
                            url =  \"ical_export.php?id=\" + calEvent.id+'&course_id='+calEvent.course_id+\"&class=private\";
                            window.location.href = url;
\t\t\t\t\t\t},
                        '";
        // line 511
        echo get_lang("ExportiCalPublic");
        echo "': function() {
                            url =  \"ical_export.php?id=\" + calEvent.id+'&course_id='+calEvent.course_id+\"&class=public\";
                            window.location.href = url;
\t\t\t\t\t\t}
\t\t\t\t\t}
\t\t\t\t});
            }
\t\t},
\t\teditable: true,
\t\tevents: \"";
        // line 520
        echo (isset($context["web_agenda_ajax_url"]) ? $context["web_agenda_ajax_url"] : null);
        echo "&a=get_events\",
\t\teventDrop: function(event, delta, revert_func) {
\t\t\t\$.ajax({
\t\t\t\turl: '";
        // line 523
        echo (isset($context["web_agenda_ajax_url"]) ? $context["web_agenda_ajax_url"] : null);
        echo "',
\t\t\t\tdata: {
                    a: 'move_event',
                    id: event.id,
                    day_delta: delta.days(),
                    minute_delta: delta.minutes()
\t\t\t\t}
\t\t\t});
\t\t},
        eventResize: function(event, delta, revert_func) {
            \$.ajax({
\t\t\t\turl: '";
        // line 534
        echo (isset($context["web_agenda_ajax_url"]) ? $context["web_agenda_ajax_url"] : null);
        echo "',
\t\t\t\tdata: {
                    a: 'resize_event',
                    id: event.id,
                    day_delta: delta.days(),
                    minute_delta: delta.minutes()
\t\t\t\t}
\t\t\t});
        },
\t\taxisFormat: 'H(:mm)', // pm-am format -> h(:mm)a
\t\ttimeFormat: 'H:mm',   // pm-am format -> h:mm
\t\tloading: function(bool) {
\t\t\tif (bool) \$('#loading').show();
\t\t\telse \$('#loading').hide();
\t\t}
\t});
});
</script>
";
        // line 552
        echo (isset($context["actions_div"]) ? $context["actions_div"] : null);
        echo "
";
        // line 553
        echo (isset($context["toolbar"]) ? $context["toolbar"] : null);
        echo "

<div id=\"simple-dialog-form\" style=\"display:none;\">
    <div style=\"width:500px\">
        <form name=\"form-simple\" class=\"form-horizontal\">
            <span id=\"calendar_course_info_simple\"></span>
            <span id=\"calendar_session_info\"></span>
            <div class=\"form-group\">
                <label class=\"col-sm-3 control-label\">
                    <b>";
        // line 562
        echo get_lang("Date");
        echo "</b>
                </label>
                <div class=\"col-sm-9\">
                    <span id=\"simple_start_date\"></span>
                    <span id=\"simple_end_date\"></span>
                </div>
            </div>
            <div class=\"form-group\">
                <label class=\"col-sm-3 control-label\">
                    <b>";
        // line 571
        echo get_lang("Title");
        echo "</b>
                </label>
                <div class=\"col-sm-9\">
                    <div id=\"simple_title\"></div>
                </div>
            </div>
            <div class=\"form-group\">
                <label class=\"col-sm-3 control-label\">
                    <b>";
        // line 579
        echo get_lang("Description");
        echo "</b>
                </label>
                <div class=\"col-sm-9\">
                    <div id=\"simple_content\"></div>
                </div>
            </div>
            <div class=\"form-group\">
                <label class=\"col-sm-3 control-label\">
                    <b>";
        // line 587
        echo get_lang("Comment");
        echo "</b>
                </label>
                <div class=\"col-sm-9\">
                    <div id=\"simple_comment\"></div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id=\"dialog-form\" style=\"display:none;\">
\t<div class=\"dialog-form-content\">
        ";
        // line 599
        echo (isset($context["form_add"]) ? $context["form_add"] : null);
        echo "
\t</div>
</div>
<div id=\"loading\" style=\"margin-left:150px;position:absolute;display:none\">
    ";
        // line 603
        echo get_lang("Loading");
        echo "...
</div>
<div id=\"calendar\"></div>
";
    }

    public function getTemplateName()
    {
        return "default/agenda/month.tpl";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  781 => 603,  774 => 599,  759 => 587,  748 => 579,  737 => 571,  725 => 562,  713 => 553,  709 => 552,  688 => 534,  674 => 523,  668 => 520,  656 => 511,  649 => 507,  642 => 503,  624 => 488,  612 => 479,  547 => 417,  523 => 396,  512 => 388,  503 => 382,  496 => 378,  492 => 377,  489 => 376,  463 => 354,  461 => 353,  455 => 350,  451 => 349,  445 => 346,  441 => 345,  435 => 342,  431 => 341,  424 => 337,  420 => 336,  396 => 315,  384 => 306,  349 => 274,  346 => 273,  341 => 270,  339 => 269,  220 => 153,  204 => 140,  198 => 137,  184 => 126,  180 => 125,  176 => 124,  172 => 123,  133 => 87,  129 => 86,  125 => 85,  121 => 84,  117 => 83,  113 => 82,  110 => 81,  101 => 75,  97 => 73,  95 => 72,  41 => 21,  19 => 1,);
    }

    public function getSource()
    {
        return "";
    }
}
