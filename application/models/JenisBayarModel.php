<?php
defined('BASEPATH') or exit('No direct script access allowed');

class jenisBayarModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getJenisBayar($keyword = false)
    {
        $keyword = !$keyword ? '' : $keyword;
        $sql = "SELECT * FROM MK_MASTER_JENIS_BAYAR WHERE JENIS_BAYAR LIKE ?";
        $data = $this->db->query($sql, [
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
