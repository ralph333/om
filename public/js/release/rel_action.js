$(document).ready(function()
	{
      $('#next').click(function()
   	  {      
    	var project_name = $("#project").val();
    	var project_region = $("#project_region").val();
    	var project_language = $("#project_language").val();
    	var project_method = $("#project_method").val();
    	var project_method_url = $("#project_method_url").val();
    	var project_code_path = $("#project_code_path").val();
    	var project_ip = $("#project_ip").val();
    	var rel_action = $("#rel_action ").val();
    	var backup_files = $("#backup_files").val();
    	var project_nginx_ip = $("#project_nginx_ip").val();
    	var project_nginx_conf = $("#project_nginx_conf").val();
    	var project_zookeeper = $("#project_zookeeper").val();
        $.ajax
        ({
        	beforeSend:function()
        	{
        		var opts = {
  					  lines: 17 // The number of lines to draw
  					, length: 30 // The length of each line
  					, width: 15 // The line thickness
  					, radius: 46 // The radius of the inner circle
  					, scale: 1 // Scales overall size of the spinner
  					, corners: 1 // Corner roundness (0..1)
  					, color: '#000' // #rgb or #rrggbb or array of colors
  					, opacity: 0.25 // Opacity of the lines
  					, rotate: 0 // The rotation offset
  					, direction: 1 // 1: clockwise, -1: counterclockwise
  					, speed: 1 // Rounds per second
  					, trail: 60 // Afterglow percentage
  					, fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
  					, zIndex: 2e9 // The z-index (defaults to 2000000000)
  					, className: 'spinner' // The CSS class to assign to the spinner
  					, top: '50%' // Top position relative to parent
  					, left: '50%' // Left position relative to parent
  					, shadow: true // Whether to render a shadow
  					, hwaccel: true // Whether to use hardware acceleration
  					, position: 'absolute' // Element positioning
  					}
  					var target = document.getElementById('foo')
  					var spinner = new Spinner(opts).spin(target);
  			        $("#spin").append(spinner.el);
  			      $('#next').attr("disabled", "1");
  			    $('#cancel').attr("disabled", "1");
        	},
        	type:"post",
        	url:"/release/release_action",
        	
        	data:{project_name:project_name, project_region:project_region, project_language:project_language, project_method:project_method, 
        		  project_method_url:project_method_url, project_code_path:project_code_path, project_ip:project_ip, rel_action:rel_action, 
        		  backup_files:backup_files, project_nginx_ip:project_nginx_ip, project_nginx_conf:project_nginx_conf, project_zookeeper:project_zookeeper},
        	success:function(data)
        	{
        		if(data=="choose action")
        		{		
        			alert("清选择发布动作");window.top.location.href="/release/releasecode";
        			exit;
        		}
        		if(data=="choose backup file")
        		{		
        			alert("清选择回滚文件");window.top.location.href="/release/releasecode";
        			exit;
        		}
        		if(data=="rollback success")
        		{		
        			alert("回滚完成");window.top.location.href="/release/releasecode";
        			exit;
        		}
        		if(data=="success")
        		{		
        			alert("发布成功");window.top.location.href="/release/releasecode";
        			exit;
        		}
        		else
        		{
        			alert(data);
        			exit;
        		}
        	}
        })
         // $('#softwareinfo').append(html);
      });
      
	});