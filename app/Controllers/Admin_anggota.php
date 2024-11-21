<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Admin_anggota extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			$data['data'] = $this->mod->getAll('anggota');
			return view('admin/anggota',$data);
		}else if(session()->get('petugas')){
			return redirect()->to(base_url('p'));
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function detail($x){
		$data['data'] = $this->mod->getData('anggota',['kodeanggota' => $x]);
		return view('admin/anggotadetail',$data);
	}
}
?>