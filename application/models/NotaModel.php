<?php
defined('BASEPATH') or exit('No direct script access allowed');

class notaModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getNota($keyword = false)
{
    $keyword = !$keyword ? '' : $keyword;
    $keyword = $this->db->escape_like_str($keyword);

    // First, get the main nota details (MK_NOTA_PENJUALAN_A and MK_NOTA_PENJUALAN_C)
    $sql_main = "
    SELECT 
        a.NO_DOK AS no_dok,
        a.NO_HP AS no_hp,
        a.USER_INPUT AS user_input,
        a.TANGGAL AS tanggal,
        c.KODE_BAYAR AS kode_bayar,
        c.NOMINAL AS nominal,
        c.NO_REK AS no_rek,
        c.DISCOUNT AS discount,
        cust.NAMA AS customer_name,  
        usr.NAMA AS user_name
    FROM 
        MK_NOTA_PENJUALAN_A a
    LEFT JOIN 
        MK_NOTA_PENJUALAN_C c ON a.NO_DOK = c.NO_DOK
    LEFT JOIN 
        MK_MASTER_CUSTOMER cust ON a.NO_HP = cust.NO_HP 
    LEFT JOIN 
        MK_MASTER_USER usr ON a.USER_INPUT = usr.USER_ID
    WHERE 
        UPPER(a.NO_DOK) LIKE UPPER(?)
    ";

    // Execute the main query
    $data_main = $this->db->query($sql_main, ['%' . $keyword . '%']);
    $main_results = $data_main->result_array();

    // Initialize an empty array to hold final results
    $final_result = [];

    // Now, for each nota_code, get the associated 'barang' details (MK_NOTA_PENJUALAN_B)
    foreach ($main_results as $main_row) {
        $nota_code = $main_row['NO_DOK'];

        // Get the barang details for this NO_DOK (nota_code)
        $sql_barang = "
        SELECT 
            b.BARCODE AS barcode,
            b.COUNT AS count,
            b.KURS AS kurs,
            b.HARGA AS harga,
            brng.NAMA AS nama
        FROM 
            MK_NOTA_PENJUALAN_B b
        LEFT JOIN
            MK_MASTER_BARANG brng ON b.BARCODE = brng.BARCODE_ID
        WHERE 
            b.NO_DOK = ?
        ";

        // Execute the query for barang
        $data_barang = $this->db->query($sql_barang, [$nota_code]);
        $barang = $data_barang->result_array();

        // Combine the main data with barang data
        $main_row['barang'] = $barang; // Add 'barang' as an array to the main data

        // Add the combined data to the final result array
        $final_result[] = $main_row;
    }

    // Return the final result as a JSON response
    if (!empty($final_result)) {
        echo json_encode([
            'success' => true,
            'data' => $final_result
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No data found'
        ]);
    }
}


    // public function getNota($keyword = false)
    // {
    //     $keyword = !$keyword ? '' : $keyword;
    //     $keyword = $this->db->escape_like_str($keyword);
    //     // $sql = "SELECT * FROM MK_NOTA_PENJUALAN_A WHERE UPPER(NO_DOK) LIKE UPPER(?)";
    //     $sql = "
    //     SELECT 
    //         a.NO_DOK,
    //         a.NO_HP,
    //         a.TANGGAL,
    //         a.USER_INPUT,
    //         MAX(b.BARCODE) AS BARCODE, 
    //     MAX(b.KURS) AS KURS, 
    //     MAX(b.HARGA) AS HARGA, 
    //     MAX(b.COUNT) AS COUNT,
    //     MAX(c.KODE_BAYAR) AS KODE_BAYAR,
    //     MAX(c.NOMINAL) AS NOMINAL,
    //     MAX(c.NO_REK) AS NO_REK,
    //     MAX(c.DISCOUNT) AS DISCOUNT
    //     FROM 
    //         MK_NOTA_PENJUALAN_A a
    //     LEFT JOIN 
    //         MK_NOTA_PENJUALAN_B b ON a.NO_DOK = b.NO_DOK
    //     LEFT JOIN 
    //         MK_NOTA_PENJUALAN_C c ON a.NO_DOK = c.NO_DOK
    //     WHERE 
    //         UPPER(a.NO_DOK) LIKE UPPER(?)
    //         GROUP BY 
    //     a.NO_DOK, a.NO_HP, a.TANGGAL, a.USER_INPUT";
    //     $data = $this->db->query($sql, [
    //         '%' . $keyword . '%'
    //     ]);
    //     $barang = $data->result_array();
    //     if (!empty($barang)) {
    //         echo json_encode([
    //             'success' => true,
    //             'data' => $barang
    //         ]);
    //     } else {
    //         echo json_encode([
    //             'success' => false,
    //             'message' => 'No barang found'
    //         ]);
    //     }
    // }

    // public function getNota($keyword = false)
    // {
    //     $keyword = !$keyword ? '' : $keyword;
    //     $keyword = $this->db->escape_like_str($keyword);
    //     $sql = "SELECT * FROM MK_MASTER_NO WHERE UPPER(NAMA) LIKE UPPER(?) OR UPPER(BERAT) LIKE UPPER(?) OR UPPER(KATEGORI) LIKE UPPER(?)";
    //     $data = $this->db->query($sql, [
    //         '%' . $keyword . '%',
    //         '%' . $keyword . '%',
    //         '%' . $keyword . '%'
    //     ]);
    //     $barang = $data->result_array();
    //     if (!empty($barang)) {
    //         echo json_encode([
    //             'success' => true,
    //             'data' => $barang
    //         ]);
    //     } else {
    //         echo json_encode([
    //             'success' => false,
    //             'message' => 'No barang found'
    //         ]);
    //     }
    // }
}
