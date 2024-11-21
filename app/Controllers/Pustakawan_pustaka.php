<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Pustakawan_pustaka extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			return redirect()->to(base_url('a'));
		}else if(session()->get('petugas')){
			$data['data'] = $this->mod->getAll('pustaka');
			$data['rak'] = $this->mod->getAll('rak');
			$data['klasifikasi'] = $this->db->query("select * from klasifikasi where tingkat = '1' order by kodeklasifikasi asc")->getResultArray();
			return view('petugas/pustaka',$data);
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url('a'));
		}
	}

	public function baru(){
		$data['rak'] = $this->mod->getAll('rak');
		$data['klasifikasi'] = $this->db->query("select * from klasifikasi where tingkat = '1' order by kodeklasifikasi asc")->getResultArray();
		$data['pengarang'] = [];
		return view('petugas/pustakabaru',$data);
	}

	public function tambahpengarang(){
		$get = $this->request->getPost();
		$pengarang = unserialize(base64_decode($get['pengarang']));
		$data = array(
			'depan' => $get['depan'],
			'tengah' => $get['tengah'],
			'belakang' => $get['belakang']
		);
		array_push($pengarang, $data);
		$data['rak'] = $this->mod->getAll('rak');
		$data['klasifikasi'] = $this->db->query("select * from klasifikasi where tingkat = '1' order by kodeklasifikasi asc")->getResultArray();
		$data['pengarang'] = $pengarang;
		return view('petugas/pustakabaru',$data);
	}

	public function hapuspengarang($a,$x){
		$pengarang = unserialize(base64_decode($x));
		array_splice($pengarang, $a, 1);
		$data['rak'] = $this->mod->getAll('rak');
		$data['klasifikasi'] = $this->db->query("select * from klasifikasi where tingkat = '1' order by kodeklasifikasi asc")->getResultArray();
		$data['pengarang'] = $pengarang;
		return view('petugas/pustakabaru',$data);
	}

	public function buatkode(){
		$get = $this->request->getPost();
		$kode = "";
		$jenis = $get['jenis'];
		if($jenis == 'lain'){
			$jenis = 'X';
		}else{
			$jenis = substr($get['jenis'], 0, 1);
			$jenis = strtoupper($jenis);
		}
		$data = $this->mod->getAll('pustaka');
		if(count($data) == 0){
			$kode = $jenis."0001".date('Y');
		}else{
			$ada = true;
			$x = count($data);
			while ($ada) {
				if($x < 10){
					$kode = $jenis."000".$x.date('Y');
				}else if($x < 100){
					$kode = $jenis."00".$x.date('Y');
				}else if($x < 1000){
					$kode = $jenis."0".$x.date('Y');
				}else {
					$kode = $jenis.$x.date('Y');
				}
				$cek = $this->mod->getSome('pustaka',['kodepustaka' => $kode]);
				if(count($cek) == 0){
					$ada = false;
				}else{
					$x++;
				}
			}
		}
		return $kode;
	}

	public function buatlabel($x){
		$x = $this->mod->getData('pustaka',['kodepustaka' => $x]);
		$a = $this->mod->getData('atribut',['kodepustaka' => $x['kodepustaka']]);
		$k = $this->mod->getData('klasifikasi',['kodeklasifikasi' => $x['kodeklasifikasi']]);
		$p = $this->db->query("select depan from pengarang where kodepustaka = '".$x['kodepustaka']."' order by kodepengarang asc limit 1")->getRowArray()['depan'];
		$pp = $this->db->query("select count(*) as jumlah from pengarang where kodepustaka = '".$x['kodepustaka']."'")->getRowArray()['jumlah'];

		$k1 = $k['kodeklasifikasi'];
		$k2 = $k['klasifikasi'];
		$p1 = strtoupper(substr($p, 0, 3));
		$p2 = $p;
		$p3 = $pp;
		if($p3 > 1){
			$p3 = $p.', et. all';
		}
		$j1 = strtoupper(substr($x['judul'], 0, 1));
		$j2 = ucwords($x['judul']);
		$r1 = "Vol. ".$a['volume'];
		$r2 = $x['kota'].': '.$x['penerbit'].', '.$x['tahun'];
		$r3 = $a['romawi'].', '.$a['halaman'].' hlm, '.$a['ilustrasi'].' ilus';
		$r4 = $a['ns'];
		$label = $k1.'|'.$k2.'|'.$p1.'|'.$p2.'|'.$p3.'|'.$j1.'|'.$j2.'|'.$r1.'|'.$r2.'|'.$r3.'|'.$r4;

		return $label;
	}

	public function simpan(){
		$get = $this->request->getPost();
		$pengarang = unserialize(base64_decode($get['pengarang']));
		$kode = $this->buatkode();
		$sampul = "";
		$ebook = "";
		$file = $this->request->getFile('sampul');
		if($file->isValid()){
			$sampul = $file->getRandomName();
			$file->move('./assets/gambar/sampul/', $sampul);
		}
		$file = $this->request->getFile('ebook');
		if($file->isValid()){
			$ebook = $file->getRandomName();
			$file->move('./assets/file/', $ebook);
		}
		$data = array(
			'kodepustaka' => $kode,
			'jenis' => $get['jenis'],
			'label' => '',
			'judul' => $get['judul'],
			'penerbit' => $get['penerbit'],
			'kota' => $get['kota'],
			'bulan' => $get['bulan'],
			'tahun' => $get['tahun'],
			'eksemplar' => '0',
			'baris' => $get['baris'],
			'status' => '1',
			'kodeklasifikasi' => $get['klasifikasi'],
			'koderak' => $get['rak']
		);
		$this->mod->inserting('pustaka',$data);
		$data = array(
			'kodeatribut' => null,
			'sampul' => $sampul,
			'bahasa' => $get['bahasa'],
			'durasi' => $get['durasi'],
			'genre' => $get['genre'],
			'volume' => $get['volume'],
			'ns' => $get['ns'],
			'halaman' => $get['halaman'],
			'romawi' => $get['romawi'],
			'ilustrasi' => $get['ilustrasi'],
			'file' => $ebook,
			'kodepustaka' => $kode
		);
		$this->mod->inserting('atribut',$data);
		for ($i=0; $i < count($pengarang); $i++) {
			$data = array(
				'kodepengarang' => null,
				'depan' => $pengarang[$i]['depan'],
				'tengah' => $pengarang[$i]['tengah'],
				'belakang' => $pengarang[$i]['belakang'],
				'kodepustaka' => $kode
			);
			$this->mod->inserting('pengarang',$data);
		}
		$label = $this->buatlabel($kode);
		$this->mod->updating('pustaka',['label' => $label],['kodepustaka' => $kode]);
		return redirect()->to(base_url('p/pustaka'));
	}

	public function ubah(){
		$get = $this->request->getPost();
		$data = array(
			'jenis' => $get['jenis'],
			'judul' => $get['judul'],
			'penerbit' => $get['penerbit'],
			'kota' => $get['kota'],
			'bulan' => $get['bulan'],
			'tahun' => $get['tahun'],
			'baris' => $get['baris'],
			'status' => $get['status'],
			'kodeklasifikasi' => $get['klasifikasi'],
			'koderak' => $get['rak']
		);
		$this->mod->updating('pustaka',$data,['kodepustaka' => $get['kode']]);
		$data = array(
			'bahasa' => $get['bahasa'],
			'durasi' => $get['durasi'],
			'genre' => $get['genre'],
			'volume' => $get['volume'],
			'ns' => $get['ns'],
			'halaman' => $get['halaman'],
			'romawi' => $get['romawi'],
			'ilustrasi' => $get['ilustrasi']
		);
		$this->mod->updating('atribut',$data,['kodepustaka' => $get['kode']]);
		return redirect()->to(base_url('p/pustaka'));
	}


	public function detail($x){
		$data['rak'] = $this->mod->getAll('rak');
		$data['klasifikasi'] = $this->db->query("select * from klasifikasi where tingkat = '1' order by kodeklasifikasi asc")->getResultArray();
		$data['data'] = $this->mod->getData('pustaka',['kodepustaka' => $x]);
		$data['atribut'] = $this->mod->getData('atribut',['kodepustaka' => $x]);
		$data['pengarang'] = $this->mod->getSome('pengarang',['kodepustaka' => $x]);
		return view('petugas/pustakadetail',$data);
	}

	public function hapus($x){
		$this->mod->deleting('rekap',['kodepustaka' => $x]);
		$this->mod->deleting('pustaka',['kodepustaka' => $x]);
		$this->mod->deleting('pengarang',['kodepustaka' => $x]);
		$this->mod->deleting('detailtransaksi',['kodepustaka' => $x]);
		$this->mod->deleting('atribut',['kodepustaka' => $x]);
		return redirect()->to(base_url('p/pustaka'));	
	}
}
?>