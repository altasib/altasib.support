<?php
/**
 * ALTASIB
 * @site http://www.altasib.ru
 * @email dev@altasib.ru
 * @copyright 2006-2018 ALTASIB
 */

namespace ALTASIB\Support;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Class C2CWTable extends Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'altasib_support_client2clientworker';
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
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_USER_ID_FIELD')
            ),
            'USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.USER_ID' => 'ref.ID')
            ),
            'WORKER_USER_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_WORKER_USER_ID_FIELD')
            ),
            'WORKER_USER' => array(
                'data_type' => 'ALTASIB\Support\User',
                'reference' => array('=this.WORKER_USER_ID' => 'ref.ID')
            ),
            'R_VIEW' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_R_VIEW_FIELD')
            ),
            'R_ANSWER' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_R_ANSWER_FIELD')
            ),
            'R_CREATE' => array(
                'data_type' => 'boolean',
                'values' => array('N', 'Y'),
                'title' => Loc::getMessage('ALTASIB_SUPPORT_C2CW_ENTITY_R_CREATE_FIELD')
            ),
        );
    }

    public static function add(array $data)
    {
        if ($data['R_VIEW'] != 'Y') {
            $data['R_VIEW'] = 'N';
        }

        if ($data['R_ANSWER'] != 'Y') {
            $data['R_ANSWER'] = 'N';
        }

        if ($data['R_CREATE'] != 'Y') {
            $data['R_CREATE'] = 'N';
        }

        return parent::add($data);
    }

    public static function getRightList()
    {
        return Array(
            "R_ANSWER" => Loc::getMessage("ALTASIB_SUPPORT_C2CW_ENTITY_R_ANSWER_FIELD"),
            "R_CREATE" => Loc::getMessage("ALTASIB_SUPPORT_C2CW_ENTITY_R_CREATE_FIELD"),
            "R_VIEW" => Loc::getMessage("ALTASIB_SUPPORT_C2CW_ENTITY_R_VIEW_FIELD"),
        );
    }

    public static function isWorker($USER_ID)
    {
        if (self::getList(array('filter' => array('WORKER_USER_ID' => $USER_ID), 'select' => array('ID')))->fetch()) {
            return true;
        }
        return false;
    }

    public static function getWorkerList($USER_ID)
    {
        $result = array();
        if ($USER_ID > 0) {
            $result = self::getList(array(
                'filter' => array('USER_ID' => $USER_ID),
                'select' => array('WORKER_USER_ID'),
                'group' => array('WORKER_USER_ID')
            ))->fetchAll();
        }
        return $result;
    }

    public static function deleteByKey($key){
        $dataKey = explode('-',$key);
        if(isset($dataKey[0]) && $dataKey[1]){
            $userId = (int)$dataKey[0];
            $workerUserId = (int)$dataKey[1];
            if($userId>0 && $workerUserId>0){
                $iterator = self::getList(['filter'=>['WORKER_USER_ID'=>$workerUserId,'USER_ID'=>$userId]]);
                while ($data = $iterator->fetch()){
                    self::delete($data['ID']);
                }
            }
        }
    }
}