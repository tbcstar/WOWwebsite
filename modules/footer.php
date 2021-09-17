<?php

    if ( DATA['website']['show_load_time'] == true )
    {
    	global $start_time;
        $end = number_format(( microtime() - $start_time), 2, ",", " ");
        echo "页面加载中 ". $end ." 秒。 <br/>";
    }
    echo DATA['website']['footer'];
?>