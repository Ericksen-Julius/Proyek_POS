<?php
defined('BASEPATH') or exit('No direct script access allowed');

class customerModel extends CI_Model
{

    public function __construct()
    {
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

        if ($data) {
            return [
                'success' => true,
                'data' => $data->result_array()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Maaf ada kesalahan, mohon tunggu sebentar'
            ];
        }
    }
}
