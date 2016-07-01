<div class="container">
    {{ content() }}
    <hr>
			
    {% if auth_name is empty %}
        <p>
            <span class="pull-right">
                <a href="">
                    <i class="fa fa-external-link" aria-hidden="true"></i>&nbsp;フロント
                </a>	        		
            </span>
        </p>
    {% endif %}
</div>