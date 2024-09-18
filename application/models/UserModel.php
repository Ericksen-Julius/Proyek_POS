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
        $users = $data->result_array();
        if (!empty($users)) {
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No users found'
            ]);
        }
    }
}
