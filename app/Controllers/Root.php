<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
use App\Libraries\Fpdf\Fpdf;
class Root extends BaseController{
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
			$this->verifikasi();
			session()->setFlashdata('gagal','');
			return view('landing');
		}
	}

	public function verifikasi(){
		$data = $this->db->query("select * from anggota where kelas not in ('Staf')")->getResultArray();
		$batas = date('Y');
		foreach ($data as $d) {
			$x = substr($d['kodeanggota'], 0, 4);
			$x = $batas - $x;
			if($x > 3){
				$this->mod->updating('anggota',['status' => '0'],['kodeanggota' => $d['kodeanggota']]);
			}
		}
	}

	public function tampilkunjungan(){
		session()->setFlashdata('gagal','');
		session()->setFlashdata('berhasil','');
		$data['hasil'] = [];
		return view('kunjungan',$data);
	}

	public function simpankunjungan(){
		$get = $this->request->getPost();
		$data = array(
			'kodekunjungan' => null,
			'waktu' => date('Y-m-d H:i:s'),
			'nama' => $get['nama'],
			'kelas' => $get['kelas']
		);
		$this->mod->inserting('kunjungan',$data);
		session()->setFlashdata('gagal','');
		session()->setFlashdata('berhasil','Terima Kasih atas Kunjungannya!');
		$data['hasil'] = [];
		$data['x'] = '';
		return view('kunjungan',$data);
	}

	public function caripustaka(){
		$get = $this->request->getPost();
		session()->setFlashdata('gagal','Menampilkan Hasil');
		session()->setFlashdata('berhasil','');
		$data['hasil'] = $this->db->query("select pustaka.*, pengarang.*, rak.rak from pustaka join pengarang on pustaka.kodepustaka = pengarang.kodepustaka join rak on pustaka.koderak = rak.koderak where pustaka.judul like '%".$get['cari']."%' or pengarang.depan like '%".$get['cari']."%' or pengarang.tengah like '%".$get['cari']."%' or pengarang.belakang like '%".$get['cari']."%' group by pustaka.kodepustaka asc")->getResultArray();
		$data['x'] = $get['cari'];
		return view('kunjungan',$data);
	}

	public function login(){
		$get = $this->request->getPost();
		$username = $get['username'];
		$password = $get['password'];
		$cek = $this->mod->getSome('petugas',['username' => $username, 'password' => md5($password)]);
		if(count($cek) > 0){
			$cek = $this->mod->getData('petugas',['username' => $username, 'password' => md5($password)]);
			if($cek['status'] == '0'){
				session()->setFlashdata('gagal','Akun tidak dapat diakses!');
				return view('landing');
			}else{
				if($cek['level'] == '0'){
					session()->set('admin',$cek['kodepetugas']);
				}else{
					session()->set('petugas',$cek['kodepetugas']);
				}
				return redirect()->to(base_url(''));
			}
		}else{
			$cek = $this->mod->getSome('anggota',['username' => $username, 'password' => md5($password)]);
			if(count($cek) > 0){
				$cek = $this->mod->getData('anggota',['username' => $username, 'password' => md5($password)]);
				if($cek['status'] == '0'){
					session()->setFlashdata('gagal','Akun tidak dapat diakses!');
					return view('landing');
				}else{
					session()->set('anggota',$cek['kodeanggota']);
					return redirect()->to(base_url(''));
				}
			}else{
				session()->setFlashdata('gagal','Akun tidak ditemukan atau Kombinasi tidak sesuai!');
				return view('landing');
			}
		}
	}

	public function logout(){
		session_unset();
		session()->destroy();
		return redirect()->to(base_url(''));
	}


	// ============================================================ CETAK LAPORAN
	public function cetakpustaka(){
		$get = $this->request->getPost();
		if($get['jenis'] == 'detail'){
			$this->prosescetakdetailpustaka();
		}else if($get['jenis'] == 'pinjam'){
			$this->prosescetakpinjam($get['periode'],$get['bulan']);
		}else{
			$this->prosescetakstatus($get['periode'],$get['bulan']);
		}
	}

	public function prosescetakpinjam($x,$y){
		$bulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$klasifikasi = ['000 (Komputer, Informasi dan Referensi Umum)','100 (Filsafat dan Psikologi)','200 (Agama)','300 (Ilmu Sosial)','400 (Bahasa)','500 (Sains dan Matematika)','600 (Teknologi)','700 (Kesenian dan Rekreasi)','800 (Sastra)','900 (Sejarah dan Geografi)'];
		$indeks = ['000','100','200','300','400','500','600','700','800','900'];
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('P','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(190,5,'DAFTAR BUKU YANG DIPINJAM',0,1,'C');
		$this->pdf->Cell(190,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(190,5,strtoupper($bulan[$y]).' '.$x,0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,5,'No',1,0,'C');
		$this->pdf->Cell(166,5,'Klasifikasi',1,0,'C');
		$this->pdf->Cell(17,5,'Jumlah',1,1,'C');

		$this->pdf->SetFont('Times','',8);
		$n = 1;
		for ($i=0; $i < count($klasifikasi) ; $i++) {
			$this->pdf->Cell(7,5,$n++,1,0,'C');
			$this->pdf->Cell(166,5,$klasifikasi[$i],1,0);
			$reff = $this->mod->getSome('klasifikasi',['reff' => $indeks[$i]]);
			$jumlah = 0;
			$jumlah = $this->db->query("select ifnull(sum(detailtransaksi.jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi join pustaka on detailtransaksi.kodepustaka = pustaka.kodepustaka where month(transaksi.waktu) = '".$y."' and year(transaksi.waktu) = '".$x."' and detailtransaksi.status = '7' and pustaka.kodeklasifikasi = '".$indeks[$i]."'")->getRowArray()['jumlah'];
			foreach ($reff as $r) {
				$isi = $this->db->query("select ifnull(sum(detailtransaksi.jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi join pustaka on detailtransaksi.kodepustaka = pustaka.kodepustaka where month(transaksi.waktu) = '".$y."' and year(transaksi.waktu) = '".$x."' and detailtransaksi.status = '7' and pustaka.kodeklasifikasi = '".$r['kodeklasifikasi']."'")->getRowArray()['jumlah'];
				$jumlah += $isi;
			}
			$this->pdf->Cell(17,5,number_format($jumlah),1,1,'C');
		}
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(173,5,'Jumlah',1,0,'C');
		$jumlah = 0;
		$jumlah = $this->db->query("select ifnull(sum(detailtransaksi.jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi join pustaka on detailtransaksi.kodepustaka = pustaka.kodepustaka where month(transaksi.waktu) = '".$y."' and year(transaksi.waktu) = '".$x."' and detailtransaksi.status = '7'")->getRowArray()['jumlah'];
		$this->pdf->Cell(17,5,number_format($jumlah),1,1,'C');

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(40,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(90,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(40,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(90,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(40,5,'',0,0,'C');
		$this->pdf->Cell(60,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(40,5,'',0,0,'C');
		$this->pdf->Cell(60,5,"NIP.".$p2['nip'],0,1);

		$this->pdf->Output();
		exit;
	}

	public function prosescetakstatus($x,$y){
		$bulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$klasifikasi = ['000 (Komputer, Informasi dan Referensi Umum)','100 (Filsafat dan Psikologi)','200 (Agama)','300 (Ilmu Sosial)','400 (Bahasa)','500 (Sains dan Matematika)','600 (Teknologi)','700 (Kesenian dan Rekreasi)','800 (Sastra)','900 (Sejarah dan Geografi)'];
		$indeks = ['000','100','200','300','400','500','600','700','800','900'];
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('P','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(190,5,'STATUS PUSTAKA',0,1,'C');
		$this->pdf->Cell(190,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(190,5,strtoupper($bulan[$y]).' '.$x,0,1,'C');
		$this->pdf->Ln(5);

		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,10,'No',1,0,'C');
		$this->pdf->Cell(103,10,'Klasifikasi',1,0,'C');
		$this->pdf->Cell(20,5,'Penambahan',1,0,'C');
		$this->pdf->Cell(20,5,'Pengurangan',1,0,'C');
		$this->pdf->Cell(20,5,'Hilang',1,0,'C');
		$this->pdf->Cell(20,5,'Rusak',1,1,'C');

		$this->pdf->Cell(110,5,'',0,0,'C');
		$this->pdf->Cell(10,5,'J',1,0,'C');
		$this->pdf->Cell(10,5,'E',1,0,'C');
		$this->pdf->Cell(10,5,'J',1,0,'C');
		$this->pdf->Cell(10,5,'E',1,0,'C');
		$this->pdf->Cell(10,5,'J',1,0,'C');
		$this->pdf->Cell(10,5,'E',1,0,'C');
		$this->pdf->Cell(10,5,'J',1,0,'C');
		$this->pdf->Cell(10,5,'E',1,1,'C');

		$this->pdf->SetFont('Times','',9);
		$n = 1;
		for ($j=0; $j < count($klasifikasi) ; $j++) {
			$this->pdf->Cell(7,6,$n++,1,0,'C');
			$this->pdf->Cell(103,6,$klasifikasi[$j],1,0);

			$data = $this->db->query("select ifnull(count(*),0) as jumlah from rekap join pustaka on rekap.kodepustaka = pustaka.kodepustaka join klasifikasi on pustaka.kodeklasifikasi = klasifikasi.kodeklasifikasi where month(rekap.waktu) = '".$y."' and year(rekap.waktu) = '".$x."' and (klasifikasi.kodeklasifikasi = '".$indeks[$j]."' or klasifikasi.reff = '".$indeks[$j]."') group by rekap.kodepustaka")->getResultArray();
			$judul = count($data);
			$eksemplar = 0;
			foreach ($data as $d) {
				$eksemplar += $d['jumlah'];
			}
			$this->pdf->Cell(10,6,number_format($judul),1,0,'C');
			$this->pdf->Cell(10,6,number_format($eksemplar),1,0,'C');

			$judul = 0;
			$eksemplar = 0;
			$this->pdf->Cell(10,6,number_format($judul),1,0,'C');
			$this->pdf->Cell(10,6,number_format($eksemplar),1,0,'C');

			$judul = 0;
			$eksemplar = 0;
			$this->pdf->Cell(10,6,number_format($judul),1,0,'C');
			$this->pdf->Cell(10,6,number_format($eksemplar),1,0,'C');

			$judul = 0;
			$eksemplar = 0;
			$this->pdf->Cell(10,6,number_format($judul),1,0,'C');
			$this->pdf->Cell(10,6,number_format($eksemplar),1,1,'C');
		}

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(60,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(60,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(60,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(60,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}

	public function prosescetakpeminjaman(){
		$get = $this->request->getPost();
		$daftarbulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$periode = $get['periode'];
		$bulan = $get['bulan'];
		$data = $this->db->query("select * from transaksi where jenis = 'keluar' and kodeanggota not in ('0') and month(waktu) = '".$bulan."' and year(waktu) = '".$periode."' order by waktu asc")->getResultArray();
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('L','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(277,5,'LAPORAN DATA PEMINJAMAN PUSTAKA',0,1,'C');
		$this->pdf->Cell(277,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(277,5,strtoupper($daftarbulan[$bulan]).' '.$periode,0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,5,'No',1,0,'C');
		$this->pdf->Cell(31,5,'Kode',1,0,'C');
		$this->pdf->Cell(40,5,'Peminjam',1,0,'C');
		$this->pdf->Cell(20,5,'Tgl. Pinjam',1,0,'C');
		$this->pdf->Cell(20,5,'Tgl. Kembali',1,0,'C');
		$this->pdf->Cell(117,5,'Keterangan',1,0,'C');
		$this->pdf->Cell(27,5,'Telat',1,0,'C');
		$this->pdf->Cell(15,5,'Status',1,1,'C');

		if(count($data) > 0){
			$n = 1;
			$this->pdf->SetFont('Times','',9);
			foreach ($data as $d) {
				$status = "Selesai";
				if($d['status'] == '1'){
					$status = "Dipinjam";
				}
				$k = $this->mod->getData('denda',['kodetransaksi' => $d['kodetransaksi']]);
				$a = $this->mod->getData('anggota',['kodeanggota' => $d['kodeanggota']])['nama'];
				$this->pdf->Cell(7,6,$n++,1,0,'C');
				$this->pdf->Cell(31,6,$d['kodetransaksi'],1,0,'C');
				$this->pdf->Cell(40,6,$a,1,0);
				$this->pdf->Cell(20,6,date('d/m/Y', strtotime($k['tglpinjam'])),1,0,'C');
				$this->pdf->Cell(20,6,date('d/m/Y', strtotime($k['tglkembali'])),1,0,'C');
				$this->pdf->Cell(117,6,$d['keterangan'],1,0);
				$this->pdf->Cell(27,6,$k['telat']." hari (Rp".number_format($k['total']).")",1,0,'R');
				$this->pdf->Cell(15,6,$status,1,1,'C');
			}
		}else{
			$this->pdf->SetFont('Times','I',9);
			$this->pdf->Cell(277,6,'Belum ada data.....',1,1,'C');
		}

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(90,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(90,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}

	public function prosescetakmutasi(){
		$get = $this->request->getPost();
		$daftarbulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$periode = $get['periode'];
		$bulan = $get['bulan'];
		$data = $this->db->query("select * from transaksi where kodeanggota = '0' and month(waktu) = '".$bulan."' and year(waktu) = '".$periode."' order by waktu asc")->getResultArray();
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('L','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(277,5,'LAPORAN DATA MUTASI PUSTAKA',0,1,'C');
		$this->pdf->Cell(277,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(277,5,strtoupper($daftarbulan[$bulan]).' '.$periode,0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,5,'No',1,0,'C');
		$this->pdf->Cell(31,5,'Kode',1,0,'C');
		$this->pdf->Cell(20,5,'Jenis',1,0,'C');
		$this->pdf->Cell(20,5,'Tanggal',1,0,'C');
		$this->pdf->Cell(50,5,'Pustaka',1,0,'C');
		$this->pdf->Cell(149,5,'Keterangan',1,1,'C');

		if(count($data) > 0){
			$n = 1;
			$this->pdf->SetFont('Times','',9);
			foreach ($data as $d) {
				$tit = $this->db->query("select count(*) as jumlah from detailtransaksi where kodetransaksi = '".$d['kodetransaksi']."'")->getRowArray()['jumlah'];
				$eks = $this->db->query("select sum(jumlah) as jumlah from detailtransaksi where kodetransaksi = '".$d['kodetransaksi']."'")->getRowArray()['jumlah'];
				$keterangan = number_format($tit).' Pustaka, '.number_format($eks).' Eksemplar';
				$this->pdf->Cell(7,5,$n++,1,0,'C');
				$this->pdf->Cell(31,5,$d['kodetransaksi'],1,0,'C');
				$this->pdf->Cell(20,5,strtoupper($d['jenis']),1,0,'C');
				$this->pdf->Cell(20,5,date('d/m/Y', strtotime($d['waktu'])),1,0,'C');
				$this->pdf->Cell(50,5,number_format($tit).' Pustaka, '.number_format($eks).' Eksemplar',1,0);
				$this->pdf->Cell(149,5,$d['keterangan'],1,1);
			}
		}else{
			$this->pdf->SetFont('Times','I',9);
			$this->pdf->Cell(277,6,'Belum ada data.....',1,1,'C');
		}

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(90,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(90,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}

	public function prosescetakanggota(){
		$get = $this->request->getPost();
		$tahun = $get['periode'];
		$bulan = (int)$get['bulan'];

		$t = $tahun;
		$b = $bulan;
		if($b < 10){
			$b = "0".$b;
		}

		$daftarbulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$data = $this->db->query("select * from anggota where kodeanggota like '".$t."%' and kodeanggota like '%".$b."' order by nama asc")->getResultArray();

		$this->pdf = new fpdf('P','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(190,5,'DAFTAR ANGGOTA',0,1,'C');
		$this->pdf->Cell(190,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(190,5,strtoupper($daftarbulan[$bulan]).' '.$tahun,0,1,'C');
		$this->pdf->Ln(5);

		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,5,'No',1,0,'C');
		$this->pdf->Cell(20,5,'Kode',1,0,'C');
		$this->pdf->Cell(20,5,'NISN',1,0,'C');
		$this->pdf->Cell(83,5,'Nama',1,0,'C');
		$this->pdf->Cell(20,5,'Jekel',1,0,'C');
		$this->pdf->Cell(20,5,'Kelas',1,0,'C');
		$this->pdf->Cell(20,5,'Status',1,1,'C');

		$this->pdf->SetFont('Times','',9);
		$n = 1;
		foreach ($data as $d) {
			$status = 'Aktif';
			if($d['status'] == '0'){
				$status = "Nonaktif";
			}
			$this->pdf->Cell(7,5,$n++,1,0,'C');
			$this->pdf->Cell(20,5,$d['kodeanggota'],1,0,'C');
			$this->pdf->Cell(20,5,$d['nisn'],1,0,'C');
			$this->pdf->Cell(83,5,$d['nama'],1,0);
			$this->pdf->Cell(20,5,$d['jekel'],1,0,'C');
			$this->pdf->Cell(20,5,$d['kelas'],1,0,'C');
			$this->pdf->Cell(20,5,$status,1,1,'C');
		}

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(60,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(60,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(60,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(60,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}

	public function prosescetakdetailpustaka(){
		$get = $this->request->getPost();
		$data = $this->db->query("select pustaka.*, atribut.* from pustaka join atribut on pustaka.kodepustaka = atribut.kodepustaka")->getResultArray();
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('P','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(190,5,'DAFTAR PUSTAKA',0,1,'C');
		$this->pdf->Cell(190,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Ln(10);

		foreach ($data as $d) {
			$pengarang = $this->mod->getSome('pengarang',['kodepustaka' => $d['kodepustaka']]);
			$at1 = $this->mod->getData('klasifikasi',['kodeklasifikasi' => $d['kodeklasifikasi']]);
			$at2 = $this->mod->getData('rak',['koderak' => $d['koderak']]);
			$pg = "";
			foreach ($pengarang as $p) {
				$pg .= $p['depan'].' '.$p['tengah'].' '.$p['belakang'].', ';
			}
			$pg = substr($pg, 0, strlen($pg)-2);


			$this->pdf->SetFont('Times','B',9);
			$this->pdf->Cell(190,5,$d['kodepustaka'],'T',1);
			$this->pdf->SetFont('Times','I',8);
			$this->pdf->MultiCell(190,4,$d['judul'],0,'J');
			$this->pdf->SetFont('Times','',8);
			$this->pdf->MultiCell(190,4,$pg,0,'J');
			$this->pdf->Cell(190,4,$at1['kodeklasifikasi'].' '.$at1['klasifikasi'].', Baris '.$d['baris'].' '.$at2['rak'],0,1);
			$this->pdf->Cell(190,4,$d['kota'].' : '.$d['penerbit'].', '.$d['bulan'].' '.$d['tahun'].', Volume : '.$d['volume'],0,1);
			$this->pdf->Cell(190,4,$d['ns'].', Bahasa '.$d['bahasa'],0,1);
			$this->pdf->Cell(190,4,$d['halaman'].' hlm. '.$d['ilustrasi'].' ilust. '.$d['romawi'].' romawi','B',1);
		}

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(60,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(60,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(60,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(60,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}


	public function prosescetakpinjams($x){
		$bulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$klasifikasi = ['000 (Komputer, Informasi dan Referensi Umum)','100 (Filsafat dan Psikologi)','200 (Agama)','300 (Ilmu Sosial)','400 (Bahasa)','500 (Sains dan Matematika)','600 (Teknologi)','700 (Kesenian dan Rekreasi)','800 (Sastra)','900 (Sejarah dan Geografi)'];
		$indeks = ['000','100','200','300','400','500','600','700','800','900'];
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('L','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(277,5,'DAFTAR BUKU YANG DIPINJAM',0,1,'C');
		$this->pdf->Cell(277,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(277,5,'TAHUN : '.$x,0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,10,'No',1,0,'C');
		$this->pdf->Cell(66,10,'Klasifikasi',1,0,'C');
		$this->pdf->Cell(204,5,'Bulan',1,1,'C');

		$this->pdf->Cell(73,5,'',0,0,'C');
		for ($i=1; $i <= 12 ; $i++) {
			if($i == 12){
				$this->pdf->Cell(17,5,$bulan[$i],1,1,'C');
			}else{
				$this->pdf->Cell(17,5,$bulan[$i],1,0,'C');
			}
		}

		$this->pdf->SetFont('Times','',8);
		$n = 1;
		for ($i=0; $i < count($klasifikasi) ; $i++) {
			$this->pdf->Cell(7,5,$n++,1,0,'C');
			$this->pdf->Cell(66,5,$klasifikasi[$i],1,0);
			for ($j=1; $j <= 12 ; $j++) {
				$reff = $this->mod->getSome('klasifikasi',['reff' => $indeks[$i]]);
				$jumlah = 0;
				$jumlah = $this->db->query("select ifnull(sum(detailtransaksi.jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi join pustaka on detailtransaksi.kodepustaka = pustaka.kodepustaka where month(transaksi.waktu) = '".$j."' and year(transaksi.waktu) = '".$x."' and detailtransaksi.status = '7' and pustaka.kodeklasifikasi = '".$indeks[$i]."'")->getRowArray()['jumlah'];
				foreach ($reff as $r) {
					$isi = $this->db->query("select ifnull(sum(detailtransaksi.jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi join pustaka on detailtransaksi.kodepustaka = pustaka.kodepustaka where month(transaksi.waktu) = '".$j."' and year(transaksi.waktu) = '".$x."' and detailtransaksi.status = '7' and pustaka.kodeklasifikasi = '".$r['kodeklasifikasi']."'")->getRowArray()['jumlah'];
					$jumlah += $isi;
				}
				if($j == 12){
					$this->pdf->Cell(17,5,number_format($jumlah),1,1,'C');
				}else{
					$this->pdf->Cell(17,5,number_format($jumlah),1,0,'C');
				}
			}
		}
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(73,5,'Jumlah',1,0,'C');
		for ($i=1; $i <= 12 ; $i++) {
			$jumlah = 0;
			$jumlah = $this->db->query("select ifnull(sum(detailtransaksi.jumlah),0) as jumlah from detailtransaksi join transaksi on detailtransaksi.kodetransaksi = transaksi.kodetransaksi join pustaka on detailtransaksi.kodepustaka = pustaka.kodepustaka where month(transaksi.waktu) = '".$i."' and year(transaksi.waktu) = '".$x."' and detailtransaksi.status = '7'")->getRowArray()['jumlah'];
			if($i == 12){
				$this->pdf->Cell(17,5,number_format($jumlah),1,1,'C');
			}else{
				$this->pdf->Cell(17,5,number_format($jumlah),1,0,'C');
			}
		}

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(90,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(90,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,"NIP.".$p2['nip'],0,1);

		$this->pdf->Output();
		exit;
	}

	public function prosescetakstatuss($x){
		$bulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$klasifikasi = ['000 (Komputer, Informasi dan Referensi Umum)','100 (Filsafat dan Psikologi)','200 (Agama)','300 (Ilmu Sosial)','400 (Bahasa)','500 (Sains dan Matematika)','600 (Teknologi)','700 (Kesenian dan Rekreasi)','800 (Sastra)','900 (Sejarah dan Geografi)'];
		$indeks = ['000','100','200','300','400','500','600','700','800','900'];
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('P','mm','A4');
		for ($i=1; $i <= 12 ; $i++) {
			$this->pdf->AddPage();
			$this->pdf->SetFont('Times','B',12);
			$this->pdf->Cell(190,5,'STATUS PUSTAKA',0,1,'C');
			$this->pdf->Cell(190,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
			$this->pdf->Cell(190,5,'BULAN '.strtoupper($bulan[$i]),0,1,'C');
			$this->pdf->Cell(190,5,'TAHUN : '.$x,0,1,'C');
			$this->pdf->Ln(5);

			$this->pdf->SetFont('Times','B',9);
			$this->pdf->Cell(7,10,'No',1,0,'C');
			$this->pdf->Cell(103,10,'Klasifikasi',1,0,'C');
			$this->pdf->Cell(20,5,'Penambahan',1,0,'C');
			$this->pdf->Cell(20,5,'Pengurangan',1,0,'C');
			$this->pdf->Cell(20,5,'Hilang',1,0,'C');
			$this->pdf->Cell(20,5,'Rusak',1,1,'C');

			$this->pdf->Cell(110,5,'',0,0,'C');
			$this->pdf->Cell(10,5,'J',1,0,'C');
			$this->pdf->Cell(10,5,'E',1,0,'C');
			$this->pdf->Cell(10,5,'J',1,0,'C');
			$this->pdf->Cell(10,5,'E',1,0,'C');
			$this->pdf->Cell(10,5,'J',1,0,'C');
			$this->pdf->Cell(10,5,'E',1,0,'C');
			$this->pdf->Cell(10,5,'J',1,0,'C');
			$this->pdf->Cell(10,5,'E',1,1,'C');

			$this->pdf->SetFont('Times','',9);
			$n = 1;
			for ($j=0; $j < count($klasifikasi) ; $j++) {
				$this->pdf->Cell(7,6,$n++,1,0,'C');
				$this->pdf->Cell(103,6,$klasifikasi[$j],1,0);

				$data = $this->db->query("select ifnull(count(*),0) as jumlah from rekap join pustaka on rekap.kodepustaka = pustaka.kodepustaka join klasifikasi on pustaka.kodeklasifikasi = klasifikasi.kodeklasifikasi where month(rekap.waktu) = '".$i."' and year(rekap.waktu) = '".$x."' and (klasifikasi.kodeklasifikasi = '".$indeks[$j]."' or klasifikasi.reff = '".$indeks[$j]."') group by rekap.kodepustaka")->getResultArray();
				$judul = count($data);
				$eksemplar = 0;
				foreach ($data as $d) {
					$eksemplar += $d['jumlah'];
				}
				$this->pdf->Cell(10,6,number_format($judul),1,0,'C');
				$this->pdf->Cell(10,6,number_format($eksemplar),1,0,'C');

				$judul = 0;
				$eksemplar = 0;
				$this->pdf->Cell(10,6,number_format($judul),1,0,'C');
				$this->pdf->Cell(10,6,number_format($eksemplar),1,0,'C');

				$judul = 0;
				$eksemplar = 0;
				$this->pdf->Cell(10,6,number_format($judul),1,0,'C');
				$this->pdf->Cell(10,6,number_format($eksemplar),1,0,'C');

				$judul = 0;
				$eksemplar = 0;
				$this->pdf->Cell(10,6,number_format($judul),1,0,'C');
				$this->pdf->Cell(10,6,number_format($eksemplar),1,1,'C');
			}

			$this->pdf->Ln(5);
			$this->pdf->SetFont('Times','',10);
			$this->pdf->Cell(60,5,'Mengetahui',0,0,'C');
			$this->pdf->Cell(70,5,'',0,0,'C');
			$this->pdf->Cell(60,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

			$this->pdf->Cell(60,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
			$this->pdf->Cell(70,5,'',0,0,'C');
			$this->pdf->Cell(60,5,'Petugas Perpustakaan',0,1);

			$this->pdf->Ln(15);
			$this->pdf->SetFont('Times','BU',10);
			$this->pdf->Cell(60,5,$p1['kepsek'],0,0,'C');
			$this->pdf->Cell(70,5,'',0,0,'C');
			$this->pdf->Cell(60,5,$p2['nama'],0,1);

			$this->pdf->SetFont('Times','',10);
			$this->pdf->Cell(60,5,"NIP.".$p1['nipkepsek'],0,0,'C');
			$this->pdf->Cell(70,5,'',0,0,'C');
			$this->pdf->Cell(60,5,"NIP.".$p2['nip'],0,1);
		}
		
		$this->pdf->Output();
		exit;
	}

	public function prosescetakpeminjamans(){
		$get = $this->request->getPost();
		$periode = $get['periode'];
		$data = $this->db->query("select * from transaksi where jenis = 'keluar' and kodeanggota not in ('0') and year(waktu) = '".$periode."' order by waktu asc")->getResultArray();
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('L','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(277,5,'LAPORAN DATA PEMINJAMAN PUSTAKA',0,1,'C');
		$this->pdf->Cell(277,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(277,5,'TAHUN : '.$periode,0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,5,'No',1,0,'C');
		$this->pdf->Cell(31,5,'Kode',1,0,'C');
		$this->pdf->Cell(40,5,'Peminjam',1,0,'C');
		$this->pdf->Cell(20,5,'Tgl. Pinjam',1,0,'C');
		$this->pdf->Cell(20,5,'Tgl. Kembali',1,0,'C');
		$this->pdf->Cell(117,5,'Keterangan',1,0,'C');
		$this->pdf->Cell(27,5,'Telat',1,0,'C');
		$this->pdf->Cell(15,5,'Status',1,1,'C');

		if(count($data) > 0){
			$n = 1;
			$this->pdf->SetFont('Times','',9);
			foreach ($data as $d) {
				$status = "Selesai";
				if($d['status'] == '1'){
					$status = "Dipinjam";
				}
				$k = $this->mod->getData('denda',['kodetransaksi' => $d['kodetransaksi']]);
				$a = $this->mod->getData('anggota',['kodeanggota' => $d['kodeanggota']])['nama'];
				$this->pdf->Cell(7,6,$n++,1,0,'C');
				$this->pdf->Cell(31,6,$d['kodetransaksi'],1,0,'C');
				$this->pdf->Cell(40,6,$a,1,0);
				$this->pdf->Cell(20,6,date('d/m/Y', strtotime($k['tglpinjam'])),1,0,'C');
				$this->pdf->Cell(20,6,date('d/m/Y', strtotime($k['tglkembali'])),1,0,'C');
				$this->pdf->Cell(117,6,$d['keterangan'],1,0);
				$this->pdf->Cell(27,6,$k['telat']." hari (Rp".number_format($k['total']).")",1,0,'R');
				$this->pdf->Cell(15,6,$status,1,1,'C');
			}
		}else{
			$this->pdf->SetFont('Times','I',9);
			$this->pdf->Cell(277,6,'Belum ada data.....',1,1,'C');
		}

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(90,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(90,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}

	public function prosescetakmutasis(){
		$get = $this->request->getPost();
		$periode = $get['periode'];
		$data = $this->db->query("select * from transaksi where kodeanggota = '0' and year(waktu) = '".$periode."' order by waktu asc")->getResultArray();
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('L','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(277,5,'LAPORAN DATA MUTASI PUSTAKA',0,1,'C');
		$this->pdf->Cell(277,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(277,5,'TAHUN : '.$periode,0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,5,'No',1,0,'C');
		$this->pdf->Cell(31,5,'Kode',1,0,'C');
		$this->pdf->Cell(20,5,'Jenis',1,0,'C');
		$this->pdf->Cell(20,5,'Tanggal',1,0,'C');
		$this->pdf->Cell(50,5,'Pustaka',1,0,'C');
		$this->pdf->Cell(149,5,'Keterangan',1,1,'C');

		if(count($data) > 0){
			$n = 1;
			$this->pdf->SetFont('Times','',9);
			foreach ($data as $d) {
				$tit = $this->db->query("select count(*) as jumlah from detailtransaksi where kodetransaksi = '".$d['kodetransaksi']."'")->getRowArray()['jumlah'];
				$eks = $this->db->query("select sum(jumlah) as jumlah from detailtransaksi where kodetransaksi = '".$d['kodetransaksi']."'")->getRowArray()['jumlah'];
				$keterangan = number_format($tit).' Pustaka, '.number_format($eks).' Eksemplar';
				$this->pdf->Cell(7,5,$n++,1,0,'C');
				$this->pdf->Cell(31,5,$d['kodetransaksi'],1,0,'C');
				$this->pdf->Cell(20,5,strtoupper($d['jenis']),1,0,'C');
				$this->pdf->Cell(20,5,date('d/m/Y', strtotime($d['waktu'])),1,0,'C');
				$this->pdf->Cell(50,5,number_format($tit).' Pustaka, '.number_format($eks).' Eksemplar',1,0);
				$this->pdf->Cell(149,5,$d['keterangan'],1,1);
			}
		}else{
			$this->pdf->SetFont('Times','I',9);
			$this->pdf->Cell(277,6,'Belum ada data.....',1,1,'C');
		}

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(90,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(90,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}

	public function prosescetakkunjungan($x){
		$get = $this->request->getPost();
		$hari = 31;
		$bulan = [1 => 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
		$p1 = $this->db->query("select * from infosistem")->getRowArray();
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);

		$this->pdf = new fpdf('L','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetFont('Times','B',12);
		$this->pdf->Cell(277,5,'LAPORAN DATA KUNJUNGAN',0,1,'C');
		$this->pdf->Cell(277,5,'PERPUSTAKAAN SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->Cell(277,5,'TAHUN : '.$x,0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Times','B',9);
		$this->pdf->Cell(7,10,'No',1,0,'C');
		$this->pdf->Cell(15,10,'Bulan',1,0,'C');
		$this->pdf->Cell(231,5,'Tanggal',1,0,'C');
		$this->pdf->Cell(12,10,'Jml.',1,0,'C');
		$this->pdf->Cell(12,10,'%',1,0,'C');
		$this->pdf->Cell(1,5,'',0,1,'C');

		$this->pdf->Cell(22,5,'',0,0,'C');
		for ($i=1; $i <= 31 ; $i++) { 
			$this->pdf->Cell(231/31,5,$i,1,0,'C');
		}
		$this->pdf->Cell(12,5,'',0,0,'C');
		$this->pdf->Cell(12,5,'',0,1,'C');

		$this->pdf->SetFont('Times','',8);
		for ($i=1; $i <= 12 ; $i++) {
			$rata = 0;
			if($i == 2){
				$hari = 28;
				if(($x % 4) == 0){
					$hari = 29;
				}
			}
			if($i == 4 || $i == 6 || $i == 9 || $i == 11){
				$hari = 30;
			}
			$rata = $this->db->query("select ifnull(count(*),0) as total from kunjungan where month(waktu) = '".$i."' and year(waktu) = '".$x."'")->getRowArray()['total'];
			if($rata > 0){
				$rata = ceil($rata/$hari);
			}
			$persen = 0;
			$total = 0;
			$this->pdf->Cell(7,5,$i,1,0,'C');
			$this->pdf->Cell(15,5,$bulan[$i],1,0);
			for ($j=1; $j <= 31 ; $j++) {
				$p = 0;
				$b = $i;
				$t = $j;
				if($b < 10){
					$b = "0".$b;
				}
				if($t < 10){
					$t = "0".$t;
				}
				$tanggal = $x.'-'.$b.'-'.$t;
				$jumlah = $this->db->query("select ifnull(count(*),0) as jumlah from kunjungan where date(waktu) = '".$tanggal."'")->getRowArray()['jumlah'];
				if($jumlah > 0){
					$p = number_format(($jumlah/$rata)*100);
				}
				$persen += $p;
				$total += $jumlah;
				$this->pdf->Cell(231/31,5,$p,1,0,'C');
			}
			if($persen > 0){
				$persen = $persen/$hari;
			}
			$this->pdf->Cell(12,5,number_format($total),1,0,'C');
			$this->pdf->Cell(12,5,number_format($persen).'%',1,1,'C');
		}
		

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(90,5,'Kepala SMP Negeri 03 Tirto',0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',10);
		$this->pdf->Cell(90,5,$p1['kepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',10);
		$this->pdf->Cell(90,5,"NIP.".$p1['nipkepsek'],0,0,'C');
		$this->pdf->Cell(97,5,'',0,0,'C');
		$this->pdf->Cell(90,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}

	public function prosescetaklabel(){
		$get = $this->request->getPost();
		$label = $this->mod->getData('pustaka',['kodepustaka' => $get['pustaka']])['label'];
		$label = explode("|", $label);
		$p = $this->mod->getData('pustaka',['kodepustaka' => $get['pustaka']]);
		$a = $this->mod->getData('atribut',['kodepustaka' => $get['pustaka']]);
		$pg = $this->mod->getSome('pengarang',['kodepustaka' => $get['pustaka']]);

		$this->pdf = new fpdf('P','mm','A4');
		$this->pdf->AddPage();
		for ($i=1; $i <= $get['jumlah'] ; $i++) {
			$pengarang = $label[3];
			if(count($pg) > 1){
				$pengarang = $label[3].', et. all';
			}

			$this->pdf->SetFont('Arial','',12);
			$this->pdf->Cell(190,10,'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -',0,1,'C');

			$this->pdf->SetFont('Arial','B',12);
			$this->pdf->Cell(190,5,'Perpustakaan SMP Negeri 03 Tirto',0,1,'C');


			$this->pdf->Ln(5);

			$this->pdf->Cell(10,5,'',0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(80,5,$label[1],0,0);
			$this->pdf->SetFont('Arial','',12);
			$this->pdf->Cell(10,5,$label[0],0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(80,5,$label[1],0,1,'R');

			$this->pdf->Cell(10,5,'',0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(80,5,$label[3],0,0);
			$this->pdf->SetFont('Arial','',12);
			$this->pdf->Cell(10,5,ucwords(strtolower($label[2])),0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(80,5,$label[3],0,1,'R');

			$this->pdf->Cell(10,5,'',0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(80,5,substr($p['judul'],0,27).'.. / '.$pengarang,0,0);
			$this->pdf->SetFont('Arial','',12);
			$this->pdf->Cell(10,5,$label[0],0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(80,5,substr($p['judul'],0,27).'.. / '.$pengarang,0,1,'R');

			$this->pdf->Cell(10,5,'',0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(80,5,'Vol. '.$a['volume'].', '.$p['kota'].' : '.$p['penerbit'].', '.$p['tahun'],0,0);
			$this->pdf->SetFont('Arial','',12);
			$this->pdf->Cell(10,5,$p['eksemplar'].'.'.substr($p['judul'], 0, 1).'.'.$i,0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(80,5,'Vol. '.$a['volume'].', '.$p['kota'].' : '.$p['penerbit'].', '.$p['tahun'],0,1,'R');

			$this->pdf->Cell(10,5,'',0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(85,5,$a['romawi'].', '.number_format($a['halaman']).' hlm, '.number_format($a['ilustrasi']).' ilus',0,0);
			$this->pdf->Cell(85,5,$a['romawi'].', '.number_format($a['halaman']).' hlm, '.number_format($a['ilustrasi']).' ilus',0,1,'R');

			$this->pdf->Cell(10,5,'',0,0,'C');
			$this->pdf->Cell(85,5,$a['ns'],0,0);
			$this->pdf->Cell(85,5,$a['ns'],0,1,'R');

			if($i % 5 == 0){
				$this->pdf->SetFont('Arial','',12);
				$this->pdf->Cell(190,10,'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -',0,1,'C');
				if($i < $get['jumlah']){
					$this->pdf->AddPage();
				}
			}

			if($i == $get['jumlah'] && $i % 5 != 0){
				$this->pdf->SetFont('Arial','',12);
				$this->pdf->Cell(190,10,'- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -',0,1,'C');
			}
		}
		$this->pdf->Output();
		exit;
	}

	public function prosescetakstbp($x){
		$romawi = [1 => 'I','II','III','IV','V','VI','VII','VIII','IX','XI','XII'];
		$nomor = "";
		$bulan = (int)date('m');
		$p = $this->mod->getData('anggota',['kodeanggota' => $x]);
		$p1 = $this->mod->getData('petugas',['level' => '0']);
		$p2 = $this->mod->getData('petugas',['kodepetugas' => session()->get('petugas')]);
		$nomor = "Nomor : ".substr($p['kodeanggota'],4,5)."/PERPUS.SPENSAKA/".$romawi[$bulan]."/".date('Y');


		$this->pdf = new fpdf('P','mm','A4');
		$this->pdf->AddPage();
		$this->pdf->SetLineWidth(0.2);
		$this->pdf->Line(15,33,195,33);
		$this->pdf->SetLineWidth(0.5);
		$this->pdf->Line(15,34,195,34);
		$this->pdf->Image('../public/assets/gambar/spensaka.png',13,9,22);
		$this->pdf->SetFont('Times','B',14);
		$this->pdf->Cell(190,8,'PERPUSTAKAAN',0,1,'C');
		$this->pdf->SetFont('Times','B',20);
		$this->pdf->Cell(190,10,'SMP NEGERI 03 TIRTO',0,1,'C');
		$this->pdf->SetFont('Times','I',10);
		$this->pdf->Cell(190,5,'Jl. Raya Karangjompo Kec. Tirto, Kabupaten Pekalongan, Jawa Tengah 51151, telp : (0285) 428003',0,1,'C');
		$this->pdf->Ln(5);

		$this->pdf->SetFont('Times','BU',12);
		$this->pdf->Cell(4,5,'',0,0,'C');
		$this->pdf->Cell(182,5,'SURAT KETERANGAN BEBAS PUSTAKA',0,1,'C');

		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(4,5,'',0,0,'C');
		$this->pdf->Cell(182,5,$nomor,0,1,'C');

		$this->pdf->Ln(10);
		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(4,5,'',0,0,'C');
		$this->pdf->Cell(182,5,'Yang bertanda tangan dibawah ini menerangkan bahwa:',0,1);
		$this->pdf->Ln(5);

		$this->pdf->Cell(14,5,'',0,0,'C');
		$this->pdf->Cell(30,5,'NISN',0,0);
		$this->pdf->Cell(142,5,': '.$p['nisn'],0,1);

		$this->pdf->Cell(14,5,'',0,0,'C');
		$this->pdf->Cell(30,5,'No. Anggota',0,0);
		$this->pdf->Cell(142,5,': '.$p['kodeanggota'],0,1);

		$this->pdf->Cell(14,5,'',0,0,'C');
		$this->pdf->Cell(30,5,'Nama',0,0);
		$this->pdf->Cell(142,5,': '.$p['nama'],0,1);

		$this->pdf->Cell(14,5,'',0,0,'C');
		$this->pdf->Cell(30,5,'Jenis Kelamin',0,0);
		$this->pdf->Cell(142,5,': '.$p['jekel'],0,1);

		$this->pdf->Cell(14,5,'',0,0,'C');
		$this->pdf->Cell(30,5,'Kelas',0,0);
		$this->pdf->Cell(142,5,': '.$p['kelas'],0,1);

		$this->pdf->Ln(5);
		$this->pdf->Cell(4,5,'',0,0,'C');
		$this->pdf->MultiCell(182,5,'Siswa tersebut tidak memiliki pinjaman koleksi pustaka milik Perpustakaan SMP Negeri 03 Tirto. Surat keterangan ini dibawa sesuai dengan kebutuhan administratif.',0,'J',0);
		
		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(60,5,'Mengetahui',0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Tirto, '.$this->tanggal_indo(date('Y-m-d')),0,1);

		$this->pdf->Cell(60,5,'Kepala Perpustakaan',0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,'Petugas Perpustakaan',0,1);

		$this->pdf->Ln(15);
		$this->pdf->SetFont('Times','BU',12);
		$this->pdf->Cell(60,5,$p1['nama'],0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,$p2['nama'],0,1);

		$this->pdf->SetFont('Times','',12);
		$this->pdf->Cell(60,5,"NIP.".$p1['nip'],0,0,'C');
		$this->pdf->Cell(70,5,'',0,0,'C');
		$this->pdf->Cell(60,5,"NIP.".$p2['nip'],0,1);
		
		$this->pdf->Output();
		exit;
	}

	function tanggal_indo($tanggal, $cetak_hari = false){
		$bulan = array (1 =>   'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		$split    = explode('-', $tanggal);
		$tgl_indo = $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
		return $tgl_indo;
	}
}
?>