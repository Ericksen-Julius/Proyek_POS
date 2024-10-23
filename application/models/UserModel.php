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
        $sql = "SELECT TRIM(NO_HP) AS NO_HP FROM MK_MASTER_USER WHERE LOWER(USER_ID) LIKE LOWER(?)";
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

    public function insertOTP($keyword = false, $OTP = false)
    {
        $keyword = !$keyword ? '' : $keyword;
        $keyword = $this->db->escape_like_str($keyword);
        $kadaluwarsaTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $sql = "UPDATE MK_MASTER_USER SET OTP = ?, KADALUARSA = TO_DATE('$kadaluwarsaTime', 'YYYY-MM-DD HH24:MI:SS') WHERE LOWER(USER_ID) LIKE LOWER(?)";
        $data = $this->db->query($sql, [
            $OTP, 
            // TO_DATE($kadaluwarsaTime, 'DD/MM/YYYY'),
            '%' . $keyword . '%'
        ]);
        // $users = $data->result_array();
        if ($this->db->affected_rows() > 0) {
            return [
                'success' => true,
                'message' => 'OTP inserted successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No users found or OTP update failed'
            ];
        }
    }

    public function getOTP($keyword = false)
    {
        $keyword = !$keyword ? '' : $keyword;
        $keyword = $this->db->escape_like_str($keyword);
        $sql = "SELECT OTP FROM MK_MASTER_USER WHERE LOWER(USER_ID) LIKE LOWER(?)";
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
