<h1>管理者一覧</h1>
<hr/>

{# 検索バー_start #}
<div class="panel panel-default">
	<div class="panel-heading">
	<h4 class="panel-title">
		<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="">
			<i class="fa fa-search" aria-hidden="true"></i>&nbsp;検索
		</a>
	</h4>
	</div>
	<div id="collapseThree" class="panel-collapse collapse" style="height: auto;">
		<div class="panel-body">
			{{ form('/backend_app/admin/', 'method': 'get', 'enctype': 'multipart/form-data') }}
				
				<div class="form-group">
					<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;名前</label>
					{{ text_field("name", "class": "form-control input-sm") }}
				</div>
				
				<div class="form-group">
					<label for="permission"><i class="fa fa-wrench" aria-hidden="true"></i>&nbsp;権限</label>
					<select name="permission" class="form-control input-sm">
						<option value="0">--</option>
						<option value="1" {% if permission == 1 %}selected{% endif %}>全権限</option>
						<option value="2" {% if permission == 2 %}selected{% endif %}>管理者情報以外</option>
						<option value="3" {% if permission == 3 %}selected{% endif %}>管理者情報 / ユーザ情報以外</option>
					</select>
				</div>
				
				<div class="form-group">
					<label for="mail"><i class="fa fa-envelope-o fa-fw"></i>&nbsp;メールアドレス</label>
					{{ text_field("mail", "class": "form-control input-sm") }}
				</div>
				
				{# {{ submit_button('検索', "class": "btn btn-success btn-xs btn-block") }} #}
				
				<button type="submit" class="btn btn-success btn-xs btn-block">
					<i class="fa fa-search" aria-hidden="true"></i>&nbsp;Search
				</button>
				
			{{ end_form() }}
		</div>
	</div>
</div>
{# 検索バー_end #}

<hr/>

<span class="pull-right" style="margin-right:14px;margin-bottom:14px;">
	<a href="/backend_app/admin/new/"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;新規登録はこちら</a>
</span>

{# 管理者情報一覧表示_start #}
{% set cnt = 0 %}
{% for admins in page %}
	{% for admin in admins %}
		{% if admin is not empty %}
			{% if cnt == 0 %}
				<table class="table table-bordered table-striped table-responsive">
					<thead>
						<tr>
							<th>#</th>
							<th>名前</th>
							<th>メールアドレス</th>　
							<th>権限</th>
							<th>編集</th>
							<th>削除</th>
						</tr>
					</thead>
				
					<tbody>
			{% endif %}
					<tr>
						<td>{{ admin.id }}</td>
						<td>{{ admin.name }}</td>
						<td style='word-break: break-all;'>{{ admin.mail }}</td>
						
						{% if admin.permission == 1 %}
							{% set permission_name = '全権限' %}
						{% elseif admin.permission == 2 %}
							{% set permission_name = '管理者情報以外' %}
						{% elseif admin.permission == 3 %}
							{% set permission_name = '管理者情報/ユーザ情報以外' %}
						{% endif %}
						
						<td>{{ permission_name }}</td>
						
						<td>
							{{ form('backend_app/admin/edit?id=' ~ admin.id ~ '/', 'method': 'post') }}
								<button type="submit" class="btn btn-primary btn-xs btn-block">
									<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								</button>
								{# <input type="submit" class="btn btn-primary btn-xs btn-block" value="編集"> #}
								<input type="hidden" name="status" value="0">
							{{ end_form() }}
						</td>
						<td>
							{{ form('backend_app/admin/delete?id=' ~ admin.id ~ '/', 'method': 'post', 'onSubmit': 'return delete_check()') }}
								<button type="submit" class="btn btn-danger btn-xs btn-block">
									<i class="fa fa-trash-o" aria-hidden="true"></i>
								</button>
								{# <input type="submit" class="btn btn-danger btn-xs btn-block" value="削除"> #}
							{{ end_form() }}
						</td>		
					</tr>			
				{% set cnt = cnt + 1 %}
		{% endif %}
	{% endfor %}
	{% break %}
{% endfor %}

{% if cnt > 0 %}
		</tbody>	
	</table>
{% else %}
Search result 0 ...
{% endif %}
{# 管理者情報一覧表示_end #}

{{ partial("partials/paginator") }}

{# ページャ呼び出し_start #}
{% set get_param = '' %}
{% if not (name is empty) or not (mail is empty) or not (permission is empty) %} 
	{% set get_param = '&name=' ~ name ~ '&permission=' ~ permission ~ '&mail=' ~ mail %}
{% endif %}
{{ pager('page': page, 'get_param': get_param) }}
{# ページャ呼び出し_end #}