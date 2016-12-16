<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2015 ALTASIB             #
#################################################
?>
<?
//////////////////////
$MESS['ALTASIB_SUPPORT_TICKET_CHANGE_ADD'] = 'Изменение в обращении';
$MESS['ALTASIB_SUPPORT_TICKET_CHANGE_DESC'] = '
#TICKET_ID# - ID обращения
#TICKET_TITLE# - Заголовок обращения
#TICKET_MESSAGE# - Текст обращения
#TICKET_DATE_CREATE# - дата создания 
#TICKET_CATEGORY# - категория обращения
#TICKET_STATUS# - статус обращения
#TICKET_PRIORITY# - критичность обращения
#TICKET_SLA# - уровень поддержки

#TICKET_OWNER_USER_ID# - ID автора обращения
#TICKET_OWNER_USER_NAME# - имя автора обращения
#TICKET_OWNER_USER_LOGIN# - логин автора обращения
#TICKET_OWNER_USER_EMAIL# - email автора обращения
#TICKET_OWNER_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_OWNER_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_CREATED_USER_ID# - ID автора сообщения
#TICKET_CREATED_USER_NAME# - имя автора сообщения
#TICKET_CREATED_USER_LOGIN# - логин автора сообщения
#TICKET_CREATED_USER_EMAIL# - email автора сообщения
#TICKET_CREATED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_CREATED_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_MODIFIED_USER_ID# - ID кто изменил
#TICKET_MODIFIED_USER_NAME# - имя кто изменил
#TICKET_MODIFIED_USER_LOGIN# - логин кто изменил
#TICKET_MODIFIED_USER_EMAIL# - email кто изменил
#TICKET_MODIFIED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_MODIFIED_USER_LIST_NAME# -  (логин) Фамилия Имя

#RESPONSIBLE_USER_ID# - ID ответственного за обращение
#RESPONSIBLE_USER_NAME# - имя ответственного за обращение
#RESPONSIBLE_USER_LOGIN# - логин ответственного за обращение
#RESPONSIBLE_USER_EMAIL# - email ответственного за обращение
#TICKET_RESPONSIBLE_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_RESPONSIBLE_USER_LIST_NAME# -  (логин) Фамилия Имя

#MESSAGE# - Текст события
#SUPPORT_EMAIL# - email тех поддержки
#URL# - ссылка для изменения обращения
#EMAIL# - email получателя
';
$MESS['ALTASIB_SUPPORT_TICKET_CHANGE_MESSAGE'] = '#MESSAGE#';
$MESS['ALTASIB_SUPPORT_TICKET_CHANGE_SUBJECT'] = 'Изменения в обращении ##TICKET_ID#';

/////////////////////
$MESS['ALTASIB_SUPPORT_MESSAGE_ADD'] = 'Изменение в обращении';
$MESS['ALTASIB_SUPPORT_MESSAGE_DESC'] = '
#TICKET_ID# - ID обращения
#TICKET_TITLE# - Заголовок обращения
#TICKET_MESSAGE# - Текст обращения
#TICKET_DATE_CREATE# - дата создания 
#TICKET_CATEGORY# - категория обращения
#TICKET_STATUS# - статус обращения
#TICKET_PRIORITY# - критичность обращения
#TICKET_SLA# - уровень поддержки

#TICKET_OWNER_USER_ID# - ID автора обращения
#TICKET_OWNER_USER_NAME# - имя автора обращения
#TICKET_OWNER_USER_LOGIN# - логин автора обращения
#TICKET_OWNER_USER_EMAIL# - email автора обращения
#TICKET_OWNER_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_OWNER_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_CREATED_USER_ID# - ID автора сообщения
#TICKET_CREATED_USER_NAME# - имя автора сообщения
#TICKET_CREATED_USER_LOGIN# - логин автора сообщения
#TICKET_CREATED_USER_EMAIL# - email автора сообщения
#TICKET_CREATED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_CREATED_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_MODIFIED_USER_ID# - ID кто изменил
#TICKET_MODIFIED_USER_NAME# - имя кто изменил
#TICKET_MODIFIED_USER_LOGIN# - логин кто изменил
#TICKET_MODIFIED_USER_EMAIL# - email кто изменил
#TICKET_MODIFIED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_MODIFIED_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_RESPONSIBLE_USER_ID# - ID ответственного за обращение
#TICKET_RESPONSIBLE_USER_NAME# - имя ответственного за обращение
#TICKET_RESPONSIBLE_USER_LOGIN# - логин ответственного за обращение
#TICKET_RESPONSIBLE_USER_EMAIL# - email ответственного за обращение
#TICKET_RESPONSIBLE_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_RESPONSIBLE_USER_LIST_NAME# -  (логин) Фамилия Имя

