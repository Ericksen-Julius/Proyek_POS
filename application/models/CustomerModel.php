<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_customer() {
        $query = $this->db->query("SELECT * FROM MK_MASTER_CUSTOMER"); 
        return $query->result_array();
    }

    // public function insert_barang_data($data) {
    //     // $data should be an associative array where keys match the column names in T_BARANG
    //     $this->db->insert('T_BARANG', $data);
        
    //     // Optional: Return the inserted ID if needed
    //     // return $this->db->insert_id();
    // }

    // public function delete_barang_data($kode_barang){
    //     $this->db->where('KODE_BARANG', $kode_barang);
    //     $this->db->delete('T_BARANG');
    // }

    // public function edit_barang_data($kode_barang, $data){
    //     $this->db->where('KODE_BARANG', $kode_barang);
    //     $this->db->update('T_BARANG', $data);
    // }

    // public function check_kode_barang_exists($kode_barang) {
    //     $this->db->where('KODE_BARANG', $kode_barang);
    //     $query = $this->db->get('T_BARANG');
    //     // $temp = $query->num_rows();
    //     // var_dump($temp);
    //     return $query->num_rows() > 0; 
    // }
    
}