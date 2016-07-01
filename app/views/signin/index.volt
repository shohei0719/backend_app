<hr>

<div class="well bs-component">
	{{ form('/backend_app/signin/', 'method': 'post') }}
		<fieldset>
		
			{% if errorMsg is not empty %}
				<div class="alert alert-danger">
					{# <a class="close" data-dismiss="alert">×</a> #}
					{{ errorMsg }}
				</div>
			{% endif %}
		
			<div class="form-group">
				<label for="mail"><i class="fa fa-envelope-o fa-fw"></i>&nbsp;メールアドレス</label>
				{{ email_field("mail", "class": "form-control", "placeholder": "example@tenda.co.jp") }}
			</div>
				
			<div class="form-group">
				<label for="password"><i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp;パスワード</label>
				{{ password_field("password", "class": "form-control", "maxlength": 20) }}
			</div>
				
			{# {{ submit_button('ログイン', "class": "btn btn-primary btn-lg btn-block") }} #}
			
			<button type="submit" class="btn btn-primary btn-lg btn-block">
				<i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp;ログイン
			</button>
			
		</fieldset>
	{{ end_form() }}
</div>
