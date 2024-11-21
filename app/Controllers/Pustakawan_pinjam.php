<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Pustakawan_pinjam extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			return redirect()->to(base_url('a'));
		}else if(session()->get('petugas')){
			$data['data'] = $this->db->query("select * from transaksi where jenis = 'keluar' and kodeanggota > 0 order by waktu desc")->getResultArray();
			$data['anggota'] = $this->mod->getAll('anggota');
			return view('petugas/pinjam',$data);
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function baru(){
		$get = $this->request->getPost();
		$data = array(
			'anggota' => $get['anggota'],
			'pinjam' => $get['tglpinjam'],
			'kembali' => $get['tglbatas'],
			'keterangan' => $get['keterangan']
		);
		$data['peminjam'] = $data;
		$data['detail'] = [];
		$data['pustaka'] = $this->mod->getAll('pustaka');
		return view('petugas/pinjambaru',$data);
	}

	public function tambahdetail(){
		$get = $this->request->getPost();
		$detail = unserialize(base64_decode($get['detail']));
		$data = array(
			'kode' => $get['pustaka'],
			'jumlah' => $get['jumlah']
		);
		array_push($detail, $data);
		$data['peminjam'] = unserialize(base64_decode($get['peminjam']));
		$data['detail'] = $detail;
		$data['pustaka'] = $this->mod->getAll('pustaka');
		return view('petugas/pinjambaru',$data);
	}

	public function hapusdetail($a,$b,$x){
		$detail = unserialize(base64_decode($x));
		array_splice($detail, $a, 1);
		$data['peminjam'] = unserialize(base64_decode($b));
		$data['detail'] = $detail;
		$data['pustaka'] = $this->mod->getAll('pustaka');
		return view('petugas/pinjambaru',$data);
	}

	public function simpan(){
		$get = $this->request->getPost();
		$detail = unserialize(base64_decode($get['detail']));
		$peminjam = unserialize(base64_decode($get['peminjam']));
		$data = array(
			'kodetransaksi' => $get['kode'],
			'jenis' => "keluar",
			'waktu' => date('Y-m-d H:i:s'),
			'keterangan' => $peminjam['keterangan'],
			'status' => '1',
			'kodeanggota' => $peminjam['anggota'],
			'kodepetugas' => session()->get('petugas')
		);
		$this->mod->inserting('transaksi',$data);
		$data = array(
			'kodedenda' => null,
			'tglpinjam' => date('Y-m-d', strtotime($peminjam['pinjam'])),
			'tglbatas' => date('Y-m-d', strtotime($peminjam['kembali'])),
			'tglkembali' => date('Y-m-d', strtotime($peminjam['kembali'])),
			'telat' => 0,
			'denda' => 0,
			'total' => 0,
			'kodetransaksi' => $get['kode']
		);
		$this->mod->inserting('denda',$data);
		for ($i=0; $i < count($detail); $i++) {
			$jumlah = 0;
			$cek = $this->mod->getSome('rekap',['kodepustaka' => $detail[$i]['kode']]);
			if(count($cek) > 0){
				$jumlah = $this->db->query("select total from rekap where kodepustaka = '".$detail[$i]['kode']."' order by waktu asc limit 1")->getRowArray()['total'];
			}
			$j7 = 0;
			$data = array(
				'kodedetail' => null,
				'jumlah' => $detail[$i]['jumlah'],
				'subjek' => '',
				'status' => '7',
				'kodepustaka' => $detail[$i]['kode'],
				'kodetransaksi' => $get['kode']
			);
			$this->mod->inserting('detailtransaksi',$data);
			$j7 += $detail[$i]['jumlah'];
			$total = ($jumlah - $j7);
			$data = array(
				'koderekap' => null,
				'waktu' => date('Y-m-d H:i:s'),
				'jumlah' => $jumlah,
				'j0' => '0',
				'j1' => '0',
				'j2' => '0',
				'j3' => '0',
				'j4' => '0',
				'j5' => '0',
				'j6' => '0',
				'j7' => $j7,
				'total' => $total,
				'kodepustaka' => $detail[$i]['kode']
			);
			$this->mod->inserting('rekap',$data);
			$this->mod->updating('pustaka',['eksemplar' => $total],['kodepustaka' => $detail[$i]['kode']]);
		}
		return redirect()->to(base_url('p/pinjam'));
	}

	public function kembali(){
		$get = $this->request->getPost();
		$detail = $this->mod->getSome('detailtransaksi',['kodetransaksi' => $get['kode']]);
		$tgl1 = strtotime($get['tglbatas']); 
		$tgl2 = strtotime($get['tglkembali']); 
		$jarak = $tgl2 - $tgl1;
		$hari = $jarak / 60 / 60 / 24;
		$denda = $hari*$get['denda'];
		$data = array(
			'tglkembali' => date('Y-m-d', strtotime($get['tglkembali'])),
			'telat' => $hari,
			'denda' => $get['denda'],
			'total' => $denda
		);
		$this->mod->updating('denda',$data,['kodetransaksi' => $get['kode']]);
		$this->mod->updating('transaksi',['status' => '0'],['kodetransaksi' => $get['kode']]);
		foreach ($detail as $dt) {
			$a = "jm".$dt['kodedetail'];
			$b = "st".$dt['kodedetail'];
			$jumlah = 0;
			$cek = $this->mod->getSome('rekap',['kodepustaka' => $dt['kodepustaka']]);
			if(count($cek) > 0){
				$jumlah = $this->db->query("select total from rekap where kodepustaka = '".$dt['kodepustaka']."' order by waktu asc limit 1")->getRowArray()['total'];
			}
			$j0 = 0;
			$j5 = 0;
			$j6 = 0;
			$data = array(
				'kodedetail' => null,
				'jumlah' => $get[$a],
				'subjek' => 'kembali',
				'status' => $get[$b],
				'kodepustaka' => $dt['kodepustaka'],
				'kodetransaksi' => $get['kode']
			);
			$this->mod->inserting('detailtransaksi',$data);
			if($get[$b] == '0'){
				$j0 += $get[$a];
			}
			if($get[$b] == '5'){
				$j5 += $get[$a];
			}
			if($get[$b] == '6'){
				$j0 += $get[$a];
				$j6 += $get[$a];
			}
			$total = ($jumlah + $j0 + $j5 - $j6);
			$data = array(
				'koderekap' => null,
				'waktu' => date('Y-m-d H:i:s'),
				'jumlah' => $jumlah,
				'j0' => $j0,
				'j1' => '0',
				'j2' => '0',
				'j3' => '0',
				'j4' => '0',
				'j5' => $j5,
				'j6' => $j6,
				'j7' => '0',
				'total' => $total,
				'kodepustaka' => $dt['kodepustaka']
			);
			$this->mod->inserting('rekap',$data);
			$this->mod->updating('pustaka',['eksemplar' => $total],['kodepustaka' => $dt['kodepustaka']]);
		}
		return redirect()->to(base_url('p/pinjam'));
	}
}
?>