<h1>端末管理画面</h1>
<hr/>
<div class="row">
	
	
	{% for users in page %}
		{% for user in users %}
			{% if user is not empty %}
				<div class="col-md-3 col-sm-6">
					<div class="thumbnail">
						{# イメージ画像を設定 #}
						{% if user.image is not empty %}
							{% set image_path = "/terminal/public/img/users_img/" ~ user.image %}
						{% else %}
							{% set image_path = "/terminal/public/img/users_img/no_image.jpg" %}
						{% endif %}
						
						{# ユーザ情報を表示 #}
						<a class="1btn" href="/members/profile/541"><img src={{ image_path }} height="120" width="120" class="1thumbnail"></a>
						<div class="caption">
							<h4><a class="1btn" href="">{{ user.name }}</a>さん</h4>
							<p>
								メールアドレス&nbsp;:&nbsp;{{ user.email }}<br>
								内線番号&nbsp;:&nbsp;{{ user.extension }}<br>
								<span class="label label-primary">ソリュション事業部システム2部2課</span>
								<span class="label label-primary">Android</span>
								<span class="label label-primary">ver&nbsp;4.2</span>
							</p>
						</div>
					</div>
				</div>
			{% endif %}
		{% endfor %}
		{% break %}
	{% endfor %}
	
</div>
{% if page.total_pages != 1 %} 
	{{ link_to('', '最初') }}
	{{ link_to('?page=' ~ page.before, '前へ') }}
	{{ link_to('#', page.current ~ '/' ~ page.total_pages) }}
	{{ link_to('?page=' ~ page.next, '次へ') }}
	{{ link_to('?page=' ~ page.last, '最後') }}
{% endif %}