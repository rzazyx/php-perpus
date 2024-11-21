<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Pustakawan_anggota extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			return redirect()->to(base_url('a'));
		}else if(session()->get('petugas')){
			$data['data'] = $this->mod->getAll('anggota');
			return view('petugas/anggota',$data);
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url('a'));
		}
	}

	public function createusername($x){
		$username = '';
		$ada = true;
		while($ada){
			$username = explode(" ", $x)[0];
			$username = strtolower($username).rand(100,999);
			$cek = $this->mod->getSome('anggota',['username' => $username]);
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
				'kodeanggota' => $input['kode'],
				'nisn' => $input['nisn'],
				'nama' => $input['nama'],
				'jekel' => $input['jekel'],
				'alamat' => $input['alamat'],
				'telepon' => $input['telepon'],
				'email' => $input['email'],
				'kelas' => $input['kelas'],
				'username' => $this->createusername($input['nama']),
				'password' => md5(123456),
				'status' => '1'
			);
			$this->mod->inserting('anggota',$data);
		}else{
			$data = array(
				'nisn' => $input['nisn'],
				'nama' => $input['nama'],
				'jekel' => $input['jekel'],
				'alamat' => $input['alamat'],
				'email' => $input['email'],
				'kelas' => $input['kelas'],
				'telepon' => $input['telepon'],
				'status' => $input['status']
			);
			$this->mod->updating('anggota',$data,['kodeanggota' => $input['kode']]);
		}
		return redirect()->to(base_url('p/anggota'));
	}

	public function hapus($x){
		$this->mod->deleting('anggota',['kodeanggota' => $x]);
		return redirect()->to(base_url('p/anggota'));	
	}

	public function detail($x){
		$data['data'] = $this->mod->getData('anggota',['kodeanggota' => $x]);
		return view('petugas/anggotadetail',$data);
	}
}
?>