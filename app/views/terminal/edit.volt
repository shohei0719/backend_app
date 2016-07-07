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
$(function(){
  //画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
  $('form').on('change', 'input[type="file"]', function(e) {
    var file = e.target.files[0],
        reader = new FileReader(),
        $preview = $(".preview");
        t = this;

    // 画像ファイル以外の場合は何もしない
    if(file.type.indexOf("image") < 0){
      return false;
    }

    // ファイル読み込みが完了した際のイベント登録
    reader.onload = (function(file) {
      return function(e) {
        //既存のプレビューを削除
        $preview.empty();
        // .prevewの領域の中にロードした画像を表示するimageタグを追加
        $preview.append($('<img>').attr({
                  src: e.target.result,
                  width: "150px",
                  class: "preview",
                  title: file.name
              }));
      };
    })(file);

    reader.readAsDataURL(file);
  });
});
</script>


<h1>バージョン編集</h1>
<hr>

<div class="row">
	<p>
		<span class="pull-right" style="margin-right:14px;">
			登録者&nbsp;:&nbsp;{{ created_admin.name }}&nbsp;&nbsp;&nbsp;登録日&nbsp;:&nbsp;{{ terminal.created_at }}<br/>
			更新者&nbsp;:&nbsp;{{ updated_admin.name }}&nbsp;&nbsp;&nbsp;更新日&nbsp;:&nbsp;{{ terminal.updated_at }}<br/>
		</span>
	</p>
</div>

{{ form('backend_app/terminal/edit?id=' ~ terminal.id ~ '/', 'method': 'post', 'enctype': 'multipart/form-data') }}

	<div class="form-group">
		<label for="id"><i class="fa fa-ban" aria-hidden="true"></i>&nbsp;ID</label>
		{{ text_field("id", "class": "form-control input-sm", "value": terminal.id, "disabled": "disabled") }}
	</div>

  {# ---- キャリア ---- #}
	<div class="form-group">
		<label for="carrier"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;キャリア名&nbsp;<span class="required-mark">必須</span></label>

		<select name="carrier" class="form-control input-sm">
			{% for carrier in carriers %}
				<option value="{{ carrier.id }}" {% if terminal.carrier == carrier.id %}selected{% endif %}>{{ carrier.name }}</option>
			{% endfor %}
		</select>
	</div>

	{# ---- メーカー ---- #}
	<div class="form-group">
		<label for="maker"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;メーカー名&nbsp;<span class="required-mark">必須</span></label>

		<select name="maker" class="form-control input-sm">
			{% for maker in makers %}
				<option value="{{ maker.id }}" {% if terminal.maker == maker.id %}selected{% endif %}>{{ maker.name }}</option>
			{% endfor %}
		</select>
	</div>

	{# ---- OS ---- #}
	<div class="form-group">
		<label for="os"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;OS名&nbsp;<span class="required-mark">必須</span></label>

		<select name="os" class="form-control input-sm">
			{% for os in oss %}
				<option value="{{ os.id }}" {% if terminal.os == os.id %}selected{% endif %}>{{ os.name }}</option>
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
				<option value="{{ version.id }}" {% if terminal.version == version.id %}selected{% endif %}>{{ version.name }}</option>
			{% endfor %}
		</select>
	</div>

 	{# ---- 端末名 ---- #}
	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;端末名&nbsp;<span class="required-mark">必須</span></label>
		{{ text_field("name", "class": "form-control input-sm", "maxlength": 50, "value": terminal.name) }}

		{% if not (errorMsg['name'] is empty) %}
		  {{MyTags.errorMsg(errorMsg['name'])}}
	  {% endif %}
	</div>

	{# ---- TEL ---- #}
	<div class="form-group">
		<label for="tel"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;TEL</label>
		{{ text_field("tel", "class": "form-control input-sm", "maxlength": 11, "value": terminal.tel) }}

		{% if not (errorMsg['tel'] is empty) %}
		  {{MyTags.errorMsg(errorMsg['tel'])}}
	  {% endif %}
	</div>

	{# ---- Mail ---- #}
	<div class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;メールアドレス</label>
		{{ text_field("mail", "class": "form-control input-sm", "maxlength": 100, "value": terminal.mail) }}

		{% if not (errorMsg['mail'] is empty) %}
			{{MyTags.errorMsg(errorMsg['mail'])}}
		{% endif %}
	</div>

	{# ---- イメージ ---- #}
	<div id="thumb" class="form-group">
		<label for="name"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;イメージ</label><br/>
      <input type="file" name="image">
      {#<div style="display: inline-block;"><input type="file" name="image" multiple="multiple" />&nbsp;<a id="button" href=""><i class="fa fa-trash-o fa-lg"></i></a></div>#}
      <div class="preview" /></div>
    </label>
  </div>

	{# ---- 組織 ---- #}
	<div class="form-group">
		<label for="organization"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;組織名&nbsp;<span class="required-mark">必須</span></label>

		<select name="organization" class="form-control input-sm">
			{% for organization in organizations %}
				<option value="{{ organization.id }}" {% if terminal.organization == organization.id %}selected{% endif %}>{{ organization.name }}</option>
			{% endfor %}
		</select>
	</div>

	{# ---- コメント ---- #}
	<div class="form-group">
		<label for="comment"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;コメント</label>

    {{text_area("comment", "class": "form-control input-sm", "rows": 3, "value": terminal.comment)}}
  </div>

	<input type="hidden" name="status" value="1">

	{{ submit_button('編集', "class": "btn btn-primary btn-lg btn-block") }}

{{ end_form() }}
