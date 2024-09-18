<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class customerModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function getCustomer($keyword = false)
    {
        $keyword = !$keyword ? '' : $keyword;
        $sql = "SELECT * FROM MK_MASTER_CUSTOMER WHERE NO_HP LIKE ? OR NAMA LIKE ?";
        $data = $this->db->query($sql, [
            '%' . $keyword . '%',
            '%' . $keyword . '%'
        ]);

        if ($data){
            echo json_encode([
				'success' => true,
                'message' => 'berhasil get customer',
                'data' => $data->result_array()
			]);
        } else {
            echo json_encode([
				'success' => false,
				'message' => 'Maaf ada kesalahan, mohon tunggu sebentar'
			]);
        }
    }
    
}