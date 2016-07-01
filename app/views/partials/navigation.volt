<nav class="navbar navbar-inverse navbar-static-top">
      <div class="container">
          <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#gnavi">
                  <span class="sr-only">メニュー</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a href="/backend_app/" class="navbar-brand">端末管理システム&nbsp;-&nbsp;CMS</a>
          </div>

          <div id="gnavi" class="collapse navbar-collapse">
              {% if auth_name is not empty %}

                  <ul class="nav navbar-nav">
                      <?php $uri = $_SERVER['REQUEST_URI']; ?>
                      {% if auth_permission == 1 %}
                          <li <?php if(preg_match('/admin/', $uri)){ ?>class='active'<?php } ?>>
                              <a href="/backend_app/admin/"><i class="fa fa-key" aria-hidden="true"></i>&nbsp;管理者一覧</a>
                          </li>
                      {% endif %}
                      {% if auth_permission <= 2 %}
                          <li <?php if(preg_match('/user/', $uri)){ ?>class='active'<?php } ?>>
                              <a href="/backend_app/user/"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;ユーザ一覧</a>
                          </li>
                      {% endif %}
                      {% if auth_permission <= 3 %}

                          <li <?php if(preg_match('/terminal/', $uri)){ ?>class='active'<?php } ?>>
                              <a href="/backend_app/terminal/"><i class="fa fa-tablet" aria-hidden="true"></i>&nbsp;端末一覧</a>
                          </li>

                          <?php
                              if(preg_match('#/carrier/#', $uri)){
                                  $active = " class='active'";
                              } elseif(preg_match('#/maker/#', $uri)){
                                  $active = " class='active'";
                              } elseif(preg_match('#/os/#', $uri)){
                                  $active = " class='active'";
                              } elseif(preg_match('#/version/#', $uri)){
                                  $active = " class='active'";
                              } elseif(preg_match('#/organization/#', $uri)){
                                  $active = " class='active'";
                              }
                          ?>

                          <li{{ active }}>
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog" aria-hidden="true"></i>&nbsp;オプション設定<span class="caret"></span></a>
                              <ul class="dropdown-menu">
                                  <li <?php if(preg_match('#/carrier/#', $uri)){ ?>class='active'<?php } ?>><a href="/backend_app/carrier/">キャリア一覧</a></li>
                                  <li <?php if(preg_match('#/maker/#', $uri)){ ?>class='active'<?php } ?>><a href="/backend_app/maker/">メーカー一覧</a></li>
                                  <li <?php if(preg_match('#/os/#', $uri)){ ?>class='active'<?php } ?>><a href="/backend_app/os/">OS一覧</a></li>
                                  <li <?php if(preg_match('#/version/#', $uri)){ ?>class='active'<?php } ?>><a href="/backend_app/version/">バージョン一覧</a></li>
                                  <li <?php if(preg_match('#/organization/#', $uri)){ ?>class='active'<?php } ?>><a href="/backend_app/organization/">組織一覧</a></li>
                              </ul>
                          </li>
                      {% endif %}
                  </ul>

              {% endif %}

              <p class="navbar-text navbar-right">
                  {% if auth_name is not empty %}
                      <a class="brand" href="/backend_app/admin/edit?id={{ auth_id }}/">
                          <i class="fa fa-user" aria-hidden="true"></i>&nbsp;{{ auth_name }}
                      </a>&nbsp;さん
                      &nbsp;|&nbsp;
                      <a class="brand" href="/backend_app/signout/">
                          <i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;ログアウト</a>
                  {% endif %}
              </p>
          </div>

      {# { elements.getMenu() } #}
      </div>
      {{ partial("partials/pankuzu") }}
</nav>
