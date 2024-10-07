<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kurs extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('kursModel');
    }
    public function getKurs()
    {
        $kurs = $this->kursModel->getKurs();
        echo json_encode($kurs);
    }
    public function kurs()
    {
        if ($this->input->method() == 'get') {
            $this->getKurs();
        } else if ($this->input->method() == 'post') {
            $json = file_get_contents('php://input');
            $result = $this->inputKurs($json);
            return $result;
        } else {
            show_404();
            return;
        }
    }

    public function inputKurs($json)
    {

        // Decode JSON menjadi array associative
        $data = json_decode($json, true);

        // Set data ke $_POST
        $_POST = $data;

        // Validasi data
        $this->load->library('form_validation');

        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required|callback_validate_date');
        $this->form_validation->set_rules('kurs', 'Kurs', 'required|numeric');


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

        $sqlCheck = "SELECT COUNT(*) as count FROM MK_MASTER_KURS WHERE TANGGAL = TO_DATE(?, 'DD/MM/YYYY')";
        $countResult = $this->db->query($sqlCheck, [$data['tanggal']])->row();
        // var_dump($countResult->COUNT);
        // return;
        
        if ($countResult->COUNT > 0) {
            // If record exists, update it
            $sqlUpdate = "UPDATE MK_MASTER_KURS SET KURS = ? WHERE TANGGAL = TO_DATE(?, 'DD/MM/YYYY')";
            $updated = $this->db->query($sqlUpdate, [$data['kurs'], $data['tanggal']]);
            if ($updated) {
                echo json_encode([
                    'success' => true,
                    'message' => 'berhasil update kurs'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Maaf ada kesalahan, mohon tunggu sebentar'
                ]);
            }
        } else {
            $sql = "INSERT INTO MK_MASTER_KURS (TANGGAL, KURS) VALUES (TO_DATE(?, 'DD/MM/YYYY'), ?)";
            $inserted = $this->db->query($sql, [
                $data['tanggal'],
                $data['kurs'],
            ]);
            if ($inserted) {
                echo json_encode([
                    'success' => true,
                    'message' => 'berhasil input kurs'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Maaf ada kesalahan, mohon tunggu sebentar'
                ]);
            }
        }

        // Jika validasi berhasil, masukkan data ke database
        // $sql = "INSERT INTO MK_MASTER_KURS (TANGGAL, KURS) VALUES (TO_DATE(?, 'DD/MM/YYYY'), ?)";
        // $inserted = $this->db->query($sql, [
        //     $data['tanggal'],
        //     $data['kurs'],
        // ]);

        // if ($inserted) {
        //     echo json_encode([
        //         'success' => true,
        //         'message' => 'berhasil input kurs'
        //     ]);
        // } else {
        //     echo json_encode([
        //         'success' => false,
        //         'message' => 'Maaf ada kesalahan, mohon tunggu sebentar'
        //     ]);
        // }
    }
    public function validate_date($date)
    {
        // Check if the date is in the format DD/MM/YYYY using regular expression
        if (preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{4}$/", $date)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('validate_date', 'The {field} must be in the format DD/MM/YYYY.');
            return FALSE;
        }
    }

    public function getAllKurs(){
        if ($this->input->method() != 'get') {
            show_404();
            return;
        }
        echo json_encode($this->kursModel->getAllKurs());
    }
}
