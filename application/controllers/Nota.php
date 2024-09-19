<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nota extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('notaModel');
        $this->load->library(['form_validation']);
    }
    public function getNota($keyword = false)
    {
        $nota = $this->notaModel->getNota($keyword); // Assuming getAllnota is defined in the model
        return $nota;
    }
    public function barang($parameter = null)
    {
        if ($this->input->method() == 'get') {
            $this->getNota($parameter);
        } else if ($this->input->method() == 'post') {
            if ($parameter != null) {
                show_404();
                return;
            }
            $json = file_get_contents('php://input');
            $result = $this->inputNota($json);
            return $result;
        } else if ($this->input->method() == 'delete') {
            if ($parameter == null) {
                show_404();
                return;
            }
            $result = $this->deleteBarang($parameter);
            return $result;
        } else {
            show_404();
            return;
        }
    }
    public function inputNota($json)
    {
        try {
            $data = json_decode($json, true);

            $this->form_validation->set_data($data);

            // Set validation rules
            $this->form_validation->set_rules('no_hp', 'No Hp', 'required|max_length[15]');
            $this->form_validation->set_rules('tanggal', 'Tanggal', 'required|valid_date');
            $this->form_validation->set_rules('user_input', 'User input', 'required|numeric|max_length[11]');
            $this->form_validation->set_rules('barcode', 'Barcode', 'required|max_length[14]');
            $this->form_validation->set_rules('kurs', 'Kurs', 'required');
            $this->form_validation->set_rules('kode_bayar', 'Kode Bayar', 'required');

            // Additional rule for file upload


            if ($this->form_validation->run() == FALSE) {
                // Validation failed
                $errors = $this->form_validation->error_array();
                echo json_encode([
                    'success' => false,
                    'message' => 'Error validation',
                    'errors' => $errors
                ]);
                return;
            }
            $sql = 'SELECT berat FROM MK_MASTER_BARANG WHERE BARCODE_ID = ?';
            $query = $this->db->query($sql, $data['barcode']);

            $result = $query->row_array();
            $harga = $result['berat'] * $data['kurs'];

            $notaCode = $this->generateNotaCode();

            $sql = 'INSERT INTO MK_NOTA_PENJUALAN_A (NO_DOK,NO_HP, TANGGAL, USER_INPUT) VALUES (?, ?, ?, ?)';
            $this->db->query($sql, [
                $notaCode,
                $data['no_hp'],
                $data['tanggal'],
                $data['user_input'],

            ]);
            $sql = 'INSERT INTO MK_NOTA_PENJUALAN_B (NO_DOK, BARCODE, KURS,HARGA) VALUES (?, ?, ?, ?)';
            $this->db->query($sql, [
                $notaCode,
                $data['barcode'],
                $data['kurs'],
                $harga
            ]);
            $sql = 'INSERT INTO MK_NOTA_PENJUALAN_C (NO_DOK, KODE_BAYAR, NOMINAL) VALUES (?, ?, ?,?)';
            $this->db->query($sql, [
                $notaCode,
                $data['kode_bayar'],
                $harga
            ]);
            echo json_encode([
                'success' => true
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function deleteBarang($id)
    {
        try {
            // Ambil nama file foto berdasarkan ID
            $sql = 'SELECT FOTO FROM MK_MASTER_BARANG WHERE BARCODE_ID = ?';
            $query = $this->db->query($sql, [$id]);
            $result = $query->row();

            if ($result) {
                // Hapus data dari database
                $deleteSql = 'DELETE FROM MK_MASTER_BARANG WHERE BARCODE_ID = ?';
                $this->db->query($deleteSql, [$id]);

                if ($this->db->affected_rows() > 0) {
                    // Hapus file jika ada
                    if ($result->FOTO) {
                        $filePath = FCPATH . 'uploads/' . $result->FOTO;
                        if (file_exists($filePath)) {
                            unlink($filePath);  // Hapus file dari direktori
                        }
                    }

                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception('No barang found with the given ID.');
                }
            } else {
                throw new Exception('No barang found with the given ID.');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function generateNotaCode()
    {
        // Ambil tahun sekarang
        $year = date('y');

        // Generate angka random 10 digit
        $randomNumber = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

        // Format nota
        $nota_dok = 'NT' . $year . $randomNumber;

        return $nota_dok;
    }
}
