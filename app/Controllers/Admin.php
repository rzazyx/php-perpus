<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Admin extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			session()->setFlashdata('gagal','');
			//$this->createvisitor();
			return view('admin/landing');
		}else if(session()->get('petugas')){
			return redirect()->to(base_url('p'));
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function createvisitor(){
		$batasbln = 12;
		$batastgl = 12;
		for ($a=1; $a <= 12 ; $a++) {
			$bln = $a;
			if($bln < 10){
				$bln = "0".$bln;
			}
			$x = 30;
			if($a == 2){
				$x = 28;
			}else if($a == 1 || $a == 3 || $a == 5 || $a == 7 || $a == 8 || $a == 10 || $a == 12){
				$x = 31;
			}
			echo "Bulan ".$a." : ".$x."<br>";
			echo "--------------------------<br>";
			for ($b=1; $b <= $x ; $b++) {
				$frekuensi = rand(0,18);
				$ada = true;
				$tgl = $b;
				if($tgl < 10){
					$tgl = "0".$tgl;
				}
				if($a == $batasbln){
					if($b > $batastgl){
						$ada = false;
					}
				}
				if($ada && $frekuensi > 0){
					echo "2023-".$bln."-".$tgl.": ".$frekuensi."<br>";
					for ($c=1; $c <= $frekuensi; $c++) {
						$s = $this->db->query("select * from kleper order by rand() limit 1")->getRowArray();
						$jam = rand(8,11);
						$menit = rand(0,59);
						$detik = rand(0,59);
						if($jam < 10){
							$jam = "0".$jam;
						}
						if($menit < 10){
							$menit = "0".$menit;
						}
						if($detik < 10){
							$detik = "0".$detik;
						}
						$waktu = "2023-".$bln."-".$tgl." ".$jam.":".$menit.":".$detik;
						$data = array(
							'kodekunjungan' => null,
							'waktu' => $waktu,
							'nama' => $s['nama'],
							'kelas' => $s['kelas']
						);
						$this->mod->inserting('kunjungan',$data);
						echo $waktu." : ".$s['nama']." ".$s['kelas']."<br>";
					}
				}
			}
			echo "++++++++++++++++++++++++++++++++++<br>";
		}
	}

	public function tampilprofil(){
		$data['data'] = $this->mod->getData('petugas',['kodepetugas' => session()->get('admin')]);
		return view('admin/profil',$data);
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
		return redirect()->to(base_url('a/profil'));
	}

	public function tampildasar(){
		$data['data'] = $this->db->query("select * from infosistem")->getRowArray();
		return view('admin/dasar',$data);
	}

	public function ubahdasar(){
		$get = $this->request->getPost();
		$data = array(
			'nama' => $get['nama'],
			'denda' => $get['denda'],
			'kepsek' => $get['kepsek'],
			'nipkepsek' => $get['nip']
		);
		$this->mod->updateAll('infosistem',$data);
		return redirect()->to(base_url('a/dasar'));
	}

	public function tampilakses(){
		session()->setFlashdata('gagal','');
		return view('admin/akses');
	}

	public function ubahakses(){
		$get = $this->request->getPost();
		$p1 = $get['plama'];
		$p2 = $get['pbaru'];
		$p3 = $get['pkonfirmasi'];
		$cek = $this->mod->getSome('petugas',['kodepetugas' => session()->get('admin'),'password' => md5($p1)]);
		if(count($cek) == 1){
			if($p2 == $p3){
				$this->mod->updating('petugas',['password' => md5($p3)],['kodepetugas' => session()->get('admin')]);
				session()->setFlashdata('gagal','Password Berhasil Diubah!');
				return view('admin/akses');
			}else{
				session()->setFlashdata('gagal','Password Konfirmasi Tidak Sesuai!');
				return view('admin/akses');
			}
		}else{
			session()->setFlashdata('gagal','Password Lama Tidak Sesuai!');
			return view('admin/akses');
		}
	}
}
?>