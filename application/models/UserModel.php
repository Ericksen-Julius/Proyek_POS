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
        $keyword = $this->db->escape_like_str($keyword);
        $sql = "SELECT * FROM MK_MASTER_USER WHERE LOWER(NAMA) LIKE LOWER(?) OR LOWER(JABATAN) LIKE LOWER(?)";
        $data = $this->db->query($sql, [
            '%' . $keyword . '%',
            '%' . $keyword . '%'
        ]);
        $users = $data->result_array();
        if (!empty($users)) {
            return [
                'success' => true,
                'data' => $users
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No users found'
            ];
        }
    }

    public function getUserByUserInput($keyword = false)
    {
        $keyword = !$keyword ? '' : $keyword;
        $keyword = $this->db->escape_like_str($keyword);
        $sql = "SELECT NAMA,NO_HP FROM MK_MASTER_USER WHERE LOWER(USER_ID) LIKE LOWER(?)";
        $data = $this->db->query($sql, [
            '%' . $keyword . '%'
        ]);
        $users = $data->result_array();
        if (!empty($users)) {
            return [
                'success' => true,
                'data' => $users
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No users found'
            ];
        }
    }
}
