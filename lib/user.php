<?
#################################################
#        Company developer: ALTASIB
#        Developer: Evgeniy Pedan
#        Site: http://www.altasib.ru
#        E-mail: dev@altasib.ru
#        Copyright (c) 2006-2015 ALTASIB
#################################################
?>
<?
namespace ALTASIB\Support;

use Bitrix\Main;
use Bitrix\Main\Type;

class User
{
    function getRole()
    {
        global $APPLICATION;
        return $APPLICATION->GetUserRight("altasib.support");        
    }
    
    function getAvatar($ID)
    {
        $ID = (int)$ID;
        if($ID==0)
            return '';
            
		$connection = Main\Application::getConnection();
        $ob = $connection->query('SELECT PERSONAL_PHOTO from b_user WHERE ID='.$ID);
        if($ar = $ob->fetch())
        {
            $arFile = \CFile::ResizeImageGet(
								$ar["PERSONAL_PHOTO"],
								array("width" => 30, "height" => 30),
								BX_RESIZE_IMAGE_EXACT,
								false
							);
                            
            return $arFile['src'];
        }
		return "";        
    }
    
    function getResponsibleList()
    {
        
    }
    
    public static function isWorker($USER_ID)
    {
        if($user = C2CWTable::getRow(array('select'=>array('USER_ID'),'filter'=>array('WORKER_USER_ID'=>$USER_ID))))
        {
            return $user['USER_ID'];
        }
    
    return false;
    }
    
    public static function getOwner($userId)
    {
        if($user = self::isWorker($userId))
            if(!self::isWorker($user))
                return $user;
            else
                return self::getOwner($user);
    }    
}
?>