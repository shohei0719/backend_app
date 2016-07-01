{# ページャマクロ #}
{%- macro pager(page, get_param) %}
{% if page.total_pages > 1 %}
	<ul class="pager">
		{# 最初のページの時に非表示 #}
		{% if page.current != 1 %}
			<li class="previous">{{ link_to('/backend_app/admin?page=1' ~ get_param ~ '/', '最初') }}</li>
			<li>{{ link_to('/backend_app/admin?page=' ~ page.before ~ get_param ~ '/', '前へ') }}</li>
		{% else %}
			<li class="previous">{{ link_to('#', '最初', 'class': 'btn', 'disabled': 'disabled') }}</li>
			<li>{{ link_to('#', '前へ', 'class': 'btn', 'disabled': 'disabled') }}</li>
		{% endif %}
		
		<li>{{ link_to('#', page.current ~ '/' ~ page.total_pages, 'class': 'btn', 'disabled': 'disabled') }}</li>
		
		{# 最後のページの時に非表示 #}
		{% if page.current != page.last %}
			<li>{{ link_to('/backend_app/admin?page=' ~ page.next ~ get_param ~ '/', '次へ') }}</li>
			<li class="next">{{ link_to('/backend_app/admin?page=' ~ page.last ~ get_param ~ '/', '最後') }}</li>
		{% else %}
			<li>{{ link_to('#', '次へ', 'class': 'btn', 'disabled': 'disabled') }}</li>
			<li class="next">{{ link_to('#', '最後', 'class': 'btn', 'disabled': 'disabled') }}</li>
		{% endif %}
	</ul>
{% endif %}
{%- endmacro %}