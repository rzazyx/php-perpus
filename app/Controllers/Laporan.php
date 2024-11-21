<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
use App\Libraries\Fpdf\Fpdf;
class Laporan extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			return redirect()->to(base_url('admin'));
		}else if(session()->get('keu')){
			return redirect()->to(base_url('keu'));
		}else if(session()->get('pen')){
			return redirect()->to(base_url('pen'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function pesan(){
		$data = $this->mod->getSome("tbl_pesan",['status_pesan' => '', 'id_pengguna' => session()->get('pen')]);
		foreach ($data as $d) {
			$this->mod->deleting("tbl_pesan",['id_pesan' => $d['id_pesan']]);
			$this->mod->deleting("tbl_dpesan",['id_pesan' => $d['id_pesan']]);
		}
		$bulan = date('m');
		$tahun = date('Y');
		$data['data'] = $this->db->query("select * from tbl_pesan where (status_pesan = '1' or status_pesan = '0') and month(waktu_pesan) = '".$bulan."' and year(waktu_pesan) = '".$tahun."' order by waktu_pesan asc")->getResultArray();
		$data['bulan'] = $bulan;
		$data['tahun'] = $tahun;
		return view('pen/laporanpesan',$data);
	}

	public function showpesan(){
		$get = $this->request->getPost();
		$bulan = $get['bulan'];
		$tahun = $get['tahun'];
		$data['data'] = $this->db->query("select * from tbl_pesan where (status_pesan = '1' or status_pesan = '0') and month(waktu_pesan) = '".$bulan."' and year(waktu_pesan) = '".$tahun."' order by waktu_pesan asc")->getResultArray();
		$data['bulan'] = $bulan;
		$data['tahun'] = $tahun;
		return view('pen/laporanpesan',$data);
	}

	public function printpesan($x){
		$x = explode("_", $x);
		$pengguna = 'Administrator';
		$tanggal = (int)$x[0];
		$bulan = $x[0];
		$tahun = $x[1];
		$b = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$periode = 'Periode '.$b[(int)$bulan].' '.$tahun;
		$data = $this->db->query("select * from tbl_pesan where (status_pesan = '1' or status_pesan = '0') and month(waktu_pesan) = '".$bulan."' and year(waktu_pesan) = '".$tahun."' order by waktu_pesan asc")->getResultArray();
		if($tanggal == 2){
			if($tahun%4 == 0){
				$tanggal = $tahun.'-02-28';
			}else{
				$tanggal = $tahun.'-02-29';
			}
		}else{
			if($tanggal == 1 || $tanggal == 3 || $tanggal == 5 || $tanggal == 7 || $tanggal == 8 || $tanggal == 10 || $tanggal == 12){
				if($tanggal < 10){
					$tanggal = $tahun.'-0'.$tanggal.'-31';
				}else{
					$tanggal = $tahun.'-'.$tanggal.'-31';
				}
			}else{
				if($tanggal < 10){
					$tanggal = $tahun.'-0'.$tanggal.'-30';
				}else{
					$tanggal = $tahun.'-'.$tanggal.'-30';
				}
			}
		}
		if(session()->get('keu')){
			$pengguna = $this->mod->getData('tbl_pengguna',['id_pengguna' => session()->get('keu')])['nama_pengguna'];
		}else if(session()->get('pen')){
			$pengguna = $this->mod->getData('tbl_pengguna',['id_pengguna' => session()->get('pen')])['nama_pengguna'];
		}
		
		$this->pdf = new fpdf('L','mm','A4');

		$this->pdf->AddPage();
		$this->pdf->SetLineWidth(1);
		$this->pdf->Line(10,27,287,27);
		$this->pdf->SetLineWidth(0);
		$this->pdf->Image('../public/assets/gambar/logo_ikon.png',5,7,20);
		$this->pdf->SetFont('Times','B',14);
		$this->pdf->Cell(10,6,'',0,0);
		$this->pdf->Cell(277,6,'JIDDAH BATIK',0,1,'C');
		$this->pdf->SetFont('Times','B',13);
		$this->pdf->Cell(10,6,'',0,0);
		$this->pdf->Cell(277,6,'Wiradesa',0,1,'C');
		$this->pdf->SetFont('Times','',11);
		$this->pdf->Cell(10,6,'',0,0);
		$this->pdf->Cell(277,4,'Alamat : Gg. Nanas, Bener Satu, Kec. Wiradesa, Kab. Pekalongan, Jawa Tengah, Telp. : 0856-4326-1778',0,1,'C');
		$this->pdf->SetFont('Times','BU',12);
		$this->pdf->Ln(9);
		$this->pdf->Cell(287,6,'LAPORAN PESANAN BARANG',0,1,'C');
		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(287,6,$periode,0,1,'C');
		$this->pdf->Ln(10);

		$this->pdf->SetFont('Times','B',11);
		$this->pdf->Cell(9,6,'No',1,0,'C');
		$this->pdf->Cell(22,6,'Tanggal',1,0,'C');
		$this->pdf->Cell(45,6,'Nama Pemesan',1,0,'C');
		$this->pdf->Cell(63,6,'Nama Barang',1,0,'C');
		$this->pdf->Cell(30,6,'Hrg. Satuan',1,0,'C');
		$this->pdf->Cell(18,6,'Jml. Beli',1,0,'C');
		$this->pdf->Cell(30,6,'Total Bayar',1,0,'C');
		$this->pdf->Cell(30,6,'Uang Muka',1,0,'C');
		$this->pdf->Cell(30,6,'Sisa Pembayaran',1,1,'C');
		$this->pdf->SetFont('Times','',9);
		$n = 1;
		$t1 = 0;
		$t2 = 0;
		$t3 = 0;
		foreach ($data as $d) {
			$x = 1;
			$detail = $this->mod->getSome('tbl_dpesan',['id_pesan' => $d['id_pesan']]);
			foreach ($detail as $dt) {
				$b = $this->mod->getData('tbl_barang',['id_barang' => $dt['id_barang']]);
				if($x == 1){
					$this->pdf->Cell(9,6,$n++,1,0,'C');
				}else{
					$this->pdf->Cell(9,6,'',1,0,'C');
				}
				
				$this->pdf->Cell(22,6,date('d/m/Y', strtotime($d['waktu_pesan'])),1,0,'C');
				$this->pdf->Cell(45,6,$d['an_pesan'],1,0);
				$this->pdf->Cell(63,6,$b['nama_barang'],1,0);
				$this->pdf->Cell(30,6,number_format($dt['hjual_dpesan']),1,0,'R');
				$this->pdf->Cell(18,6,number_format($dt['jumlah_dpesan']),1,0,'R');
				$this->pdf->Cell(30,6,number_format($dt['subtotal_dpesan']),1,0,'R');
				if(count($detail) == 1 || $x == count($detail)){
					$this->pdf->Cell(30,6,number_format($d['dp_pesan']),1,0,'R');
					$this->pdf->Cell(30,6,number_format($d['total_pesan'] - $d['dp_pesan']),1,1,'R');
					$t2 += $d['dp_pesan'];
					$t3 += ($d['total_pesan'] - $d['dp_pesan']);
				}else{
					$this->pdf->Cell(30,6,'',1,0,'R');
					$this->pdf->Cell(30,6,'',1,1,'R');
				}
				
				$t1 += $dt['subtotal_dpesan'];
				
				$x++;
			}
		}
		$this->pdf->SetFont('Times','B',11);
		$this->pdf->Cell(187,6,'TOTAL',1,0,'R');
		$this->pdf->Cell(30,6,number_format($t1),1,0,'R');
		$this->pdf->Cell(30,6,number_format($t2),1,0,'R');
		$this->pdf->Cell(30,6,number_format($t3),1,1,'R');

		$this->pdf->Ln(10);
		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(157,7,'',0,0,'C');
		$this->pdf->Cell(95,7,'Wiradesa, '.$this->tanggal_indo($tanggal),0,1,'C');
		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(95,7,'Mengetahui',0,0,'C');
		$this->pdf->Cell(62,7,'',0,0);
		$this->pdf->Cell(95,7,'Dibuat',0,1,'C');
		$this->pdf->Cell(95,7,'Pemilik',0,0,'C');
		$this->pdf->Cell(62,7,'',0,0);
		$this->pdf->Cell(95,7,'Bagian Penjualan',0,1,'C');
		$this->pdf->Ln(20);
		$this->pdf->SetFont('Times','BU',12);
		$this->pdf->Cell(95,7,'Ardhan',0,0,'C');
		$this->pdf->Cell(62,7,'',0,0);
		$this->pdf->Cell(95,5,$pengguna,0,1,'C');

		$this->pdf->Output("Laporan Pesanan (".$periode.").pdf", 'D');
		//$this->pdf->Output();
		//exit;
	}

	public function jual(){
		$data = $this->mod->getSome("tbl_jual",['total_jual' => '0', 'id_pengguna' => session()->get('pen')]);
		foreach ($data as $d) {
			$this->mod->deleting("tbl_jual",['id_jual' => $d['id_jual']]);
			$this->mod->deleting("tbl_djual",['id_jual' => $d['id_jual']]);
		}
		$bulan = date('m');
		$tahun = date('Y');
		$data['data'] = $this->db->query("select * from tbl_jual where month(waktu_jual) = '".$bulan."' and year(waktu_jual) = '".$tahun."' order by waktu_jual asc")->getResultArray();
		$data['bulan'] = $bulan;
		$data['tahun'] = $tahun;
		return view('pen/laporanjual',$data);
	}

	public function showjual(){
		$get = $this->request->getPost();
		$bulan = $get['bulan'];
		$tahun = $get['tahun'];
		$data['data'] = $this->db->query("select * from tbl_jual where month(waktu_jual) = '".$bulan."' and year(waktu_jual) = '".$tahun."' order by waktu_jual asc")->getResultArray();
		$data['bulan'] = $bulan;
		$data['tahun'] = $tahun;
		return view('pen/laporanjual',$data);
	}

	public function printjual($x){
		$x = explode("_", $x);
		$pengguna = 'Administrator';
		$tanggal = (int)$x[0];
		$bulan = $x[0];
		$tahun = $x[1];
		$b = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$periode = 'Periode '.$b[(int)$bulan].' '.$tahun;
		$data = $this->db->query("select * from tbl_jual where month(waktu_jual) = '".$bulan."' and year(waktu_jual) = '".$tahun."' order by waktu_jual asc")->getResultArray();
		if($tanggal == 2){
			if($tahun%4 == 0){
				$tanggal = $tahun.'-02-28';
			}else{
				$tanggal = $tahun.'-02-29';
			}
		}else{
			if($tanggal == 1 || $tanggal == 3 || $tanggal == 5 || $tanggal == 7 || $tanggal == 8 || $tanggal == 10 || $tanggal == 12){
				if($tanggal < 10){
					$tanggal = $tahun.'-0'.$tanggal.'-31';
				}else{
					$tanggal = $tahun.'-'.$tanggal.'-31';
				}
			}else{
				if($tanggal < 10){
					$tanggal = $tahun.'-0'.$tanggal.'-30';
				}else{
					$tanggal = $tahun.'-'.$tanggal.'-30';
				}
			}
		}
		if(session()->get('keu')){
			$pengguna = $this->mod->getData('tbl_pengguna',['id_pengguna' => session()->get('keu')])['nama_pengguna'];
		}else if(session()->get('pen')){
			$pengguna = $this->mod->getData('tbl_pengguna',['id_pengguna' => session()->get('pen')])['nama_pengguna'];
		}

		$this->pdf = new fpdf('L','mm','A4');

		$this->pdf->AddPage();
		$this->pdf->SetLineWidth(1);
		$this->pdf->Line(10,27,287,27);
		$this->pdf->SetLineWidth(0);
		$this->pdf->Image('../public/assets/gambar/logo_ikon.png',5,7,20);
		$this->pdf->SetFont('Times','B',14);
		$this->pdf->Cell(10,6,'',0,0);
		$this->pdf->Cell(277,6,'JIDDAH BATIK',0,1,'C');
		$this->pdf->SetFont('Times','B',13);
		$this->pdf->Cell(10,6,'',0,0);
		$this->pdf->Cell(277,6,'Wiradesa',0,1,'C');
		$this->pdf->SetFont('Times','',11);
		$this->pdf->Cell(10,6,'',0,0);
		$this->pdf->Cell(277,4,'Alamat : Gg. Nanas, Bener Satu, Kec. Wiradesa, Kab. Pekalongan, Jawa Tengah, Telp. : 0856-4326-1778',0,1,'C');
		$this->pdf->SetFont('Times','BU',12);
		$this->pdf->Ln(9);
		$this->pdf->Cell(287,6,'LAPORAN PENJUALAN BARANG',0,1,'C');
		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(287,6,$periode,0,1,'C');
		$this->pdf->Ln(10);

		$this->pdf->SetFont('Times','B',11);
		$this->pdf->Cell(7,6,'No',1,0,'C');
		$this->pdf->Cell(20,6,'Tanggal',1,0,'C');
		$this->pdf->Cell(112.5,6,'No. Faktur / Penjualan',1,0,'C');
		$this->pdf->Cell(112.5,6,'No. PO',1,0,'C');
		$this->pdf->Cell(25,6,'Total',1,1,'C');
		$this->pdf->SetFont('Times','',9);
		$n = 1;
		$total = 0;
		foreach ($data as $d) {
			$this->pdf->Cell(7,6,$n,1,0,'C');
			$this->pdf->Cell(20,6,date('d/m/Y', strtotime($d['waktu_jual'])),1,0,'C');
			$this->pdf->Cell(112.5,6,$d['id_jual'],1,0);
			$this->pdf->Cell(112.5,6,$d['id_pesan'],1,0);
			//$this->pdf->Cell(120,6,$d['vendor_jual'].': '.$d['an_jual'].', '.$d['jabatan_jual'],1,0);
			//$this->pdf->Cell(105,6,$d['alamat_jual'],1,0);
			$this->pdf->Cell(25,6,number_format($d['total_jual']),1,1,'R');
			$total += ($d['total_jual']);
			$n++;
		}
		$this->pdf->SetFont('Times','B',11);
		$this->pdf->Cell(252,6,'JUMLAH TOTAL',1,0,'C');
		$this->pdf->Cell(25,6,number_format($total),1,0,'R');

		$this->pdf->Ln(10);
		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(157,7,'',0,0,'C');
		$this->pdf->Cell(95,7,'Wiradesa, '.$this->tanggal_indo($tanggal),0,1,'C');
		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(95,7,'Mengetahui',0,0,'C');
		$this->pdf->Cell(62,7,'',0,0);
		$this->pdf->Cell(95,7,'Dibuat',0,1,'C');
		$this->pdf->Cell(95,7,'Pemilik',0,0,'C');
		$this->pdf->Cell(62,7,'',0,0);
		$this->pdf->Cell(95,7,'Bagian Penjualan',0,1,'C');
		$this->pdf->Ln(20);
		$this->pdf->SetFont('Times','BU',12);
		$this->pdf->Cell(95,7,'Ardhan',0,0,'C');
		$this->pdf->Cell(62,7,'',0,0);
		$this->pdf->Cell(95,5,$pengguna,0,1,'C');

		$this->pdf->Output("Laporan Penjualan (".$periode.").pdf", 'D');
		//$this->pdf->Output();
		//exit;
	}

	function tanggal_indo($tanggal, $cetak_hari = false){
		$bulan = array (1 =>   'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		$split    = explode('-', $tanggal);
		$tgl_indo = $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
		return $tgl_indo;
	}
}
?>