<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Anggota extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			return redirect()->to(base_url('a'));
		}else if(session()->get('petugas')){
			return redirect()->to(base_url('p'));
		}else if(session()->get('anggota')){
			session()->setFlashdata('gagal','');
			return view('anggota/landing');
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function tampilprofil(){
		$data['data'] = $this->mod->getData('anggota',['kodeanggota' => session()->get('anggota')]);
		return view('anggota/profil',$data);
	}

	public function ubahprofil(){
		$get = $this->request->getPost();
		$data = array(
			'nama' => $get['nama'],
			'alamat' => $get['alamat'],
			'telepon' => $get['telepon'],
			'username' => $get['username']
		);
		$this->mod->updating('anggota',$data,['kodeanggota' => $get['kode']]);
		return redirect()->to(base_url('ag/profil'));
	}

	public function tampilakses(){
		session()->setFlashdata('gagal','');
		return view('anggota/akses');
	}

	public function ubahakses(){
		$get = $this->request->getPost();
		$p1 = $get['plama'];
		$p2 = $get['pbaru'];
		$p3 = $get['pkonfirmasi'];
		$cek = $this->mod->getSome('anggota',['kodeanggota' => session()->get('anggota'),'password' => md5($p1)]);
		if(count($cek) == 1){
			if($p2 == $p3){
				$this->mod->updating('anggota',['password' => md5($p3)],['kodeanggota' => session()->get('anggota')]);
				session()->setFlashdata('gagal','Password Berhasil Diubah!');
				return view('anggota/akses');
			}else{
				session()->setFlashdata('gagal','Password Konfirmasi Tidak Sesuai!');
				return view('anggota/akses');
			}
		}else{
			session()->setFlashdata('gagal','Password Lama Tidak Sesuai!');
			return view('anggota/akses');
		}
	}

	public function tampilriwayat(){
		$data['data'] = $this->mod->getSome('transaksi',['jenis' => 'keluar','kodeanggota' => session()->get('anggota')]);
		return view('anggota/riwayat',$data);
	}
}
?>