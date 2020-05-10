<html>
<head>
<script type="text/javascript" src="jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="timers.js"></script>
<script type="text/javascript" src="cook.js"></script>
<link rel="stylesheet" href="css.css" />

<style>



#t-box{
	margin-left:32px;
	}


</style>
 
<div id="msg-box" class="msg-box">
  <ul>
  </ul>
</div>

<form id="t-box" action="?" style="">
  Имя: <input type="text" class='name' style="width:100px;" >
  <input type="text" class='msg' style="width:500px;" >
  <input type="submit" value="Отправить" style="margin-top:5px;">
</form>




</head>
<script>
	
$(function(){

	
  //Если куки с именем не пустые, тащим имя и заполняем форму с именем
	if ($.cookie("name") != ""){
	  $("#t-box input[class='name']").val($.cookie("name"));
	}
	
  //Переменная отвечает за id последнего пришедшего сообщения
  var mid = 0;
  
  
  //Функция обновления сообщений чата
  function get_message_chat(){
    //Генерируем Ajax запрос
    $.ajaxSetup({url: "chat.php",global: true,type: "GET",data: "event=get&id="+mid+"&t="+
        (new Date).getTime()});
    //Отправляем запрос
    $.ajax({
      //Если все удачно
      success: function(msg_j){
        //Если есть сообщения в принятых данных
        if(msg_j.length > 2){
          //Парсим JSON
          var obj = JSON.parse(msg_j);
          //Проганяем циклом по всем принятым сообщениям
          for(var i=0; i < obj.length; i ++){
            //Присваиваем переменной ID сообщения
            mid = obj[i].id;
			
            //Добавляем в чат сообщение
			var fromClass = '';
			if (obj[i].name == $("#t-box input[class='name']").val()){ //$.cookie("name")
				fromClass = 'from-me';
			}else{
				fromClass = 'from-them';
			}
			
			$("#msg-box").append('<p class="'+fromClass+'">'+obj[i].name+': '+obj[i].msg+'</p>');
          }
			//Прокручиваем чат до самого конца
			var objDiv = document.getElementById("msg-box");
			objDiv.scrollTop = objDiv.scrollHeight;
        }
      }
    });
  }
 
  //Первый запрос к серверу. Принимаем сообщения
  get_message_chat();
 
  //Обновляем чат каждые две секунды
  $("#t-box").everyTime(2000, 'refresh', function() {
    get_message_chat();
  });
 
  //Событие отправки формы
  $("#t-box").submit(function() {
    //Запрашиваем имя у юзера.
    if($("#t-box input[class='name']").val() == ""){ alert("Пожалуйста, введите свое имя!")}else{
      //Добавляем в куки имя
      $.cookie("name", $("#t-box input[class='name']").val());
 
      //Тащим сообщение из формы
      var msg = $("#t-box input[class='msg']").val();
      //Если сообщение не пустое
      if(msg != ""){
        //Чистим форму
        $("#t-box input[class='msg']").val("");
        //Генерируем Ajax запрос
        $.ajaxSetup({url: "chat.php", type: "GET",data: "event=set&name="+
            $("#t-box input[class='name']").val() + "&msg=" + msg});
        //Отправляем запрос
        $.ajax();
      }
    }
    //Возвращаем false, чтобы форма не отправлялась.
    return false;
  });
});
</script>
<body>
</body>
</html>