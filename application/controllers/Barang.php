<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('barangModel');
        $this->load->library(['form_validation', 'upload']);
    }
    public function getBarang($keyword = false)
    {
        $barang = $this->barangModel->getBarang($keyword); // Assuming getAllbarang is defined in the model
        return $barang;
    }
    public function barang($parameter = null)
    {
        if ($this->input->method() == 'get') {
            echo json_encode($this->getBarang($parameter));
        } else if ($this->input->method() == 'post') {
            if ($parameter != null) {
                show_404();
                return;
            }
            $result = $this->inputBarang();
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
    public function inputBarang()
    {

        try {
            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // Maximum file size in KB

            // Initialize upload library with config
            $this->upload->initialize($config);

            // Set validation rules
            $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[50]');
            $this->form_validation->set_rules('barcode_id', 'Barcode', 'required|max_length[14]|is_unique[MK_MASTER_BARANG.BARCODE_ID]');
            $this->form_validation->set_rules('berat', 'Berat', 'required|numeric');
            $this->form_validation->set_rules('kategori', 'Kategori', 'required|max_length[10]');

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
            if (!$this->upload->do_upload('foto')) {
                $error = $this->upload->display_errors();
                echo json_encode([
                    'success' => false,
                    'message' => 'File upload failed',
                    'errors' => $error
                ]);
                return;
            }
            $this->form_validation->set_rules('foto', 'Foto', 'required', [
                'required' => 'The %s field is required.',
            ]);
            $uploadData = $this->upload->data();
            $fotoPath = $uploadData['file_name'];

            // Generate barcode
            $barcode = $this->generateBarcode($this->input->post('kategori'));

            // Insert data ke database
            $sql = 'INSERT INTO MK_MASTER_BARANG (NAMA, BERAT, KATEGORI, FOTO, BARCODE_ID) VALUES (?, ?, ?, ?, ?)';
            $inserted = $this->db->query($sql, [
                $this->input->post('nama'),
                $this->input->post('berat'),
                $this->input->post('kategori'),
                $fotoPath,
                $this->input->post('barcode_id'),
            ]);

            if ($inserted) {
                echo json_encode([
                    'success' => true
                ]);
            } else {
                throw new Exception('Input barang failed.');
            }
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

    public function updateBarang()
    {
        try {
            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048; // Maximum file size in KB

            $this->upload->initialize($config);

            // Set validation rules for other fields
            $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[50]');
            // $this->form_validation->set_rules('barcode_id', 'Barcode', 'required|max_length[14]|is_unique[MK_MASTER_BARANG.BARCODE_ID]');
            $this->form_validation->set_rules('berat', 'Berat', 'required|numeric');
            $this->form_validation->set_rules('kategori', 'Kategori', 'required|max_length[10]');

            // Run form validation
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

            // Get the existing record from the database
            $query = $this->db->query('SELECT FOTO FROM MK_MASTER_BARANG WHERE BARCODE_ID = ?', [$this->input->post('barcode_id')]);
            $existingRecord = $query->row();

            // Check if an image was uploaded
            if (!empty($_FILES['foto']['name'])) {
                // Handle file upload
                if (!$this->upload->do_upload('foto')) {
                    $error = $this->upload->display_errors();
                    echo json_encode([
                        'success' => false,
                        'message' => 'File upload failed',
                        'errors' => $error
                    ]);
                    return;
                }

                $uploadData = $this->upload->data();
                $fotoPath = $uploadData['file_name'];

                // Delete the old file if it exists
                if (!empty($existingRecord->FOTO)) {
                    $oldFilePath = 'uploads/' . $existingRecord->FOTO;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            } else {
                $fotoPath = $existingRecord->FOTO;
            }
            // Update the record in the database
            $sql = 'UPDATE MK_MASTER_BARANG SET NAMA = ?, BERAT = ?, KATEGORI = ?, FOTO = ? WHERE BARCODE_ID = ?';
            $updated = $this->db->query($sql, [
                $this->input->post('nama'),
                $this->input->post('berat'),
                $this->input->post('kategori'),
                $fotoPath,
                $this->input->post('barcode_id'),
            ]);

            if ($updated) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Record updated successfully.'
                ]);
            } else {
                throw new Exception('Update operation failed.');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }


    public function generateBarcode($type)
    {
        // Validasi tipe barcode
        if (!in_array($type, ['LM', 'GD'])) {
            throw new Exception('Invalid barcode type.');
        }

        // Ambil tahun sekarang
        $year = date('y');

        // Generate angka random 10 digit
        $randomNumber = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

        // Format barcode
        $barcode = $type . $year . $randomNumber;

        return $barcode;
    }
}
