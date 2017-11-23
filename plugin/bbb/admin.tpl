<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th>{{ 'CreatedAt'|get_lang }}</th>
            <th>{{ 'Status'|get_lang }}</th>
            <th>{{ 'Records'|get_lang }}</th>
            <th>{{ 'Course'|get_lang }}</th>
            <th>{{ 'Session'|get_lang }}</th>
            <th>{{ 'Participants'|get_lang }}</th>
            <th>{{ 'Actions'|get_lang }}</th>
        </tr>
    </thead>
        <tbody>
        {% for meeting in meetings %}
            <tr id="meeting-{{ meeting.id }}">
                {% if meeting.visibility == 0 %}
                    <td class="muted">{{ meeting.created_at }}</td>
                {% else %}
                    <td>{{ meeting.created_at }}</td>
                {% endif %}
                <td>
                    {% if meeting.status == 1 %}
                        <span class="label label-success">{{ 'MeetingOpened'|get_lang }}</span>
                    {% else %}
                        <span class="label label-info">{{ 'MeetingClosed'|get_lang }}</span>
                    {% endif %}
                </td>
                <td>
                    {% if meeting.record == 1 %}
                        {# Record list #}
                        {{ meeting.show_links }}
                    {% endif %}
                </td>
                <td>{{ meeting.course ?: '-' }}</td>
                <td>{{ meeting.session ?: '-' }}</td>
                <td>
                    {{ meeting.participants ? meeting.participants|join('<br>') : '-' }}
                </td>
                <td>
                    {{ meeting.action_links }}
                </td>
            </tr>
        {% endfor %}
    </tbody>
</table>
