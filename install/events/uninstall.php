<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2013 ALTASIB             #
#################################################
?>
<?
$arDel = Array(
    'ALTASIB_SUPPORT_TICKET_CHANGE',
    'ALTASIB_SUPPORT_MESSAGE',
    'ALTASIB_SUPPORT_MESSAGE_SUPPORT',
    'ALTASIB_SUPPORT_CHANGE_STATUS',
    'ALTASIB_SUPPORT_CHANGE_RESPONSIBLE',
    'ALTASIB_SUPPORT_CHANGE_PRIORITY',
    'ALTASIB_SUPPORT_CHANGE_CATERGORY',
    'ALTASIB_SUPPORT',
    'ALTASIB_SUPPORT_EXPIRE_NOTIFY'
);
$eventType = new CEventType;
$eventM = new CEventMessage;
foreach($arDel as $v)
{
    $eventType->Delete($v);
    $dbEvent = CEventMessage::GetList($b="ID", $order="ASC", Array("EVENT_NAME" => $v));
    while($arEvent = $dbEvent->Fetch())
            $eventM->Delete($arEvent["ID"]);
}
?>