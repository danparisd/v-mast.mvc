<?php

namespace Models;

use Core\Model;
use Helpers\Data;

class MembersModel extends Model {

	public  function __construct()
	{
		parent::__construct();
	}

	/**
	 * For getting data of a member
	 * @param $fields Requested fields could be * for all or comma separated list
	 * @param $where array Example: array('id' => array('=', 1), 'name' => array('!=', 'John'))
	 * @return array
	 */
	public function getMember($fields, $where)
	{
		$sql = "SELECT $fields FROM ".PREFIX."members WHERE";
		$prepare = array();
		$i=0;

		foreach($where as $key=>$value)
		{
			$sql .= ($i>0 ? (isset($value[2]) ? " ".$value[2]." " : " AND ") : " ")."$key ".$value[0]." :$key";
			$prepare[':'.$key] = $value[1];
			$i++;
		}

		return $this->db->select($sql, $prepare);
	}

    /**
     * Create new member
     * @param $data
     * @return string
     */
	public function createMember($data){
		$this->db->insert(PREFIX."members",$data);
		return $this->db->lastInsertId('memberID');
	}


    /**
     * Update member
     * @param $data
     * @param $where
     * @return int Number of affected rows
     */
	public function updateMember($data, $where){
		return $this->db->update(PREFIX."members",$data,$where);
	}
}