	function getProjectinfo(val)
	{
		$.post("/release/getprojectinfo", { project_name: val},
		function(data){
			$('#rel_app_info').empty();
			var html = '<div class="form-group">'
					 + '<label for="desc">开发环境:' + data[2] + '</label>'
					 + '</div>'
					 + '<div class="form-group">'
					 + '<label for="desc">代码仓库地址:' + data[3] + '--' + data[4] + '</label>'
					 + '</div>'
					 + '<div class="form-group">'
					 + '<label for="desc">代码部署路径:' + data[5] + '</label>'
					 + '</div>'
					 + '<div class="form-group">'
					 + '<label for="desc">代码服务器ip:<br>' + data[6] + '</label>'
					 + '</div>'
					 + '<div class="form-group">'
					 + '<label for="desc">nginx服务器ip:<br>' + data[7] + '</label>'
					 + '</div>'
					 + '<div class="form-group">'
					 + '<label for="desc">nginx配置文件:<br>' + data[8] + '</label>'
					 + '</div>'
					 + '<input type="hidden" name="project" id="project" value="' + data[0] + '">'
					 + '<input type="hidden" name="project_region" id="project_region" value="' + data[1] + '">'
					 + '<input type="hidden" name="project_language" id="project_language" value="' + data[2] + '">'
					 + '<input type="hidden" name="project_method" id="project_method" value="' + data[3] + '">'
					 + '<input type="hidden" name="project_method_url" id="project_method_url" value="' + data[4] + '">'
					 + '<input type="hidden" name="project_code_path" id="project_code_path" value="' + data[5] + '">'
					 + '<input type="hidden" name="project_ip" id="project_ip" value="' + data[6] + '">'
					 + '<input type="hidden" name="project_nginx_ip" id="project_nginx_ip" value="' + data[7] + '">'
					 + '<input type="hidden" name="project_nginx_conf" id="project_nginx_conf" value="' + data[8] + '">'
					 + '<input type="hidden" name="project_zookeeper" id="project_zookeeper" value="' + data[9] + '">'
					 + '<div class="form-group">'
					 + '<label for="desc">选择执行动作</label>'
					 + '<div class="form-group">'
					 + '<select name="rel_action" id="rel_action" class="om_input" onchange="getReleaseAction(this.value)">'
					 + '<option value="choose">请选择</option>'
					 + '<option value="release">发布</option>'
					 + '<option value="rollback">回滚</option>'
					 + '</select>'
					 + '</div>'
					 + '</div>'
					 + '<div id="rel_backup_files" class="form-group">'
					 + '</div>';
					
		    
			$('#rel_app_info').append(html);
		},'json');
	}
