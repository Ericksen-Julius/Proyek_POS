<?php
defined('BASEPATH') or exit('No direct script access allowed');

class notaModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // public function getNota($keyword = false)
    // {
    //     $keyword = !$keyword ? '' : $keyword;
    //     $keyword = $this->db->escape_like_str($keyword);
    //     $sql = "SELECT * FROM MK_MASTER_NO WHERE UPPER(NAMA) LIKE UPPER(?) OR UPPER(BERAT) LIKE UPPER(?) OR UPPER(KATEGORI) LIKE UPPER(?)";
    //     $data = $this->db->query($sql, [
    //         '%' . $keyword . '%',
    //         '%' . $keyword . '%',
    //         '%' . $keyword . '%'
    //     ]);
    //     $barang = $data->result_array();
    //     if (!empty($barang)) {
    //         echo json_encode([
    //             'success' => true,
    //             'data' => $barang
    //         ]);
    //     } else {
    //         echo json_encode([
    //             'success' => false,
    //             'message' => 'No barang found'
    //         ]);
    //     }
    // }
}