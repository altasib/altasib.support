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
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
class Rights
{
    protected $userId = 0;
    protected $role;
    protected $right;
    protected $supportTeam;
    const CUSTOMER = 'C';
    const SUPPORT = 'E';
    const ADMIN = 'W';
    
	public function __construct($userId,$ticketId)
	{
        global $USER;
        $userId = (int)$userId;
        $ticketId = (int)$ticketId;
        if($userId==0 && isset($USER) && is_object($USER) && $USER->IsAuthorized())
            $userId = $USER->GetID();
        
        if($userId==0)
            throw new \Bitrix\Main\ArgumentNullException('userId');

        $this->userId = (int)$userId;
        $this->role = self::getUserRole();
        $this->supportTeam = self::isSupportTeam();
        if($ticketId>0)
            $this->right = self::getTicketRight($ticketId);
	}
    
    private function getUserRole()
    {
        global $APPLICATION;
        $userGroup = \CUser::GetUserGroup($this->userId);
    return $APPLICATION->GetUserRight("altasib.support",$userGroup); 
    }
    
    public function getRole()
    {
        return $this->role;
    }
    
    public function isSupportTeam()
    {
        return ($this->role == 'E' || $this->role == 'W');
    }
    public function getRight()
    {
        return $this->right;
    }
    
    public function allow($right)
    {
        return (isset($this->right[$right]) && $this->right[$right]);
    }
    
    private function getTicketRight($ticketId)
    {
        $filter = array();
        $this->right = false;
        $ticket = TicketTable::getRow(array('filter'=>array('ID'=>$ticketId),'select'=>array("ID",'CATEGORY_ID',"OWNER_USER_ID","RESPONSIBLE_USER_ID")));
        if($this->role == self::ADMIN)
        {
            $this->right = array(
                'VIEW' => true,
                'ANSWER' => true,
                'CHANGE_STATUS' => true,
                'CHANGE_RESPONSIBLE' => true,
                'CHANGE_ASSISTANTS' => true,
                'CHANGE_CATEGORY' => true,
                'CHANGE_PRIORITY' => true,
            );            
        }
        //todo: add ticket member from cust
        if($this->role == self::CUSTOMER)
        {
            if($ticket['OWNER_USER_ID']!=$this->userId)
            {
                $c2cw = \ALTASIB\Support\C2CWTable::GetList(
                    array('filter' => array('WORKER_USER_ID'=>$this->userId,'CATEGORY_ID'=>$ticket['CATEGORY_ID'],'R_VIEW'=>'Y'))
                );                
                $isWorker = false;
                while($cw = $c2cw->fetch())
                {
                    $isWorker = true;
                    $this->right['VIEW'] = true;
                    if($cw['R_ANSWER']=='Y')
                        $this->right["ANSWER"] = true;
                }
                
                if(!$isWorker)
                {
                    $c2cw = \ALTASIB\Support\C2CWTable::GetList(
                        array('filter' => array('WORKER_USER_ID'=>$ticket['OWNER_USER_ID'],'USER_ID'=>$this->userId))
                    );
                    if($c2cw->fetch())
                        $this->right = array(
                            'VIEW' => true,
                            'CREATE' => true,
                            'ANSWER' => true,
                            'CHANGE_PRIORITY' => true
                        );
                    
                }                
            }
            else
            {
                $this->right = array(
                    'VIEW' => true,
                    'CREATE' => true,
                    'ANSWER' => true,
                    'CHANGE_PRIORITY' => true
                );
            }
        }
        if($this->role == self::SUPPORT)
        {
            if($ticket['RESPONSIBLE_USER_ID']!=$this->userId)
            {
                $wtc = \ALTASIB\Support\WtCTable::GetList(
                    array('filter' => array('USER_ID'=>$this->userId,'CATEGORY_ID'=>$ticket['CATEGORY_ID'],'CLIENT_USER_ID'=>$ticket['OWNER_USER_ID'],'R_VIEW'=>'Y'))
                );
                if($client = $wtc->fetch())
                {
                    $this->right["CHANGE_STATUS"] = $client['R_CHANGE_S'] == 'Y' ? true : false;
                    $this->right["CHANGE_RESPONSIBLE"] = $client['R_CHANGE_R'] == 'Y' ? true : false;
                    $this->right["CHANGE_ASSISTANTS"] = $client['R_CHANGE_A'] == 'Y' ? true : false;
                    $this->right["CHANGE_CATEGORY"] = $client['R_CHANGE_C'] == 'Y' ? true : false;
                    $this->right["CHANGE_PRIORITY"] = $client['R_CHANGE_P'] == 'Y' ? true : false;
                    $this->right["ANSWER"] = $client['R_ANSWER'] == 'Y' ? true : false;
                }
                else
                {
                    if(\ALTASIB\Support\TicketMemberTable::getList(array('select'=>array('ID'),'filter'=>array('USER_ID'=>$this->userId,'TICKET_ID'=>$ticketId)))->fetch())
                    {
                        $this->right['VIEW'] = true;
                        $this->right['ANSWER'] = true;
                    }
                }                
            }
            else
            {
                $this->right = array(
                    'VIEW' => true,
                    'ANSWER' => true,
                    'CHANGE_PRIORITY' => true,
                    'CHANGE_STATUS' => true,
                    'CHANGE_ASSISTANTS' => true
                );
            }
        }
    return $this->right;
    }
}
?>