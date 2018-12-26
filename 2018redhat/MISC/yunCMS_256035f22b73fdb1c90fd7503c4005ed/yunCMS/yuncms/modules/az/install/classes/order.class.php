<?php
// --------------------------------------------------------------------------------
// 黄页模块企业产品购买交易订单类
// --------------------------------------------------------------------------------
// phpcms官方团队制作
// http://www.phpcms.cn
// --------------------------------------------------------------------------------
// $Id: order.class.php 2011-6-24 15:19:06 $
// --------------------------------------------------------------------------------

class order {

	private $db;

	/**
	 * 构造函数
	 */
	public function __construct() {
		$this->db = pc_base::load_model('order_model');
	}

	/**
	 * 生成流水记录
	 * @param unknown_type
	 */
	public function add_record($data){
		$require_items = array('userid','username','uid', 'buycarid', 'email','contactname','telephone','order_sn','money','quantity','addtime','usernote','ip');
		if(is_array($data)) {
			foreach($data as $key=>$item) {
				if(in_array($key,$require_items)) $info[$key] = $item;
			}
		} else {
			return false;
		}
		$order_exist = $this->db->get_one(array('order_sn'=>$info['order_sn']), 'id');
		if($order_exist) return $order_exist['id'];
		$this->db->insert($info);
		return $this->db->insert_id();
	}

	/**
	 * 获取流水记录
	 * @param init $id 流水帐号
	 */
	public function get_record($id) {
		$id = intval($id);
		$result = array();
		$result = $this->db->get_one(array('id'=>$id));
		$status_arr = array('succ','failed','error','timeout','cancel');
		return ($result && !in_array($result['status'],$status_arr)) ? $result: false;
	}

	/**
	 * 获取订单信息
	 * @param intval $userid 商户ID
	 * @param intval(0/1) $status 0表示未发货订单，1表示已发货订单，为空为全部订单
	 */
	public function listinfo($userid, $status) {
		$where = array('uid'=>$userid);
		if (isset($status) && is_numeric($status)) {
			$where['status'] = $status;
		}
		$page = max(intval($_GET['page']), 1);
		$data = $this->db->listinfo($where, '`id` DESC', $page);
		$this->pages = $this->db->pages;
		return $data;
	}

	/**
	 * 获取订单详情
	 * @param intval $id 订单ID
	 */
	public function get($id) {
		$result = $this->db->get_one(array('id'=>$id));
		//取得商品信息
		$buycar_db = pc_base::load_model('buycar_model');
		$result['products'] = $buycar_db->select('`id` IN('.$result['buycarid'].') AND `status`=1', 'title, quantity, thumb, url, price');
		//取得送货地址信息
		$member_address = pc_base::load_model('member_address_model');
		$result['address'] = $member_address->get_one(array('userid'=>$result['userid']));
 		return $result;
	}

    /**
	 * 修改订单
	 * @param intval $id 订单ID
     * @param array $data 数组
	 */
    public function update($id, $data) {
		$where = array();
		if ($data['postal']) {
			$where['postal'] = new_addslashes($data['postal']);
		}
		if ($data['status']) {
			$where['status'] = intval($data['status']);
		}
        $r = $this->db->get_one(array('id'=>$id), 'uid, username, contactname, status, email');
        if ($r['status']==0 && $data['tip']) {
        	$message = str_replace($r['username'], '', $r['contactname']);
			$message .= L('you').$message.L('Shipped');
        	if ($data['tip']==1) {
		        $message_db = pc_base::load_model('message_model');
				$message_db->add_message($r['username'],'SYSTEM',L('order_status_reminder'),$message);
        	} else {
        		pc_base::load_sys_func('mail');
        		sendmail($r['email'], L('order_status_reminder'), $message);
        	}
        }
		$this->db->update($where, array('id'=>$id));
        return true;
    }
}