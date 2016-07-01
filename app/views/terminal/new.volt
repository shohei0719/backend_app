<h1>端末作成</h1>
<hr>

{{ form('backend_app/terminal/new/', 'method': 'post', 'enctype': 'multipart/form-data') }}

	{# ---- キャリア ---- #}
	<div class="form-group">
		<label for="carrier"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;キャリア名&nbsp;<span class="required-mark">必須</span></label>

		<select name="carrier" class="form-control input-sm">
			{% for carrier in carriers %}
				<option value="{{ carrier.id }}" {% if carrier_id == carrier.id %}selected{% endif %}>{{ carrier.name }}</option>
			{% endfor %}
		</select>
	</div>

	{# ---- メーカー ---- #}
	<div class="form-group">
		<label for="maker"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;メーカー名&nbsp;<span class="required-mark">必須</span></label>

		<select name="maker" class="form-control input-sm">
			{% for maker in makers %}
				<option value="{{ maker.id }}" {% if maker_id == maker.id %}selected{% endif %}>{{ maker.name }}</option>
			{% endfor %}
		</select>
	</div>

	{# ---- OS ---- #}
	<div class="form-group">
		<label for="os"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;OS名&nbsp;<span class="required-mark">必須</span></label>

		<select name="os" class="form-control input-sm">
			{% for os in oss %}
				<option value="{{ os.id }}" {% if os_id == os.id %}selected{% endif %}>{{ os.name }}</option>
			{% endfor %}
		</select>

		{% if errorMsg['related_os'] is not empty %}

			<div class="alert alert-danger">
				{{ errorMsg['related_os'] }}
			</div>

		{% endif %}
	</div>

	{# ---- バージョン名 ---- #}
	<div class="form-group">
		<label for="version"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;バージョン名&nbsp;<span class="required-mark">必須</span></label>

		<select name="version" class="form-control input-sm">
			{% for version in versions %}
				<option value="{{ version.id }}" {% if version_id == version.id %}selected{% endif %}>{{ version.name }}</option>
			{% endfor %}
		</select>
	</div>

 	{# ---- 端末名 ---- #}
	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;端末名&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("name", "class": "form-control input-sm", "maxlength": 50, "value": name) }}

		{% if not (errorMsg['name'] is empty) %}
		  {{MyTags.errorMsg(errorMsg['name'])}}
	  {% endif %}
	</div>

	{# ---- TEL ---- #}
	<div class="form-group">
		<label for="tel"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;TEL</label>
		{{ text_field("tel", "class": "form-control input-sm", "maxlength": 11, "value": tel) }}

		{% if not (errorMsg['tel'] is empty) %}
		  {{MyTags.errorMsg(errorMsg['tel'])}}
	  {% endif %}
	</div>

	{# ---- Mail ---- #}
	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;メールアドレス</label>
		{{ text_field("mail", "class": "form-control input-sm", "maxlength": 100, "value": mail) }}

		{% if not (errorMsg['mail'] is empty) %}
			{{MyTags.errorMsg(errorMsg['mail'])}}
		{% endif %}
	</div>

	{# ---- イメージ ---- #}
	<div id="thumb" class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;イメージ</label><br/>
      <div style="display: inline-block;"><input type="file" name="images[]" multiple="multiple" />&nbsp;<a id="button" href="javascript:（0）"><i class="fa fa-trash-o fa-lg"></i></a></div>
    </label>
  </div>

	{# ---- 組織 ---- #}
	<div class="form-group">
		<label for="organization"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;組織名&nbsp;<span class="required-mark">必須</span></label>

		<select name="organization" class="form-control input-sm">
			{% for organization in organizations %}
				<option value="{{ organization.id }}" {% if organization_id == organization.id %}selected{% endif %}>{{ organization.name }}</option>
			{% endfor %}
		</select>
	</div>

	{# ---- コメント ---- #}
	<div class="form-group">
		<label for="comment"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;コメント</label>

    {{text_area("comment", "class": "form-control input-sm", "rows": 3, "value": comment)}}
  </div>

	{{ submit_button('新規作成', "class": "btn btn-primary btn-lg btn-block") }}
{{ end_form() }}
