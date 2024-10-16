<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('customerModel');
	}
	public function getCustomer($keyword = false)
	{
		$users = $this->customerModel->getCustomer($keyword); // Assuming getAllUsers is defined in the model
		echo json_encode($users);
	}
	public function customer()
	{
		if ($this->input->method() == 'get') {
			$this->getCustomer();
		} else if ($this->input->method() == 'post') {
			$json = file_get_contents('php://input');
			$result = $this->inputCustomer($json);
			return $result;
		} else if ($this->input->method() == 'put') {
			$json = file_get_contents('php://input');
			$result = $this->editCustomer($json);
			return $result;
		} else if ($this->input->method() == 'delete') {
			$json = file_get_contents('php://input');
			$result = $this->deleteCustomer($json);
			return $result;
		} else {
			show_404();
			return;
		}
	}

	public function editCustomer($json)
	{
		// Decode JSON menjadi array associative
		$data = json_decode($json, true);

		// Validasi data
		$this->load->library('form_validation');

		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('no_hp', 'No HP', 'required|max_length[11]');
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
				'message' => 'customer updated successfully'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Maaf no_hp tidak ditemukan'
			]);
		}
	}

	public function deleteCustomer($json)
	{
		try {
			$data = json_decode($json, true);
			$sqlCheck = "SELECT COUNT(*) as count FROM MK_NOTA_PENJUALAN_A WHERE NO_HP = ?";
			$countResult = $this->db->query($sqlCheck, [$data['no_hp']])->row();
			if ($countResult->COUNT > 0) {
				throw new Exception('Customer masih berhubungan dengan nota, tidak bisa dihapus');
			}
			$sql = 'DELETE FROM MK_MASTER_CUSTOMER WHERE NO_HP = ?';
			$this->db->query($sql, [$data['no_hp']]);

			// if ($error['code'] != 0) {
			// 	// Ada error, tangani di sini
			// 	throw new Exception($error['message']);
			// }
			if ($this->db->affected_rows() > 0) {
				echo json_encode(['success' => true]);
			} else {
				throw new Exception('No user found with the given handphone number.');
			}
		} catch (Exception $e) {
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
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

		$this->form_validation->set_rules('no_hp', 'No_hp', 'required|max_length[11]|is_unique[MK_MASTER_CUSTOMER.NO_HP]');
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
