<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('userModel');
		$this->load->library('form_validation');
	}
	public function getUser($keyword = false)
	{
		$users = $this->userModel->getUser($keyword); // Assuming getAllUsers is defined in the model
		echo json_encode($users);
	}
	public function user($parameter = null)
	{
		if ($this->input->method() == 'get') {
			return $this->getUser($parameter);
		} else if ($this->input->method() == 'post') {
			if ($parameter != null) {
				show_404();
				return;
			}
			$json = file_get_contents('php://input');
			$result = $this->inputUser($json);
			return $result;
		} else if ($this->input->method() == 'put') {
			if ($parameter == null) {
				show_404();
				return;
			}
			$json = file_get_contents('php://input');
			$result = $this->updateUser($parameter, $json);
			return $result;
		} else if ($this->input->method() == 'delete') {
			if ($parameter == null) {
				show_404();
				return;
			}
			$result = $this->deleteUser($parameter);
			return $result;
		} else {
			show_404();
			return;
		}
	}
	public function inputUser($json)
	{
		try {
			$data = json_decode($json, true);

			$_POST = $data;

			// Set validation rules
			$this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('jabatan', 'Jabatan', 'required|max_length[30]');

			// Validate the input data
			if ($this->form_validation->run() == FALSE) {
				// If validation fails, return errors
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

			// Insert the data into the database
			$sql = 'INSERT INTO MK_MASTER_USER (NAMA, PASSWORD, JABATAN) VALUES (?, ?, ?)';
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
				throw new Exception('Insert operation failed.');
			}
		} catch (Exception $e) {
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]);
		}
	}
	public function deleteUser($id)
	{
		try {
			$sql = 'DELETE FROM MK_MASTER_USER WHERE USER_ID = ?';
			$this->db->query($sql, [$id]);

			if ($this->db->affected_rows() > 0) {
				echo json_encode(['success' => true]);
			} else {
				throw new Exception('No user found with the given ID.');
			}
		} catch (Exception $e) {
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]);
		}
	}
	public function updateUser($id, $json)
	{
		try {
			$data = json_decode($json, true);

			// Validasi data

			$this->form_validation->set_data($data);  // Set data for validation

			$this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');
			$this->form_validation->set_rules('password', 'Password', 'required');
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

			$hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

			// Jika validasi berhasil, masukkan data ke database
			$sql = 'UPDATE MK_MASTER_USER SET NAMA = ?, PASSWORD = ?, JABATAN = ? WHERE USER_ID = ?';
			$updated = $this->db->query($sql, [
				$data['nama'],
				$hashedPassword,
				$data['jabatan'],
				(int)$id
			]);

			if ($this->db->affected_rows() > 0) {
				echo json_encode(['success' => true]);
			} else {
				throw new Exception('No user found with the given ID.');
			}
		} catch (Exception $e) {
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]);
		}
	}
	public function authenticateUser()
	{
		if ($this->input->method() == 'post') {
			try {
				$json = file_get_contents('php://input');
				$data = json_decode($json, true);

				$_POST = $data;

				// Set validation rules
				$this->form_validation->set_rules('user_id', 'user_id', 'required|integer');
				$this->form_validation->set_rules('password', 'Password', 'required');
				// Validate the input data
				if ($this->form_validation->run() == FALSE) {
					// If validation fails, return errors
					$errors = $this->form_validation->error_array();
					echo json_encode([
						'success' => false,
						'message' => 'Error validation',
						'errors' => $errors
					]);
					return;
				}

				// Retrieve the user from the database
				$sql = 'SELECT * FROM MK_MASTER_USER WHERE USER_ID = ?';
				$query = $this->db->query($sql, [$data['user_id']]);
				$user = $query->row_array();

				if ($user) {
					if (password_verify($data['password'], $user['PASSWORD'])) {
						// unset($user['PASSWORD']); // Remove password from the response
						$user['PASSWORD'] = $data['password'];
						echo json_encode([
							'success' => true,
							'user' => $user
						]);
					} else {
						// Invalid password
						echo json_encode([
							'success' => false,
							'message' => 'Invalid password'
						]);
					}
				} else {
					// User not found
					echo json_encode([
						'success' => false,
						'message' => 'User not found'
					]);
				}
			} catch (Exception $e) {
				echo json_encode([
					'success' => false,
					'message' => $e->getMessage()
				]);
			}
		} else {
			show_404();
			return;
		}
	}

	public function getUserByUserInput($keyword = false)
	{
		$users = $this->userModel->getUserByUserInput($keyword); 
		echo json_encode($users);
	}
}
