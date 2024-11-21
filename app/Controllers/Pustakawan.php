<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Pustakawan extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			return redirect()->to(base_url('a'));
		}else if(session()->get('petugas')){
			session()->setFlashdata('gagal','');
			return view('petugas/landing');
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function tampilprofil(){
		$data['data'] = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);
		return view('petugas/profil',$data);
	}

	public function ubahprofil(){
		$get = $this->request->getPost();
		$data = array(
			'nip' => $get['nip'],
			'nama' => $get['nama'],
			'alamat' => $get['alamat'],
			'telepon' => $get['telepon'],
			'username' => $get['username']
		);
		$this->mod->updating('petugas',$data,['kodepetugas' => $get['kode']]);
		return redirect()->to(base_url('p/profil'));
	}

	public function tampilakses(){
		session()->setFlashdata('gagal','');
		return view('petugas/akses');
	}

	public function ubahakses(){
		$get = $this->request->getPost();
		$p1 = $get['plama'];
		$p2 = $get['pbaru'];
		$p3 = $get['pkonfirmasi'];
		$cek = $this->mod->getSome('petugas',['kodepetugas' => session()->get('petugas'),'password' => md5($p1)]);
		if(count($cek) == 1){
			if($p2 == $p3){
				$this->mod->updating('petugas',['password' => md5($p3)],['kodepetugas' => session()->get('petugas')]);
				session()->setFlashdata('gagal','Password Berhasil Diubah!');
				return view('petugas/akses');
			}else{
				session()->setFlashdata('gagal','Password Konfirmasi Tidak Sesuai!');
				return view('petugas/akses');
			}
		}else{
			session()->setFlashdata('gagal','Password Lama Tidak Sesuai!');
			return view('petugas/akses');
		}
	}
}
?>