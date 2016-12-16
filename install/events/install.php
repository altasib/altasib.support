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
$dbEvent = CEventMessage::GetList($b="ID", $order="ASC", Array("EVENT_NAME" => "ALTASIB_SUPPORT"));
if(!($dbEvent->Fetch()))
{
    $arAdd = Array(
        'ALTASIB_SUPPORT_TICKET_CHANGE',
        'ALTASIB_SUPPORT_MESSAGE',
        'ALTASIB_SUPPORT_MESSAGE_SUPPORT',
        'ALTASIB_SUPPORT',
        'ALTASIB_SUPPORT_EXPIRE_NOTIFY'
    ); 
    $langs = CLanguage::GetList(($b=""), ($o="")); 
    while($lang = $langs->Fetch())
    {
        $lid = $lang["LID"];
        IncludeModuleLangFile(__FILE__, $lid);

        $arSites = array();
        $sites = CSite::GetList(($b=""), ($o=""), Array("LANGUAGE_ID"=>$lid));
        while ($site = $sites->Fetch())
                        $arSites[] = $site["LID"];

        $et = new CEventType;
        $emess = new CEventMessage;
        foreach($arAdd as $v)
        {
            $et->Add(array(
                "LID" => $lid,
                "EVENT_NAME" => $v,
                "NAME" => GetMessage($v."_ADD"),
                "DESCRIPTION" => GetMessage($v."_DESC"),
            ));

            if(count($arSites) > 0)
            {
                $bodyType = 'html';
                if($v=='ALTASIB_SUPPORT_EXPIRE_NOTIFY' || $v=='ALTASIB_SUPPORT_TICKET_CHANGE')
                    $bodyType = 'text';
                    
                $emess->Add(array(
                    "ACTIVE" => "Y",
                    "EVENT_NAME" => $v,
                    "LID" => $arSites,
                    "EMAIL_FROM" => "#SUPPORT_EMAIL#",
                    "EMAIL_TO" => "#EMAIL#",
                    "BCC" => "",
                    "SUBJECT" => GetMessage($v."_SUBJECT"),
                    "MESSAGE" => GetMessage($v."_MESSAGE"),
                    "BODY_TYPE" => $bodyType,
                ));
            }
        }
    }
}
?>