<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Admin_rak extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			$data['data'] = $this->mod->getAll('rak');
			return view('admin/rak',$data);
		}else if(session()->get('petugas')){
			return redirect()->to(base_url('p'));
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function simpan(){
		$input = $this->request->getPost();
		if($input['aksi'] == 'simpan'){
			$data = array(
				'koderak' => null,
				'rak' => $input['rak'],
				'baris' => $input['baris']
			);
			$this->mod->inserting('rak',$data);
		}else{
			$data = array(
				'rak' => $input['rak'],
				'baris' => $input['baris']
			);
			$this->mod->updating('rak',$data,['koderak' => $input['kode']]);
		}
		return redirect()->to(base_url('a/rak'));
	}

	public function hapus($x){
		$this->mod->deleting('rak',['koderak' => $x]);
		return redirect()->to(base_url('a/rak'));	
	}
}
?>