#MESSAGE# - Текст сообщения
#TICKET_FILES# - Список прикрепленныъ файлов
#FILES# - Список прикрепленныъ файлов к сообщению
#CREATED_USER_ID# - ID автора сообщения
#CREATED_USER_NAME# - имя автора сообщения
#CREATED_USER_LOGIN# - логин автора сообщения
#CREATED_USER_EMAIL# - email автора сообщения
#CREATED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#CREATED_USER_LIST_NAME# -  (логин) Фамилия Имя

#SUPPORT_EMAIL# - email тех поддержки
#URL# - ссылка для изменения обращения
#EMAIL# - email получателя
';
$MESS['ALTASIB_SUPPORT_MESSAGE_MESSAGE'] = '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
</head>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:arial, sans-serif; font-size:14px; color:#474747; background:#fff; width:100%; min-width:600px;  max-width:1000px; margin: 0px auto;">
		<tr>
		<td valign="top" align="left" style="background:#007cc5; padding: 10px 17px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td  valign="top" align="left" style="text-align:left; vertical-align:top; "></td>
				<td  valign="bottom" align="right" style="text-align:right; vertical-align:bottom; padding-bottom:3px"></td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
		<td  valign="top" align="left">
		<br />
           <div style="font-size:16px; margin: 0px 17px; border-bottom:1px solid #e4e4e4; padding-bottom:17px; text-align:center; font-weight:bold;">Добавлено сообщение в обращении ##TICKET_ID#</div>
            <div style="padding: 10px 17px;">
            <p>Тема: #TICKET_TITLE#</p>
            <p>От кого: #CREATED_USER_LIST_NAME#</p>
            <p>========ТЕКСТ СООБЩЕНИЯ=======</p>
            <p>#MESSAGE#</p>
            <p>#FILES#</p>
            <p>================================</p>
            
            <p>Статус - #TICKET_STATUS#</p>
            <p>Категория - #TICKET_CATEGORY#</p>
            <p>Критичность - #TICKET_PRIORITY#</p>
            
            <p>Для просмотра и ответа в обращении воспользуйтесь ссылкой:</p>
            <p><a href="#URL#">#URL#</a></p>
			</div>
		</td>
		</tr>
		<tr>
		<td  valign="top" align="left" >
			<div style="height:20px">&nbsp;</div>
		</td>
		<tr>
		<td  valign="top" align="left" style="background:url(images/bg_foot_subscribe.jpg) 0px 0px #e4e8eb; padding:13px 17px 13px 17px;  border-top:1px solid #ececec;">
		</td>
		</tr>
</table>
</body>
</html>';
$MESS['ALTASIB_SUPPORT_MESSAGE_SUBJECT'] = 'Добавлено сообщение в обращении ##TICKET_ID#';

///////////////////////////
$MESS['ALTASIB_SUPPORT_ADD'] = 'Новое обращение';
$MESS['ALTASIB_SUPPORT_DESC'] = '
#TICKET_ID# - ID обращения
#TICKET_TITLE# - Заголовок обращения
#TICKET_MESSAGE# - Текст обращения
#TICKET_DATE_CREATE# - дата создания 
#TICKET_CATEGORY# - категория обращения
#TICKET_STATUS# - статус обращения
#TICKET_PRIORITY# - критичность обращения
#TICKET_SLA# - уровень поддержки

#TICKET_OWNER_USER_ID# - ID автора обращения
#TICKET_OWNER_USER_NAME# - имя автора обращения
#TICKET_OWNER_USER_LOGIN# - логин автора обращения
#TICKET_OWNER_USER_EMAIL# - email автора обращения
#TICKET_OWNER_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_OWNER_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_CREATED_USER_ID# - ID автора сообщения
#TICKET_CREATED_USER_NAME# - имя автора сообщения
#TICKET_CREATED_USER_LOGIN# - логин автора сообщения
#TICKET_CREATED_USER_EMAIL# - email автора сообщения
#TICKET_CREATED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_CREATED_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_MODIFIED_USER_ID# - ID кто изменил
#TICKET_MODIFIED_USER_NAME# - имя кто изменил
#TICKET_MODIFIED_USER_LOGIN# - логин кто изменил
#TICKET_MODIFIED_USER_EMAIL# - email кто изменил
#TICKET_MODIFIED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_MODIFIED_USER_LIST_NAME# -  (логин) Фамилия Имя

