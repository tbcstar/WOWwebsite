<?php
#################
# Unused class. #
#################
class Support 
{
	
	public function loadEmailForm() 
	{
		?><br/>
		<form action="?page=support&do=email" method="post">
	        问题: <br/>
	        		<select name="issue">
	               		<option>技术问题</option>
	               		<option>违规</option>
	               		<option>其他...</option>       
	        		</select>
	        		<br/>
	        描述你的问题: <br/>
	        <textarea name="description" cols="50" rows="7"></textarea>
        </form>
		<?php
	}
	
}

$Support = new Support();