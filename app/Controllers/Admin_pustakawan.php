<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Admin_pustakawan extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			$data['data'] = $this->mod->getSome('petugas',['level' => '1']);
			return view('admin/pustakawan',$data);
		}else if(session()->get('petugas')){
			return redirect()->to(base_url('p'));
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function createusername($x){
		$username = '';
		$ada = true;
		while($ada){
			$username = explode(" ", $x)[0];
			$username = strtolower($username).rand(100,999);
			$cek = $this->mod->getSome('petugas',['username' => $username]);
			if(count($cek) == 0){
				$ada = false;
			}
		}
		return $username;
	}

	public function simpan(){
		$input = $this->request->getPost();
		if($input['aksi'] == 'simpan'){
			$data = array(
				'kodepetugas' => $input['kode'],
				'nip' => $input['nip'],
				'nama' => $input['nama'],
				'jekel' => $input['jekel'],
				'alamat' => $input['alamat'],
				'telepon' => $input['telepon'],
				'level' => '1',
				'username' => $this->createusername($input['nama']),
				'password' => md5(123456),
				'status' => '1'
			);
			$this->mod->inserting('petugas',$data);
		}else{
			$data = array(
				'nip' => $input['nip'],
				'nama' => $input['nama'],
				'jekel' => $input['jekel'],
				'alamat' => $input['alamat'],
				'telepon' => $input['telepon'],
				'status' => $input['status']
			);
			$this->mod->updating('petugas',$data,['kodepetugas' => $input['kode']]);
		}
		return redirect()->to(base_url('a/pustakawan'));
	}

	public function hapus($x){
		$this->mod->deleting('petugas',['kodepetugas' => $x]);
		return redirect()->to(base_url('a/pustakawan'));	
	}
}
?>