<h1>管理者一覧</h1>
<hr/>

<div class="panel panel-default">
	<div class="panel-heading">
	<h4 class="panel-title">
		<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="">■ 検索バー</a>
	</h4>
	</div>
	<div id="collapseThree" class="panel-collapse collapse" style="height: auto;">
		<div class="panel-body">
			{{ form('/backend_app/admin/', 'method': 'get', 'enctype': 'multipart/form-data') }}
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="active">名前</th>
							<td>{{ text_field("name", "class": "form-control", "maxlength": 20, "value": name) }}</td>
						</tr>
						<tr>	
							<th class="active">権限</th>
							<td>{{ select_static("permission", "class": "form-control", ['全権限', '管理者情報以外', '管理者情報 / ユーザ情報以外']) }}</td>
						</tr>
						<tr>
							<th class="active">メールアドレス</th>
							<td>{{ text_field("mail", "class": "form-control", "maxlength": 255, "value": mail) }}</td>
						</tr>
					</thead>
				</table>
				
				{{ submit_button('検索', "class": "btn btn-success btn-xs btn-block") }}
				
			{{ end_form() }}
		</div>
	</div>
</div>

<hr/>

{# {% if page.total_pages != 1 %} #} 
	{{ link_to('', '最初') }}
	{{ link_to('?page=' ~ page.before, '前へ') }}
	{{ link_to('#', page.current ~ '/' ~ page.total_pages) }}
	{{ link_to('?page=' ~ page.next, '次へ') }}
	{{ link_to('?page=' ~ page.last, '最後') }}
{# {% endif %} #}


<span class="pull-right" style="margin-right:14px;">
	{{ link_to('/backend_app/admin/new/', '➤ 新規登録はこちら') }}
</span>


<br/>
<table class="table table-bordered table-striped table-responsive">
	
	<thead>
      	<tr>
        	<th>#</th>
			<th>名前</th>
			<th>メールアドレス</th>　
			<th>権限</th>
			<th>#</th>
			<th>#</th>
      	</tr>
    </thead>
	
	<tbody>
		{% for admins in page %}
			{% for admin in admins %}
				{% if admin is not empty %}
				
					<tr>
						<td>{{ admin.id }}</td>
						<td>{{ admin.name }}</td>
						<td>{{ admin.mail }}</td>
						
						{% if admin.permission == 0 %}
							{% set permission_name = '全権限' %}
						{% elseif admin.permission == 1 %}
							{% set permission_name = '管理者情報以外' %}
						{% elseif admin.permission == 2 %}
							{% set permission_name = '管理者情報/ユーザ情報以外' %}
						{% endif %}
						
						<td>{{ permission_name }}</td>
						
						<td>
							{{ form('backend_app/admin/edit?id=' ~ admin.id, 'method': 'post') }}
								<input type="submit" class="btn btn-primary btn-xs btn-block" value="編集">
								<input type="hidden" name="status" value="0">
							{{ end_form() }}
						</td>
						<td>
							{{ form('backend_app/admin/delete?id=' ~ admin.id, 'method': 'post', 'onSubmit': 'return delete_check()') }}
								<input type="submit" class="btn btn-danger btn-xs btn-block" value="削除">
							{{ end_form() }}
						</td>
						
					</tr>			
					
				{% endif %}
			{% endfor %}
			{% break %}
		{% endfor %}
	</tbody>
	
</table>

{% if page.total_pages != 1 %} 
	{{ link_to('', '最初') }}
	{{ link_to('?page=' ~ page.before, '前へ') }}
	{{ link_to('#', page.current ~ '/' ~ page.total_pages) }}
	{{ link_to('?page=' ~ page.next, '次へ') }}
	{{ link_to('?page=' ~ page.last, '最後') }}
{% endif %}