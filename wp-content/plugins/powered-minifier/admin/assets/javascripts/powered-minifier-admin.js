(function($)
{
	'use strict';
    $(function()
    {
        if($('.input-status').length > 0)
        {
            $('.input-status').on('click',function()
            {
                if($(this).is(':checked'))
    		    {
    		       	$("#handler-minifier").show(100);
    		    }
    		    else
    		    {
    		    	$("#handler-minifier").hide(500);
    		    }
    		});

    		if($("#handler-minifier.show").length)
    		{
    		    $("#handler-minifier").show();
    		}
        }
    });
})(jQuery);