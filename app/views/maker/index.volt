<h1>メーカー一覧</h1>
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
			{{ form('/backend_app/maker/', 'method': 'get', 'enctype': 'multipart/form-data') }}
				
				<div class="form-group">
					<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;メーカー名</label>
					{{ text_field("name", "class": "form-control input-sm") }}
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
	<a href="/backend_app/maker/new/"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;新規登録はこちら</a>
</span>

{# キャリア一覧表示_start #}
{% set cnt = 0 %}
{% for makers in page %}
	{% for maker in makers %}
		{% if maker is not empty %}
			{% if cnt == 0 %}
				<table class="table table-bordered table-striped table-responsive">
					<thead>
						<tr>
							<th>#</th>
							<th>メーカー名</th>
							<th>編集</th>
							<th>削除</th>
						</tr>
					</thead>
				
					<tbody>
			{% endif %}
					<tr>
						<td>{{ maker.id }}</td>
						<td>{{ maker.name }}</td>
						
						<td class="col-md-1">
							{{ form('backend_app/maker/edit?id=' ~ maker.id ~ '/', 'method': 'post') }}
								<button type="submit" class="btn btn-primary btn-xs btn-block">
									<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
								</button>
								{# <input type="submit" class="btn btn-primary btn-xs btn-block" value="編集"> #}
								<input type="hidden" name="status" value="0">
							{{ end_form() }}
						</td>
						<td class="col-md-1">
							{{ form('backend_app/maker/delete?id=' ~ maker.id ~ '/', 'method': 'post', 'onSubmit': 'return delete_check()') }}
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