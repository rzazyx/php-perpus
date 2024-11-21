<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Pustakawan_siklus extends BaseController{
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
			return redirect()->to(base_url('ag'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function kunjungan(){
		$bulan = date('m');
		$tahun = date('Y');
		$data['bulan'] = $bulan;
		$data['tahun'] = $tahun;
		$data['data'] = $this->db->query("select * from kunjungan where month(waktu) = '".$bulan."' and year(waktu) = '".$tahun."' order by waktu desc")->getResultArray();
		return view('petugas/kunjungan',$data);
	}

	public function kunjungantampil(){
		$get = $this->request->getPost();
		$bulan = $get['bulan'];
		$tahun = $get['tahun'];
		$data['bulan'] = $bulan;
		$data['tahun'] = $tahun;
		$data['data'] = $this->db->query("select * from kunjungan where month(waktu) = '".$bulan."' and year(waktu) = '".$tahun."' order by waktu desc")->getResultArray();
		return view('petugas/kunjungan',$data);
	}
}
?>