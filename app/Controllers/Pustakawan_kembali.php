<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Pustakawan_kembali extends BaseController{
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
			return view('petugas/kembali',$data);
		}else if(session()->get('anggota')){
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function simpan(){
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
		return redirect()->to(base_url('p/kembali'));
	}
}
?>