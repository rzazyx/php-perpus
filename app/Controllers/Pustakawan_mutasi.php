<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Pustakawan_mutasi extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
		date_default_timezone_set('Asia/Jakarta');
	}

	public function index(){
		if(session()->get('admin')){
			return redirect()->to(base_url('a'));
		}else if(session()->get('petugas')){
			$data['data'] = $this->db->query("select * from transaksi where kodeanggota = 0 order by waktu desc")->getResultArray();
			return view('petugas/mutasi',$data);
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url('a'));
		}
	}

	public function masukbaru(){
		$data['jenis'] = '1';
		$data['detail'] = [];
		$data['pustaka'] = $this->mod->getAll('pustaka');
		return view('petugas/mutasibaru',$data);
	}

	public function keluarbaru(){
		$data['jenis'] = '0';
		$data['detail'] = [];
		$data['pustaka'] = $this->mod->getAll('pustaka');
		return view('petugas/mutasibaru',$data);
	}

	public function tambahdetail(){
		$get = $this->request->getPost();
		$detail = unserialize(base64_decode($get['detail']));
		$data = array(
			'kode' => $get['pustaka'],
			'jumlah' => $get['jumlah'],
			'status' => $get['status'],
			'subjek' => $get['subjek']
		);
		array_push($detail, $data);
		$data['jenis'] = $get['jenis'];
		$data['detail'] = $detail;
		$data['pustaka'] = $this->mod->getAll('pustaka');
		return view('petugas/mutasibaru',$data);
	}

	public function hapusdetail($a,$b,$x){
		$detail = unserialize(base64_decode($x));
		array_splice($detail, $a, 1);
		$data['jenis'] = $b;
		$data['detail'] = $detail;
		$data['pustaka'] = $this->mod->getAll('pustaka');
		return view('petugas/mutasibaru',$data);
	}

	public function simpan(){
		$get = $this->request->getPost();
		$detail = unserialize(base64_decode($get['detail']));
		$jenis = "masuk";
		if($get['jenis'] == '0'){
			$jenis = "keluar";
		}
		$data = array(
			'kodetransaksi' => $get['kode'],
			'jenis' => $jenis,
			'waktu' => date('Y-m-d H:i:s'),
			'keterangan' => $get['keterangan'],
			'status' => '0',
			'kodeanggota' => '0',
			'kodepetugas' => session()->get('petugas')
		);
		$this->mod->inserting('transaksi',$data);
		if($jenis == 'masuk'){
			for ($i=0; $i < count($detail); $i++) {
				$jumlah = 0;
				$cek = $this->mod->getSome('rekap',['kodepustaka' => $detail[$i]['kode']]);
				if(count($cek) > 0){
					$jumlah = $this->db->query("select total from rekap where kodepustaka = '".$detail[$i]['kode']."' order by waktu desc limit 1")->getRowArray()['total'];
				}
				$j1 = 0;
				$j2 = 0;
				$j3 = 0;
				$data = array(
					'kodedetail' => null,
					'jumlah' => $detail[$i]['jumlah'],
					'subjek' => $detail[$i]['subjek'],
					'status' => $detail[$i]['status'],
					'kodepustaka' => $detail[$i]['kode'],
					'kodetransaksi' => $get['kode']
				);
				$this->mod->inserting('detailtransaksi',$data);
				if($detail[$i]['status'] == '1'){
					$j1 += $detail[$i]['jumlah'];
				}
				if($detail[$i]['status'] == '2'){
					$j2 += $detail[$i]['jumlah'];
				}
				if($detail[$i]['status'] == '3'){
					$j3 += $detail[$i]['jumlah'];
				}
				$total = ($jumlah + $j1 + $j2 + $j3);
				$data = array(
					'koderekap' => null,
					'waktu' => date('Y-m-d H:i:s'),
					'jumlah' => $jumlah,
					'j0' => '0',
					'j1' => $j1,
					'j2' => $j2,
					'j3' => $j3,
					'j4' => '0',
					'j5' => '0',
					'j6' => '0',
					'j7' => '0',
					'total' => $total,
					'kodepustaka' => $detail[$i]['kode']
				);
				$this->mod->inserting('rekap',$data);
				$this->mod->updating('pustaka',['eksemplar' => $total],['kodepustaka' => $detail[$i]['kode']]);
			}
		}else{
			for ($i=0; $i < count($detail); $i++) {
				$jumlah = 0;
				$cek = $this->mod->getSome('rekap',['kodepustaka' => $detail[$i]['kode']]);
				if(count($cek) > 0){
					$jumlah = $this->db->query("select total from rekap where kodepustaka = '".$detail[$i]['kode']."' order by waktu asc limit 1")->getRowArray()['total'];
				}
				$j4 = 0;
				$j5 = 0;
				$j6 = 0;
				$data = array(
					'kodedetail' => null,
					'jumlah' => $detail[$i]['jumlah'],
					'subjek' => $detail[$i]['subjek'],
					'status' => $detail[$i]['status'],
					'kodepustaka' => $detail[$i]['kode'],
					'kodetransaksi' => $get['kode']
				);
				$this->mod->inserting('detailtransaksi',$data);
				if($detail[$i]['status'] == '4'){
					$j4 += $detail[$i]['jumlah'];
				}
				if($detail[$i]['status'] == '5'){
					$j5 += $detail[$i]['jumlah'];
				}
				if($detail[$i]['status'] == '6'){
					$j6 += $detail[$i]['jumlah'];
				}
				$total = ($jumlah - $j4 - $j5 - $j6);
				$data = array(
					'koderekap' => null,
					'waktu' => date('Y-m-d H:i:s'),
					'jumlah' => $jumlah,
					'j0' => '0',
					'j1' => '0',
					'j2' => '0',
					'j3' => '0',
					'j4' => $j4,
					'j5' => $j5,
					'j6' => $j6,
					'j7' => '0',
					'total' => $total,
					'kodepustaka' => $detail[$i]['kode']
				);
				$this->mod->inserting('rekap',$data);
				$this->mod->updating('pustaka',['eksemplar' => $total],['kodepustaka' => $detail[$i]['kode']]);
			}
		}
		return redirect()->to(base_url('p/mutasi'));
	}
}
?>