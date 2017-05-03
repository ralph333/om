	function getReleaseAction(val)
	{
		if(val == "rollback")
		{
			var project_name=document.getElementById("project").value;
			var html = '<label for="desc">选择要回退的备份</label>'
					 + '<div class="form-group">'
					 + '<select name="backup_files" id="backup_files" class="rel_record_form">'
					 + '<option value="choose">请选择</option>'
					 + '</select>';
			
			$.post("/release/getbackupfiles", { project_name: project_name},
				function(data){
				$('#rel_backup_files').empty();
				$('#rel_backup_files').append(html);
				for(var i=0;i<(data.length-1);i++) {
					$('#backup_files').append('<option value="' +data[i] + '">' + data[i] + '</option>');
				};
					},'json');
			
		}
		if(val == "release")
		{
			$('#rel_backup_files').empty();
		}
	}
