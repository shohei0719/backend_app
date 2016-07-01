<h1>ユーザ作成</h1>
<hr>

{{ form('backend_app/user/new/', 'method': 'post', 'enctype': 'multipart/form-data') }}

	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;氏名&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("name", "class": "form-control input-sm", "maxlength": 20, "value": name) }}
		{% if errorMsg['name'] is not empty %}

			<div class="alert alert-danger">
				{{ errorMsg['name'] }}
			</div>

		{% endif %}
	</div>

	<div class="form-group">
		<label for="mail"><i class="fa fa-envelope-o fa-fw"></i>&nbsp;メールアドレス&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("mail", "class": "form-control input-sm", "maxlength": 255, "value": mail) }}
		{% if errorMsg['mail'] is not empty %}

			<div class="alert alert-danger">
				{{ errorMsg['mail'] }}
			</div>

		{% endif %}
	</div>

	<div class="form-group">
		<label for="password"><i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp;パスワード&nbsp;<span class="required-mark">必須</span></label>
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

	{{ submit_button('新規作成', "class": "btn btn-primary btn-lg btn-block") }}
{{ end_form() }}
