<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('userModel');
	}
	public function getUser($keyword = false)
	{
		$users = $this->userModel->getUser($keyword); // Assuming getAllUsers is defined in the model
		var_dump($users);
	}
	public function user()
	{
		if ($this->input->method() == 'get') {
			$this->getUser();
		} else if ($this->input->method() == 'post') {
			$json = file_get_contents('php://input');
			$result = $this->inputUser($json);
			return $result;
		}
	}
	public function inputUser($json)
	{

		// Decode JSON menjadi array associative
		$data = json_decode($json, true);

		// Set data ke $_POST
		$_POST = $data;

		// Validasi data
		$this->load->library('form_validation');

		$this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'required|max_length[30]');


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

		// Encrypt the password using password_hash()
		$hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

		// Jika validasi berhasil, masukkan data ke database
		$sql = 'INSERT INTO MK_MASTER_CUSTOMER (Nama,Password,Jabatan) VALUES (?, ?, ?)';
		$inserted = $this->db->query($sql, [
			$data['nama'],
			$hashedPassword,
			$data['jabatan']
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
