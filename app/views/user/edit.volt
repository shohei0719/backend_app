<style>
form img.thumb {
    margin:0 5px 5px 0;
    max-width:160px;
    vertical-align:bottom;
}
</style>
<script>
function img_reset(){
    //value初期化
	document.getElementById('hidden_image').value = '';
	//image非表示
	var dom_obj=document.getElementById('preview_image');
    var dom_obj_parent=dom_obj.parentNode;
    dom_obj_parent.removeChild(dom_obj);
}
</script>


<h1>ユーザ編集</h1>

<div>
<span class="pull-right title-link-position">
	{{ link_to('/backend_app/user/change?id=' ~ user.id ~ '/', '➤ パスワードの変更はこちら') }}
</span>
</div>

<hr>

<div class="row">
	<p>
		<span class="pull-right" style="margin-right:14px;">
			登録者&nbsp;:&nbsp;{{ created_user.name }}&nbsp;&nbsp;&nbsp;登録日&nbsp;:&nbsp;{{ created_user.created_at }}<br/>
			更新者&nbsp;:&nbsp;{{ updated_user.name }}&nbsp;&nbsp;&nbsp;更新日&nbsp;:&nbsp;{{ updated_user.updated_at }}<br/>
		</span>
	</p>
</div>

{{ form('backend_app/user/edit?id=' ~ user.id ~ '/', 'method': 'post', 'enctype': 'multipart/form-data') }}

	<div class="form-group">
		<label for="id"><i class="fa fa-ban" aria-hidden="true"></i>&nbsp;ID</label>
		{{ text_field("id", "class": "form-control input-sm", "value": user.id, "disabled": "disabled") }}
	</div>

	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;氏名&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("name", "class": "form-control input-sm", "maxlength": 20, "value": user.name) }}
		{% if errorMsg['name'] is not empty %}

			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">×</a>
				{{ errorMsg['name'] }}
			</div>

		{% endif %}
	</div>

	<div class="form-group">
		<label for="mail"><i class="fa fa-envelope-o fa-fw"></i>&nbsp;メールアドレス&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("mail", "class": "form-control input-sm", "maxlength": 255, "value": user.mail) }}
		{% if errorMsg['mail'] is not empty %}

			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">×</a>
				{{ errorMsg['mail'] }}
			</div>

		{% endif %}
	</div>

	<input type="hidden" name="status" value="1">

	{{ submit_button('編集', "class": "btn btn-primary btn-lg btn-block") }}
{{ end_form() }}
