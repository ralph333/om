	function getLanguage(val)
	{
		if(val == "java")
		{
			$('#nginx_info').empty();
			var nginx_html = '<label for="desc">nginx配置</label>'
					  +'<div class="form-group">'
					  +'<textarea type="text" name="rel_conf_project_nginx_ip" style="margin: 0px; width: 286px; height: 214px;" class="om_input" placeholder="每行填写一个ip。IDC和vpc内的机器，请写内网ip。阿里云请写公网ip。"></textarea>'
					  +'</div>'
					  +'<div class="form-group">'
					  +'<input type="text" name="rel_conf_project_nginx_conf_path" class="om_input" placeholder="nginx upstream配置文件">'
					  +'</div>' ;
			
			$('#nginx_info').html(nginx_html);
		}
		if(val !== "java")
		{
			$('#nginx_info').empty();
		}
	}


