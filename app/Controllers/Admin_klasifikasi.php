<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Admin_klasifikasi extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			$data['data'] = $this->db->query("select * from klasifikasi where tingkat = 1 order by kodeklasifikasi asc")->getResultArray();
			return view('admin/klasifikasi',$data);
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
				'kodeklasifikasi' => $input['kode'],
				'klasifikasi' => $input['klasifikasi'],
				'tingkat' => $input['tingkat'],
				'reff' => $input['reff']
			);
			$this->mod->inserting('klasifikasi',$data);
		}else{
			$data = array(
				'kodeklasifikasi' => $input['kodebaru'],
				'klasifikasi' => $input['klasifikasi'],
			);
			$this->mod->updating('klasifikasi',$data,['kodeklasifikasi' => $input['kodelama']]);
		}
		return redirect()->to(base_url('a/klasifikasi'));
	}

	public function hapus($x){
		$this->mod->deleting('klasifikasi',['kodeklasifikasi' => $x]);
		return redirect()->to(base_url('a/klasifikasi'));	
	}
}
?>