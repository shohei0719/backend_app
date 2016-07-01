<h1>メーカー作成</h1>
<hr>

{{ form('backend_app/maker/new/', 'method': 'post', 'enctype': 'multipart/form-data') }}
	
	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;メーカー名&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("name", "class": "form-control input-sm", "maxlength": 50, "value": name) }}
		{% if errorMsg['name'] is not empty %}
			
			<div class="alert alert-danger">
				{{ errorMsg['name'] }}
			</div>
			
		{% endif %}	
	</div>
	
	{{ submit_button('新規作成', "class": "btn btn-primary btn-lg btn-block") }}
{{ end_form() }}