#RESPONSIBLE_USER_ID# - ID ответственного за обращение
#RESPONSIBLE_USER_NAME# - имя ответственного за обращение
#RESPONSIBLE_USER_LOGIN# - логин ответственного за обращение
#RESPONSIBLE_USER_EMAIL# - email ответственного за обращение
#TICKET_RESPONSIBLE_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_RESPONSIBLE_USER_LIST_NAME# -  (логин) Фамилия Имя
#TICKET_FILES# - Список прикрепленныъ файлов

#SUPPORT_EMAIL# - email тех поддержки
#URL# - ссылка для изменения обращения
#EMAIL# - email получателя
';

$MESS['ALTASIB_SUPPORT_MESSAGE'] = '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
</head>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:arial, sans-serif; font-size:14px; color:#474747; background:#fff; width:100%; min-width:600px;  max-width:1000px; margin: 0px auto;">
		<tr>
		<td valign="top" align="left" style="background:#007cc5; padding: 10px 17px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td  valign="top" align="left" style="text-align:left; vertical-align:top; "></td>
				<td  valign="bottom" align="right" style="text-align:right; vertical-align:bottom; padding-bottom:3px"></td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
		<td  valign="top" align="left">
		<br />
           <div style="font-size:16px; margin: 0px 17px; border-bottom:1px solid #e4e4e4; padding-bottom:17px; text-align:center; font-weight:bold;">Новое обращение ##TICKET_ID#</div>
            <div style="padding: 10px 17px;">
            <p>Тема: #TICKET_TITLE#</p>
            <p>От кого: #TICKET_OWNER_USER_LIST_NAME#</p>
            <p>=========ТЕКСТ ОБРАЩЕНИЯ========</p>
            <p>#TICKET_MESSAGE#</p>
            <p>#TICKET_FILES#</p>
            <p>================================</p>
            
            <p>Статус - #TICKET_STATUS#</p>
            <p>Ответственный - #TICKET_RESPONSIBLE_USER_LIST_NAME#</p>
            <p>Категория - #TICKET_CATEGORY#</p>
            <p>Критичность - #TICKET_PRIORITY#</p>
            
            <p>Для просмотра и редактирования обращения воспользуйтесь ссылкой:</p>
            <p><a href="#URL#">#URL#</a></p>
			</div>
		</td>
		</tr>
		<tr>
		<td  valign="top" align="left" >
			<div style="height:20px">&nbsp;</div>
		</td>
		<tr>
		<td  valign="top" align="left" style="background:url(images/bg_foot_subscribe.jpg) 0px 0px #e4e8eb; padding:13px 17px 13px 17px;  border-top:1px solid #ececec;">
		</td>
		</tr>
</table>
</body>
</html>';

$MESS['ALTASIB_SUPPORT_SUBJECT'] = 'Новое обращение ##TICKET_ID#';

/////////////////////////
$MESS['ALTASIB_SUPPORT_MESSAGE_SUPPORT_ADD'] = 'Изменение в обращении (для сотрудников)';
$MESS['ALTASIB_SUPPORT_MESSAGE_SUPPORT_DESC'] = '
#TICKET_ID# - ID обращения
#TICKET_TITLE# - Заголовок обращения
#TICKET_MESSAGE# - Текст обращения
#TICKET_DATE_CREATE# - дата создания 
#TICKET_CATEGORY# - категория обращения
#TICKET_STATUS# - статус обращения
#TICKET_PRIORITY# - критичность обращения
#TICKET_SLA# - уровень поддержки

