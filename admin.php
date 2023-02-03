
<html>
<head>
<meta charset="utf-8">    
<title> Untitled </title>
</head>
<body>

<p>Панель Админа</p>

<table>
<tr> <th>User логин</th> <th>Баланс в USD</th> <th>Баллы</th> <th>Подарки</th> </tr>
<? include 'info_admin.php'; ?> 
</table>

<hr>

<p><b>Командная строка</b></p>

<? session_start(); if (isset($_SESSION['err'])) 
{echo '<span style="color:red">'.$_SESSION['err'].'</span><br><br>';} ?> 
<? session_start(); if (isset($_SESSION['err'])) 
{echo '<span style="color:brown">'.$_SESSION['command'].'</span><br><br>'; 
unset($_SESSION['err']); unset($_SESSION['command']); } ?> 
<? session_start(); if (isset($_SESSION['msg'])) 
{echo '<span style="color:green">'.$_SESSION['msg'].'</span><br><br>'; unset($_SESSION['msg']); } ?> 

<form action="admin_action.php" method="post">
    <input type="text" size="40px" name="command" placeholder="">
    <button type="submit">Выполнить=></button>
</form>  

<?
/*
var_dump($_SESSION['commands']); 
var_dump($_SESSION['user']); 
var_dump($_SESSION['user_login']); 
var_dump($_SESSION['res']); 
var_dump($_SESSION['checkCode']); 
var_dump($_SESSION['flag']);
*/
?>
<br>
<div class="instructions"> <p><b>Инструкция по пользованию командной строки</b></p>
Допустимые команды.<br>
login:0; - отправляет деньги<br>
login:0:0; - отправляет деньги и баллы<br>
login:gift; - отправляет подарок<br>
login:0:0:gift; - отправляет деньги, баллы и подарок<br>
<br>
Примеры запросов:<br>
login:0:0:A;<br>
login:10:20;<br>
login1:C;login2:10:15:E; и т.д.<br>
<br>
Консоль умная. Прежде чем исполнять - она проверяет - все ли указанные пользователи есть в базе.
Также она не выполнит команду если часть команды написана с ошибкой.
Например,<br>  
1:5;demo:C; - она выполнит<br>
1:5;demo:C: - она <b>НЕ</b> выполнит, но при этом 1:5;demo:C - выполнит
</div>

<style>
table {
   border: 1px solid #000;
}
/* границы ячеек первого ряда таблицы */
th {
   border: 1px solid #000;
}
/* границы ячеек тела таблицы */
td {
   border-left: 1px solid #000;
   border-bottom: 1px solid #000;
}

th, tr {border-bottom: 1px solid #000;}

.instructions {
    font-size:18px;
}

</style>

</body>
</html>