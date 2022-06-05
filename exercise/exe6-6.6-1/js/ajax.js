$(function() {
	$.ajax({
		url: "data/comment.json",
		cache:false,
		success: function(data) {
			$.each(data, function(index, item) {
				console.log(item)	
				setHtml($("#content ul"),item)
			})
		}
	})

	$("#submit").click(function() {
		var data = {
			"name": $("#name").val(),
			"msg": $("#msg").val(),
			"date": dateFormat(new Date())
		}
		$.ajax({
			url: "message.php",
			data: data,
			type: "post",
			success: function(){
				$("#name").val(""),
				$("#msg").val(""),
				setHtml($("#content ul"),data)
			}
		})
	})

	function setHtml(ele,obj){
		var tempStr = '<li><div class="pic"></div><p><a href="#">' 
					+ obj['name'] + '</a><br />' + obj['msg'] + 
					'<span><br />' + obj['date'] + '</span></p></li>'
		ele.prepend(tempStr)
	}

	function dateFormat(date) {   
		var fmt = "";
		var o = {
			"M+": date.getMonth() + 1,   
			"d+": date.getDate(),   
			"h+": date.getHours(),    
			"m+": date.getMinutes(),   
			"s+": date.getSeconds(),   
			"q+": Math.floor((date.getMonth() + 3) / 3),    
			"S": date.getMilliseconds()
		};
		if (/(y+)/.test("yyyy-MM-dd hh:mm:ss"))
			fmt = "yyyy-MM-dd hh:mm:ss".replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
		for (var k in o)
			if (new RegExp("(" + k + ")").test(fmt))
				fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;
	}
})