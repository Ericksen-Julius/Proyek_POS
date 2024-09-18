<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->model('customerModel');
	}
	public function getCustomer($keyword = false)
	{
		$users = $this->customerModel->getCustomer($keyword); // Assuming getAllUsers is defined in the model
		var_dump($users);
	}
	public function customer()
	{
		if ($this->input->method() == 'get') {
			$this->getCustomer();
		} else if ($this->input->method() == 'post') {
			$json = file_get_contents('php://input');
			$result = $this->inputCustomer($json);
			return $result;
		} else if ($this->input->method() == 'put'){
            $json = file_get_contents('php://input');
            $result = $this->editCustomer($json);
            return $result;
        } else if ($this->input->method() == 'delete'){
            // $
        }
	}

    public function editCustomer($json)
    {
        // Decode JSON menjadi array associative
        $data = json_decode($json, true);

        // Validasi data
        $this->load->library('form_validation');

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('no_hp', 'No_hp', 'required|max_length[11]');
        $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required|max_length[100]');
        $this->form_validation->set_rules('kota', 'Kota', 'required|max_length[50]');

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal
            $errors = $this->form_validation->error_array();
            echo json_encode([
                'success' => false,
                'message' => 'Error validation',
                'errors' => $errors
            ]);
            return;
        }

        // Jika validasi berhasil, update data ke database
        $sql = 'UPDATE MK_MASTER_CUSTOMER SET Nama = ?, Alamat = ?, Kota = ? WHERE No_hp = ?';
        $updated = $this->db->query($sql, [
            $data['nama'],
            $data['alamat'],
            $data['kota'],
            $data['no_hp']
        ]);

        if ($updated) {
            echo json_encode([
                'success' => true,
                'message' => 'Customer updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Maaf ada kesalahan, mohon tunggu sebentar'
            ]);
        }
    }

	public function inputCustomer($json)
	{

		// Decode JSON menjadi array associative
		$data = json_decode($json, true);

		// Set data ke $_POST
		$_POST = $data;

		// Validasi data
		$this->load->library('form_validation');

		$this->form_validation->set_rules('no_hp', 'No_hp', 'required|max_length[11]');
		$this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'required|max_length[100]');
		$this->form_validation->set_rules('kota', 'Kota', 'required|max_length[50]');


		if ($this->form_validation->run() == FALSE) {
			// Jika validasi gagal
			$errors = $this->form_validation->error_array();
			echo json_encode([
				'success' => false,
				'message' => 'Error validation',
				'errors' => $errors
			]);
			return;
		}

		// Jika validasi berhasil, masukkan data ke database
		$sql = 'INSERT INTO MK_MASTER_CUSTOMER (No_hp, Nama, Alamat, Kota) VALUES (?, ?, ?, ?)';
		$inserted = $this->db->query($sql, [
			$data['no_hp'],
			$data['nama'],
			$data['alamat'],
			$data['kota']
		]);

		if ($inserted) {
			echo json_encode([
				'success' => true
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Maaf ada kesalahan, mohon tunggu sebentar'
			]);
		}
	}
}
