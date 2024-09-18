<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class customer extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('CustomerModel');
    }

    public function index() {
        $data['customer'] = $this->CustomerModel->get_barang_data();
        // var_dump($data);
        // $this->load->view('formBarang', $data);
    }

    // public function insert() {
    //     $kode_barang = $this->input->post('inputKode');
    
    // // Check if kode barang already exists
    // if ($this->modelBarang->check_kode_barang_exists($kode_barang)) {
    //     // If exists, return an error response
    //     echo json_encode(['status' => 'error', 'message' => 'Kode Barang harus unique!']);
    // } else {
    //     // If not exists, proceed with the insert
    //     $data = array(
    //         'KODE_BARANG' => $kode_barang,
    //         'NAMA_BARANG' => $this->input->post('inputNama'),
    //         'HARGA_BARANG' => $this->input->post('inputHarga')
    //     );

    //     $this->modelBarang->insert_barang_data($data);
    //     echo json_encode(['status' => 'success', 'message' => 'Data successfully saved!']);
    // }
    //     // // Get POST data
    //     // $data = array(
    //     //     'KODE_BARANG' => $this->input->post('inputKode'),
    //     //     'NAMA_BARANG' => $this->input->post('inputNama'),
    //     //     'HARGA_BARANG' => $this->input->post('inputHarga')
    //     // );

    //     // // var_dump($data);

    //     // // Insert data into database
    //     // $insert_id = $this->modelBarang->insert_barang_data($data);

    //     // // if ($insert_id) {
    //     // //     // Success
    //     // //     echo json_encode(['status' => 'success', 'message' => 'Data successfully saved!']);
    //     // // } else {
    //     // //     // Failure
    //     // //     echo json_encode(['status' => 'error', 'message' => 'Failed to save data.']);
    //     // // }
    // }

    // public function delete(){
    //     $kode_barang = $this->input->post('inputKode');
    //     if ($kode_barang) {
    //         $this->modelBarang->delete_barang_data($kode_barang);
    //         echo json_encode(['status' => 'success', 'message' => 'Data successfully deleted!']);
    //     } else {
    //         echo json_encode(['status' => 'error', 'message' => 'No kode_barang provided']);
    //     }
    // }

    // public function edit(){
    //     $kode_barang = $this->input->post('inputKode');
    //     $data = array(
    //         'KODE_BARANG' => $this->input->post('inputKode'),
    //         'NAMA_BARANG' => $this->input->post('inputNama'),
    //         'HARGA_BARANG' => $this->input->post('inputHarga')
    //     );
        
    //     $this->modelBarang->edit_barang_data($kode_barang, $data);
        
    // }
}
