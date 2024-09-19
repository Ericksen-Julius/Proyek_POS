<?php
defined('BASEPATH') or exit('No direct script access allowed');

class kursModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getKurs()
    {
        $sql = "SELECT * FROM (SELECT * FROM MK_MASTER_KURS ORDER BY TANGGAL DESC) WHERE ROWNUM = 1";
        $data = $this->db->query($sql);

        if ($data) {
            echo json_encode([
                'success' => true,
                'message' => 'berhasil get kurs',
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
