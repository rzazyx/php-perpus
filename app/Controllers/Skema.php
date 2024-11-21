<?php
namespace App\Controllers;
use CodeIgniter\Config\Services;
use App\Models\Databasemodel;
class Skema extends BaseController{
	function __construct(){
		$this->mod = new Databasemodel();
		$this->db = db_connect();
	}

	public function index(){
		if(session()->get('admin')){
			return redirect()->to(base_url('admin'));
		}else if(session()->get('keu')){
			session()->setFlashdata('gagal','');
			$data['data'] = $this->mod->getAll("tbl_akun");
			return view('keu/skema',$data);
		}else if(session()->get('pen')){
			return redirect()->to(base_url('pen'));
		}else{
			return redirect()->to(base_url(''));
		}
	}

	public function changedata(){
		$get = $this->request->getPost();
		$tr = ["tunai","kredit","cicil"];
		for ($i=0; $i < count($tr); $i++) {
			$x = "tr".$i;
			if($get[$x] == ''){
				$this->mod->deleting("tbl_skema",['id_akun' => $get['kode'], 'proses_skema' => $tr[$i]]);
			}else{
				$cek = $this->mod->getSome("tbl_skema",['id_akun' => $get['kode'], 'proses_skema' => $tr[$i]]);
				if(count($cek) > 0){
					$kode = $this->mod->getData("tbl_skema",['id_akun' => $get['kode'], 'proses_skema' => $tr[$i]])['id_skema'];
					$this->mod->updating("tbl_skema",['transaksi_skema' => $get[$x]],['id_skema' => $kode]);
				}else{
					$data = array(
						'id_skema' => null,
						'proses_skema' => $tr[$i],
						'transaksi_skema' => $get[$x],
						'id_akun' => $get['kode']
					);
					$this->mod->inserting("tbl_skema",$data);
				}
			}
		}
		session()->setFlashdata('gagal','simpan');
		$data['data'] = $this->mod->getAll("tbl_akun");
			return view('keu/skema',$data);
	}
}
?>