<h1>管理者パスワード変更</h1>

<span class="pull-right title-link-position">
	{{ link_to('/backend_app/admin/edit?id=' ~ id, '➤ 管理者変更画面に戻る') }}
</span>

<hr>

{{ form('/backend_app/admin/change?id=' ~ id, 'method': 'post', 'enctype': 'multipart/form-data') }}
	
	<div class="form-group">
		<label for="password"><i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp;新しいパスワード&nbsp;<span class="required-mark">必須</span></label>
		{{ password_field("password", "class": "form-control input-sm", "maxlength": 20, "value": password) }}
		{% if errorMsg['password'] is not empty %}
			
			<div class="alert alert-danger">
				{{ errorMsg['password'] }}
			</div>
			
		{% endif %}	
	</div>
	
	<div class="form-group">
		<label for="re_password"><i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp;パスワード再入力&nbsp;<span class="required-mark">必須</span></label>
		{{ password_field("re_password", "class": "form-control input-sm", "maxlength": 20, "value": re_password) }}
		{% if errorMsg['re_password'] is not empty %}
			
			<div class="alert alert-danger">
				{{ errorMsg['re_password'] }}
			</div>
			
		{% endif %}	
	</div>
	
	{{ submit_button('再発行', "class": "btn btn-primary btn-lg btn-block") }}
{{ end_form() }}