#TICKET_OWNER_USER_ID# - ID автора обращения
#TICKET_OWNER_USER_NAME# - имя автора обращения
#TICKET_OWNER_USER_LOGIN# - логин автора обращения
#TICKET_OWNER_USER_EMAIL# - email автора обращения
#TICKET_OWNER_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_OWNER_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_CREATED_USER_ID# - ID автора сообщения
#TICKET_CREATED_USER_NAME# - имя автора сообщения
#TICKET_CREATED_USER_LOGIN# - логин автора сообщения
#TICKET_CREATED_USER_EMAIL# - email автора сообщения
#TICKET_CREATED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_CREATED_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_MODIFIED_USER_ID# - ID кто изменил
#TICKET_MODIFIED_USER_NAME# - имя кто изменил
#TICKET_MODIFIED_USER_LOGIN# - логин кто изменил
#TICKET_MODIFIED_USER_EMAIL# - email кто изменил
#TICKET_MODIFIED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_MODIFIED_USER_LIST_NAME# -  (логин) Фамилия Имя

#RESPONSIBLE_USER_ID# - ID ответственного за обращение
#RESPONSIBLE_USER_NAME# - имя ответственного за обращение
#RESPONSIBLE_USER_LOGIN# - логин ответственного за обращение
#RESPONSIBLE_USER_EMAIL# - email ответственного за обращение
#TICKET_RESPONSIBLE_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_RESPONSIBLE_USER_LIST_NAME# -  (логин) Фамилия Имя

#MESSAGE# - Текст сообщения
#TICKET_FILES# - Список прикрепленныъ файлов
#FILES# - Список прикрепленныъ файлов к сообщению

#CREATED_USER_ID# - ID автора сообщения
#CREATED_USER_NAME# - имя автора сообщения
#CREATED_USER_LOGIN# - логин автора сообщения
#CREATED_USER_EMAIL# - email автора сообщения
#CREATED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#CREATED_USER_LIST_NAME# -  (логин) Фамилия Имя
#CLOSE_TXT# - Текст "Обращение закрыто клиентом"

#SUPPORT_EMAIL# - email тех поддержки
#URL# - ссылка для изменения обращения
#EMAIL# - email получателя
';
$MESS['ALTASIB_SUPPORT_MESSAGE_SUPPORT_MESSAGE'] = '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=windows-1251" />
</head>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:arial, sans-serif; font-size:14px; color:#474747; background:#fff; width:100%; min-width:600px;  max-width:1000px; margin: 0px auto;">
		<tr>
		<td valign="top" align="left" style="background:#007cc5; padding: 10px 17px;">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr>
				<td  valign="top" align="left" style="text-align:left; vertical-align:top; "></td>
				<td  valign="bottom" align="right" style="text-align:right; vertical-align:bottom; padding-bottom:3px"></td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
		<td  valign="top" align="left">
		<br />
		<div style="font-size:16px; margin: 0px 17px; border-bottom:1px solid #e4e4e4; padding-bottom:17px; text-align:center; font-weight:bold;">Добавлено сообщение в обращении ##TICKET_ID#</div>
		<font style="color: #777">
        #CLOSE_TXT#
		<p>Тема: #TICKET_TITLE#</p>
		<p>От кого: #CREATED_USER_LIST_NAME#</p>
		<p>========ТЕКСТ СООБЩЕНИЯ=======</p>
		</font>
		<p>#MESSAGE#</p>
        <p>#FILES#</p>
		<font style="color: #777">
		<p>================================</p>

		<p>
        Автор обращения:#TICKET_OWNER_USER_LIST_NAME# <br />
		Статус - #TICKET_STATUS#<br />
		Ответственный - <b>#TICKET_RESPONSIBLE_USER_LIST_NAME#</b><br />
		Категория - #TICKET_CATEGORY#<br />
		Критичность - #TICKET_PRIORITY#</p>
                
		<p>(support)</p>
		</font>
		<p>Для просмотра и редактирования обращения воспользуйтесь ссылкой:</p>
		<p><a href="#URL#">#URL#</a></p>
		</td>
		</tr>
		<tr>
		<td  valign="top" align="left" >
			<div style="height:20px">&nbsp;</div>
		</td>
		<tr>
		<td  valign="top" align="left" style="background:url(images/bg_foot_subscribe.jpg) 0px 0px #e4e8eb; padding:13px 17px 13px 17px;  border-top:1px solid #ececec;">
		</td>
		</tr>
</table>
</body>
</html>';
$MESS['ALTASIB_SUPPORT_MESSAGE_SUPPORT_SUBJECT'] = 'Добавлено сообщение в обращении ##TICKET_ID#';

