<?php
defined('BASEPATH') or exit('No direct script access allowed');

class userModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getUser($keyword = false)
    {
        $keyword = !$keyword ? '' : $keyword;
        $sql = "SELECT * FROM MK_MASTER_USER WHERE NAMA LIKE ? OR JABATAN LIKE ?";
        $data = $this->db->query($sql, [
            '%' . $keyword . '%',
            '%' . $keyword . '%'
        ]);
        return $data->result_array();
    }
}
