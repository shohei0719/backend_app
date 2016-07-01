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

<h1>編集画面</h1>
<hr>
{{ form('terminal/edit', 'method': 'post', 'enctype': 'multipart/form-data') }}
	
	<div class="form-group">
		<label for="name">氏名&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("name", "class": "form-control", "maxlength": 20, "value": name) }}
		{% if errorMsg['name'] is not empty %}
			
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert">×</a>
				{{ errorMsg['name'] }}
			</div>
			
		{% endif %}	
	</div>
	
	<div class="form-group">
		<label for="extension">内線番号</label>
		{{ text_field("extension", "class": "form-control", "maxlength": 10, "value": extension) }}
	</div>
	
	<div class="form-group">
		<label for="image">画像</label><br/>
		<img src="/terminal/public/img/users_img/{{image}}" id="preview_image" class="thumb">
		<input type="file" name="image" onclick="img_reset()">
		<input type="hidden" id="hidden_image" value="{{image}}">
    </ul>
	</div>
	
	{{ submit_button('編集', "class": "btn btn-primary btn-lg btn-block") }}
{{ end_form() }}
