$(document).ready(function()
{
	function getproject()
	{
		$.post("/release/getproject",
		function(data){
			$("#rel_app").empty();
			$('#rel_app').append('<option value="choose">请选择</option>');
			for(var i=0;i<data.length;i++) 
			{
				if(data[i][1] == 'aliyun')
				{
					var region = '阿里云';
				};
				if(data[i][1] == 'aws')
				{
					var region = '亚马逊AWS';
				};
				if(data[i][1] == 'qcloud')
				{
					var region = '腾讯云';
				};
				if(data[i][1] == 'huidu')
				{
					var region = '阿里云-灰度环境';
				};
				if(data[i][1] == 'test')
				{
					var region = '测试环境';
				};
				$('#rel_app').append('<option value="' + data[i] + '">' + data[i][0] + '--' + region + '</option>');
			};
			
		});
	}
	
	getproject();
});