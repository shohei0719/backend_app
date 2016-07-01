<h1>バージョン作成</h1>
<hr>

{{ form('backend_app/version/new/', 'method': 'post', 'enctype': 'multipart/form-data') }}
	
	<div class="form-group">
		<label for="related_os"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;OS名&nbsp;<span class="required-mark">必須</span></label>
		
		<select name="related_os" class="form-control input-sm">
			{% for os in oss %}
				<option value="{{ os.id }}" {% if related_os == os.id %}selected{% endif %}>{{ os.name }}</option>
			{% endfor %}
		</select>

		{% if errorMsg['related_os'] is not empty %}
			
			<div class="alert alert-danger">
				{{ errorMsg['related_os'] }}
			</div>
			
		{% endif %}	
	</div>

	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;バージョン名&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("name", "class": "form-control input-sm", "maxlength": 50, "value": name) }}
		{% if errorMsg['name'] is not empty %}
			
			<div class="alert alert-danger">
				{{ errorMsg['name'] }}
			</div>
			
		{% endif %}	
	</div>
	
	{{ submit_button('新規作成', "class": "btn btn-primary btn-lg btn-block") }}
{{ end_form() }}
