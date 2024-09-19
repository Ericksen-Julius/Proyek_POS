<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JenisBayar extends CI_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->model('jenisBayarModel');
	}
	public function getJenisBayar($keyword = false)
	{
		$users = $this->jenisBayarModel->getJenisBayar($keyword); // Assuming getAllUsers is defined in the model
		var_dump($users);
	}
	public function jenisBayar()
	{
		if ($this->input->method() == 'get') {
			$this->getJenisBayar();
		} else if ($this->input->method() == 'post') {
			$json = file_get_contents('php://input');
			$result = $this->inputJenisBayar($json);
			return $result;
		} else if ($this->input->method() == 'put'){
            $json = file_get_contents('php://input');
            $result = $this->editJenisBayar($json);
            return $result;
        } else if ($this->input->method() == 'delete'){
            $json = file_get_contents('php://input');
            $result = $this->deleteJenisBayar($json);
			return $result;
		} else {
			show_404();
			return;
		}
	}

    public function editJenisBayar($json)
    {
        // Decode JSON menjadi array associative
        $data = json_decode($json, true);

        // Validasi data
        $this->load->library('form_validation');

        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('kode', 'Kode', 'required|max_length[5]');
		$this->form_validation->set_rules('jenis_bayar', 'Jenis_bayar', 'required|max_length[20]');

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
        $sql = 'UPDATE MK_MASTER_JENIS_BAYAR SET JENIS_BAYAR = ? WHERE KODE = ?';
        $updated = $this->db->query($sql, [
            $data['jenis_bayar'],
            $data['kode']
        ]);

        if ($updated) {
            echo json_encode([
                'success' => true,
                'message' => 'Customer updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Maaf no_hp tidak ditemukan'
            ]);
        }
    }

    public function deleteJenisBayar($json)
	{
		try {
            $data = json_decode($json, true);
			$sql = 'DELETE FROM MK_MASTER_JENIS_BAYAR WHERE KODE = ?';
			$this->db->query($sql, [$data['kode']]);

			if ($this->db->affected_rows() > 0) {
				echo json_encode(['success' => true]);
			} else {
				throw new Exception('No jenis pembayaran found with the given code.');
			}
		} catch (Exception $e) {
			echo json_encode([
				'success' => false,
				'message' => $e->getMessage()
			]);
		}
	}

	public function inputJenisBayar($json)
	{

		// Decode JSON menjadi array associative
		$data = json_decode($json, true);

		// Set data ke $_POST
		$_POST = $data;

		// Validasi data
		$this->load->library('form_validation');

		$this->form_validation->set_rules('kode', 'Kode', 'required|max_length[5]');
		$this->form_validation->set_rules('jenis_bayar', 'Jenis_bayar', 'required|max_length[20]');

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
		$sql = 'INSERT INTO MK_MASTER_JENIS_BAYAR (KODE, JENIS_BAYAR) VALUES (?, ?)';
		$inserted = $this->db->query($sql, [
			$data['kode'],
			$data['jenis_bayar']
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
