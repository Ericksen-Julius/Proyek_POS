<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nota extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('notaModel');
        $this->load->model('kursModel');
        $this->load->library(['form_validation']);
    }
    public function getNota($keyword = false)
    {
        $nota = $this->notaModel->getNota($keyword); // Assuming getAllnota is defined in the model
        return $nota;
    }
    public function nota($parameter = null)
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
            $result = $this->deleteNota($parameter);
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
            $this->form_validation->set_rules('user_input', 'User input', 'required|numeric|max_length[11]');
            $this->form_validation->set_rules('kode_bayar', 'Kode Bayar', 'required');
            $this->form_validation->set_rules('nominal', 'Nominal', 'required');

            // Validate the input data
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

            // Fetch the exchange rate and date
            $kurs = $this->kursModel->getKurs();
            $tanggal = $kurs['data'][0]['TANGGAL'];
            $tanggal = DateTime::createFromFormat('d-M-y', $tanggal);
            $tanggal = $tanggal->format('d/m/Y');
            $kurs = $kurs['data'][0]['KURS'];

            $notaCode = $this->generateNotaCode();

            $sql = "INSERT INTO MK_NOTA_PENJUALAN_A (NO_DOK, NO_HP, TANGGAL, USER_INPUT) VALUES (?, ?, TO_DATE(?, 'DD/MM/YYYY'), ?)";
            $this->db->query($sql, [
                $notaCode,
                $data['no_hp'],
                $tanggal,
                $data['user_input']
            ]);

            foreach ($data['barang'] as $barang) {
                $sql = 'SELECT BERAT FROM MK_MASTER_BARANG WHERE BARCODE_ID = ?';
                $query = $this->db->query($sql, $barang['barcode']);
                $result = $query->row_array();

                $harga = $result['BERAT'] * $kurs;

                $sql = 'INSERT INTO MK_NOTA_PENJUALAN_B (NO_DOK, BARCODE, KURS, HARGA,COUNT) VALUES (?, ?, ?, ?, ?)';
                $this->db->query($sql, [
                    $notaCode,
                    $barang['barcode'],
                    $kurs,
                    $harga,
                    $barang['count']
                ]);
            }

            $sql = 'INSERT INTO MK_NOTA_PENJUALAN_C (NO_DOK, KODE_BAYAR, NOMINAL) VALUES (?, ?, ?)';
            $this->db->query($sql, [
                $notaCode,
                $data['kode_bayar'],
                $data['nominal']
            ]);

            // Return a success response
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


    public function deleteNota($id)
    {
        try {
            $deleteSql = 'DELETE FROM MK_NOTA_PENJUALAN_A WHERE NO_DOK = ?';
            $this->db->query($deleteSql, [$id]);

            if ($this->db->affected_rows() > 0) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('No Dok tidak valid.');
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
    public function check_barcode_exists($barcode)
    {
        $sql = 'SELECT 1 FROM MK_MASTER_BARANG WHERE BARCODE_ID = ?';
        $query = $this->db->query($sql, [$barcode]);
        $result = $query->row();


        if (!$result) {
            $this->form_validation->set_message('check_barcode_exists', 'Barcode barang tidak ditemukan di database');
            return false;
        }

        return true;
    }
    public function check_no_hp_exists($np_hp)
    {
        $sql = 'SELECT 1 FROM MK_MASTER_CUSTOMER WHERE NO_HP = ?';
        $query = $this->db->query($sql, [$np_hp]);
        $result = $query->row();


        if (!$result) {
            $this->form_validation->set_message('check_np_hp_exists', 'Np hp customer tidak ditemukan di database');
            return false;
        }

        return true;
    }
    public function check_user_input_exists($user_input)
    {
        $sql = 'SELECT 1 FROM MK_MASTER_USER WHERE USER_ID = ?';
        $query = $this->db->query($sql, [$user_input]);
        $result = $query->row();

        if (!$result) {
            $this->form_validation->set_message('check_user_input_exists', 'User id admin tidak ditemukan di database');
            return false;
        }

        return true;
    }
    public function check_jenis_bayar_exists($jenis_bayar)
    {
        $sql = 'SELECT 1 FROM MK_MASTER_JENIS_BAYAR WHERE KODE = ?';
        $query = $this->db->query($sql, [$jenis_bayar]);
        $result = $query->row();

        if (!$result) {
            $this->form_validation->set_message('check_jenis_bayar_exists', 'Jenis bayar tidak valid');
            return false;
        }

        return true;
    }
}