////////////////////
$MESS['ALTASIB_SUPPORT_EXPIRE_NOTIFY_ADD'] = 'Напоминание о необходимости ответа';
$MESS['ALTASIB_SUPPORT_EXPIRE_NOTIFY_DESC'] = '
#TICKET_ID# - ID обращения
#TICKET_TITLE# - Заголовок обращения
#TICKET_MESSAGE# - Текст обращения
#TICKET_DATE_CREATE# - дата создания 
#TICKET_CATEGORY# - категория обращения
#TICKET_STATUS# - статус обращения
#TICKET_PRIORITY# - критичность обращения
#TICKET_SLA# - уровень поддержки

#TICKET_OWNER_USER_ID# - ID автора обращения
#TICKET_OWNER_USER_NAME# - имя автора обращения
#TICKET_OWNER_USER_LOGIN# - логин автора обращения
#TICKET_OWNER_USER_EMAIL# - email автора обращения
#TICKET_OWNER_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_OWNER_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_CREATED_USER_ID# - ID автора сообщения
#TICKET_CREATED_USER_NAME# - имя автора сообщения
#TICKET_CREATED_USER_LOGIN# - логин автора сообщения
#TICKET_CREATED_USER_EMAIL# - email автора сообщения
#TICKET_CREATED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_CREATED_USER_LIST_NAME# -  (логин) Фамилия Имя

#TICKET_MODIFIED_USER_ID# - ID кто изменил
#TICKET_MODIFIED_USER_NAME# - имя кто изменил
#TICKET_MODIFIED_USER_LOGIN# - логин кто изменил
#TICKET_MODIFIED_USER_EMAIL# - email кто изменил
#TICKET_MODIFIED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_MODIFIED_USER_LIST_NAME# -  (логин) Фамилия Имя

#RESPONSIBLE_USER_ID# - ID ответственного за обращение
#RESPONSIBLE_USER_NAME# - имя ответственного за обращение
#RESPONSIBLE_USER_LOGIN# - логин ответственного за обращение
#RESPONSIBLE_USER_EMAIL# - email ответственного за обращение
#TICKET_RESPONSIBLE_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#TICKET_RESPONSIBLE_USER_LIST_NAME# -  (логин) Фамилия Имя

#MESSAGE# - Текст сообщения
#TICKET_FILES# - Список прикрепленныъ файлов
#FILES# - Список прикрепленныъ файлов к сообщению

#CREATED_USER_ID# - ID автора сообщения
#CREATED_USER_NAME# - имя автора сообщения
#CREATED_USER_LOGIN# - логин автора сообщения
#CREATED_USER_EMAIL# - email автора сообщения
#CREATED_USER_SHORT_NAME# - Имя Фамилия, если не заполнены логин
#CREATED_USER_LIST_NAME# -  (логин) Фамилия Имя
#EXPIRATION_DATE# - дата истечения времени реакции
#REMAINED_TIME# - сколько осталось до даты истечения времени реакции 

#SUPPORT_EMAIL# - email тех поддержки
#URL# - ссылка для изменения обращения
#EMAIL# - email получателя
';
$MESS['ALTASIB_SUPPORT_EXPIRE_NOTIFY_MESSAGE'] = 'Напоминание о необходимости ответа в обращении ##TICKET_ID#.

Когда будет просрочено - #EXPIRATION_DATE# (осталось: #REMAINED_TIME#)

>================= ИНФОРМАЦИЯ ПО ОБРАЩЕНИЮ ===========================

Тема: #TICKET_TITLE#
От кого: #TICKET_OWNER_USER_LIST_NAME#

Уровень поддержки - #TICKET_SLA#
Статус - #TICKET_STATUS#
Ответственный - #TICKET_RESPONSIBLE_USER_LIST_NAME#
Категория - #TICKET_CATEGORY#
Критичность - #TICKET_PRIORITY#

>================ СООБЩЕНИЕ ТРЕБУЮЩЕЕ ОТВЕТА =========================
#MESSAGE#
>=====================================================================


Для просмотра либо ответа воспользуйтесь ссылкой:
#URL#';
$MESS['ALTASIB_SUPPORT_EXPIRE_NOTIFY_SUBJECT'] = 'Напоминание о необходимости ответа в обращении ##TICKET_ID#';
?>