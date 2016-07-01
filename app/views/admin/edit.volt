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


<h1>管理者編集</h1>

<div>
<span class="pull-right title-link-position">
	{{ link_to('/backend_app/admin/change?id=' ~ admin.id ~ '/', '➤ パスワードの変更はこちら') }}
</span>
</div>

<hr>

<div class="row">
	<p>
		<span class="pull-right" style="margin-right:14px;">
			登録者&nbsp;:&nbsp;{{ created_admin.name }}&nbsp;&nbsp;&nbsp;登録日&nbsp;:&nbsp;{{ created_admin.created_at }}<br/>
			更新者&nbsp;:&nbsp;{{ updated_admin.name }}&nbsp;&nbsp;&nbsp;更新日&nbsp;:&nbsp;{{ updated_admin.updated_at }}<br/>
		</span>
	</p>
</div>

{{ form('backend_app/admin/edit?id=' ~ admin.id ~ '/', 'method': 'post', 'enctype': 'multipart/form-data') }}
	
	<div class="form-group">
		<label for="id"><i class="fa fa-ban" aria-hidden="true"></i>&nbsp;ID</label>
		{{ text_field("id", "class": "form-control input-sm", "value": admin.id, "disabled": "disabled") }}
	</div>
	
	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;氏名&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("name", "class": "form-control input-sm", "maxlength": 20, "value": admin.name) }}
		{% if errorMsg['name'] is not empty %}
			
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">×</a>
				{{ errorMsg['name'] }}
			</div>
			
		{% endif %}	
	</div>
	
	<div class="form-group">
		<label for="mail"><i class="fa fa-envelope-o fa-fw"></i>&nbsp;メールアドレス&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("mail", "class": "form-control input-sm", "maxlength": 255, "value": admin.mail) }}
		{% if errorMsg['mail'] is not empty %}
			
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">×</a>
				{{ errorMsg['mail'] }}
			</div>
			
		{% endif %}	
	</div>
	
	<div class="form-group">
		<label for="permisson"><i class="fa fa-wrench" aria-hidden="true"></i>&nbsp;権限&nbsp;<span class="required-mark">必須</span></label>
		
		<select name="permission" class="form-control input-sm">
			<option value="1" {% if admin.permission == 1 %}selected{% endif %}>全権限</option>
			<option value="2" {% if admin.permission == 2 %}selected{% endif %}>管理者情報以外</option>
			<option value="3" {% if admin.permission == 3 %}selected{% endif %}>管理者情報 / ユーザ情報以外</option>
		</select>
		
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
