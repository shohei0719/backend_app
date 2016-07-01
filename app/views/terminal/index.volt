<h1>端末一覧</h1>
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
			{{ form('/backend_app/terminal/', 'method': 'get', 'enctype': 'multipart/form-data') }}

				<div class="form-group">
					<label for="carrier"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;キャリア名&nbsp;</label>
					<select name="carrier" class="form-control input-sm">
						<option value="0">--</option>
						{% for carrier in carriers %}
							<option value="{{ carrier.id }}" {% if carrier == carrier.id %}selected{% endif %}>{{ carrier.name }}</option>
						{% endfor %}
				　</select>
				</div>

				<div class="form-group">
					<label for="maker"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;メーカー名&nbsp;</label>
					<select name="maker" class="form-control input-sm">
						<option value="0">--</option>
						{% for maker in makers %}
							<option value="{{ maker.id }}" {% if maker == maker.id %}selected{% endif %}>{{ maker.name }}</option>
						{% endfor %}
				　</select>
				</div>

				<div class="form-group">
					<label for="os"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;OS名&nbsp;</label>
					<select name="os" class="form-control input-sm">
						<option value="0">--</option>
						{% for os in oss %}
							<option value="{{ os.id }}" {% if os == os.id %}selected{% endif %}>{{ os.name }}</option>
						{% endfor %}
				　</select>
				</div>

				<div class="form-group">
					<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;バージョン名</label>
					{{ text_field("name", "class": "form-control input-sm", "value": name) }}
				</div>

				<div class="form-group">
					<label for="organization"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;組織名&nbsp;</label>
					<select name="organization" class="form-control input-sm">
						<option value="0">--</option>
						{% for organization in organizations %}
							<option value="{{ organization.id }}" {% if organization == organization.id %}selected{% endif %}>{{ organization.name }}</option>
						{% endfor %}
				　</select>
				</div>

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
	<a href="/backend_app/terminal/new/"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;新規登録はこちら</a>
</span>

{# キャリア一覧表示_start #}
{% set cnt = 0 %}
{% for terminals in page %}
	{% for terminal in terminals %}
		{% if terminal is not empty %}
			{% if cnt == 0 %}
				<table class="table table-bordered table-striped table-responsive">
					<thead>
						<tr>
							<th>#</th>
							<th>端末名</th>
							<th>OS</th>
							<th>バージョン</th>
							<th>編集</th>
							<th>削除</th>
						</tr>
					</thead>

					<tbody>
			{% endif %}
					<tr>
						<td>{{ terminal.id }}</td>
						<td>{{ terminal.name }}</td>
						<td>{{ terminal.os_name }}</td>
						<td>{{ terminal.version_name }}</td>

						<td class="col-md-1">
							{{ form('backend_app/terminal/edit?id=' ~ terminal.id ~ '/', 'method': 'post') }}
								<button type="submit" class="btn btn-primary btn-xs btn-block">
									<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								</button>
								{# <input type="submit" class="btn btn-primary btn-xs btn-block" value="編集"> #}
								<input type="hidden" name="status" value="0">
							{{ end_form() }}
						</td>
						<td class="col-md-1">
							{{ form('backend_app/terminal/delete?id=' ~ terminal.id ~ '/', 'method': 'post', 'onSubmit': 'return delete_check()') }}
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
{# キャリア一覧表示_end #}

{{ partial("partials/paginator") }}

{# ページャ呼び出し_start #}
{% set get_param = '' %}
{% if not (name is empty) or not (mail is empty) or not (permission is empty) %}
	{% set get_param = '&name=' ~ name ~ '&permission=' ~ permission ~ '&mail=' ~ mail %}
{% endif %}
{{ pager('page': page, 'get_param': get_param) }}
{# ページャ呼び出し_end #}
