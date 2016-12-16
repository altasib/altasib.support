<?
#################################################
#        Company developer: ALTASIB
#        Developer: Evgeniy Pedan
#        Site: http://www.altasib.ru
#        E-mail: dev@altasib.ru
#        Copyright (c) 2006-2013 ALTASIB
#################################################
?>
<?
namespace ALTASIB\Support;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

Class WtCTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'altasib_support_worker2client';
	}

	public static function getMap()
	{
        return array(
                'ID' => array(
                        'data_type' => 'integer',
                        'primary' => true,
                        'autocomplete' => true,
                ),
                'CATEGORY_ID' => array(
                        'data_type' => 'integer',
                        'required' => true,
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_CATEGORY_ID_FIELD')
                ),                
                'USER_ID' => array(
                        'data_type' => 'integer',
                        'required' => true,
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_USER_ID_FIELD')
                ),
                'USER' => array(
                        'data_type' => 'ALTASIB\Support\User',
                        'reference' => array('=this.USER_ID' => 'ref.ID')
                ),                
                'CLIENT_USER_ID' => array(
                        'data_type' => 'integer',
                        'required' => true,
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_CLIENT_USER_ID_FIELD')
                ),
                'CLIENT_USER' => array(
                        'data_type' => 'ALTASIB\Support\User',
                        'reference' => array('=this.CLIENT_USER_ID' => 'ref.ID')
                ),                
                'R_VIEW' => array(
                        'data_type' => 'boolean',
                        'values' => array('N','Y'),
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_R_VIEW_FIELD')
                ),
                'R_ANSWER' => array(
                        'data_type' => 'boolean',
                        'values' => array('N','Y'),
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_R_ANSWER_FIELD')
                ),
                'R_CHANGE_R' => array(
                        'data_type' => 'boolean',
                        'values' => array('N','Y'),
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_R_FIELD')
                ),
                'R_CHANGE_A' => array(
                        'data_type' => 'boolean',
                        'values' => array('N','Y'),
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_A_FIELD')
                ),
                
                'R_CHANGE_S' => array(
                        'data_type' => 'boolean',
                        'values' => array('N','Y'),
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_S_FIELD')
                ),
                'R_CHANGE_P' => array(
                        'data_type' => 'boolean',
                        'values' => array('N','Y'),
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_P_FIELD')
                ),
                'R_CHANGE_C' => array(
                        'data_type' => 'boolean',
                        'values' => array('N','Y'),
                        'title' => Loc::getMessage('ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_C_FIELD')
                ),                                                                                                                
        );
	}
    public static function add(array $data)
    {
        if($data['R_VIEW']!='Y')
            $data['R_VIEW'] = 'N';

        if($data['R_ANSWER']!='Y')
            $data['R_ANSWER'] = 'N';
            
        if($data['R_CHANGE_R']!='Y')
            $data['R_CHANGE_R'] = 'N';
            
        if($data['R_CHANGE_S']!='Y')
            $data['R_CHANGE_S'] = 'N';
            
        if($data['R_CHANGE_P']!='Y')
            $data['R_CHANGE_P'] = 'N';
            
        if($data['R_CHANGE_C']!='Y')
            $data['R_CHANGE_C'] = 'N';

        if($data['R_CHANGE_A']!='Y')
            $data['R_CHANGE_A'] = 'N';
     
        return parent::add($data);
    }
    public static function getRightList()
    {
            return Array(
					"R_VIEW"=>Loc::getMessage("ALTASIB_SUPPORT_WTC_ENTITY_R_VIEW_FIELD"),
                    "R_ANSWER"=>Loc::getMessage("ALTASIB_SUPPORT_WTC_ENTITY_R_ANSWER_FIELD"),
                    "R_CHANGE_R"=>Loc::getMessage("ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_R_FIELD"),
                    "R_CHANGE_S"=>Loc::getMessage("ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_S_FIELD"),
                    "R_CHANGE_P"=>Loc::getMessage("ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_P_FIELD"),
                    "R_CHANGE_C"=>Loc::getMessage("ALTASIB_SUPPORT_WTC_ENTITY_R_CHANGE_C_FIELD"),                    
    );
    }    
}
?>