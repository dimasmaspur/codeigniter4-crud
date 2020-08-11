<?php namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController {

    protected $komikModel;

    public function __construct(){
        
        $this->komikModel = new KomikModel();
    }


    public function index(){

        $data = [
            'title' => 'Daftar Komik',
            'komik' => $this->komikModel->getKomik()
        ];



        return view('komik/index',$data);

    }
    
    public function detail($slug){
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikModel->getKomik($slug)
        ];

        if(empty($data['komik'])){
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul komik '.$slug.' tidak ditemukan.');
        }
        return view('komik/detail',$data);
    }

    public function create(){

        
        $data = [
            'title'=>'Form Tambah Data Komik',
            'validation'=> \Config\Services::validation()
        ];

        return view('komik/create',$data);
    }

    public function save(){

        if(!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required' => '* {field} komik harus diisi',
                    'is_unique' => '* {field} komik sudah ada'
                    ]
            ],
            'penulis' => [
                'rules' => 'required',
                'errors' => [
                    'required'=> '* {field} komik harus diisi'
                ]
            ],
            'penerbit' => [
                'rules' => 'required',
                'errors' => [
                    'required'=> '* {field} komik harus diisi'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|mime_in[sampul,image/jpg,image/jpeg,image/png]|is_image[sampul]',
                'errors' => [
                    'max_size'=> '* ukuran gambar terlalu besar',
                    'mime_in'=> '* itu bukan gambar',
                    'is_image'=> '* itu bukan gambar'
                ]
            ]
            
            
        ])){
            // $validation = \Config\Services::validation();
            // return redirect()->to('/komik/create')->withInput()->with('validation',$validation);
            return redirect()->to('/komik/create')->withInput();

        }
        // ambil gambar
        $filesampul = $this->request->getFile('sampul');

        // apakah tidak ada gambar yang di upload
        if($filesampul->getError() == 4){
            $namasampul = 'default.png';
        }else{
            // generate nama sampul random
            $namasampul = $filesampul->getRandomName();
    
            // pindahkan file ke folder img
            $filesampul->move('img',$namasampul);
            // // ambil nama file
            // $namasampul = $filesampul->getName();
        }


        $slug = url_title($this->request->getVar('judul'),'-',true);

        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namasampul
        ]);

        session()->setFlashdata('pesan','Data berhasil ditambahkan.');

        return redirect()->to('/komik');
    }

    public function delete($id){
        // cari berdasarkan id
        $komik = $this->komikModel->find($id);

        // cek jika file gambarnya default
        if($komik['sampul']!='default.png'){
            // hapus gambar
            unlink('img/'.$komik['sampul']);
        }


        $this->komikModel->delete($id);
        session()->setFlashdata('pesan','Data berhasil dihapus.');

        return redirect()->to('/komik');
    }

    public function edit($slug){
        $data = [
            'title'=>'Form Ubah Data Komik',
            'validation'=> \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug)
        ];

        return view('komik/edit',$data);
    }

    public function update($id){

        $komiklama= $this->komikModel->getKomik($this->request->getVar('slug'));
        if($komiklama['judul'] == $this->request->getVar('judul')){
            $rule_judul= 'required';
        }else{ 
            $rule_judul= 'required|is_unique[komik.judul]';
        }

        if(!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '* {field} komik harus diisi',
                    'is_unique' => '* {field} komik sudah ada'
                    ]
            ],
            'penulis' => [
                'rules' => 'required',
                'errors' => [
                    'required'=> '* {field} komik harus diisi'
                ]
            ],
            'penerbit' => [
                'rules' => 'required',
                'errors' => [
                    'required'=> '* {field} komik harus diisi'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|mime_in[sampul,image/jpg,image/jpeg,image/png]|is_image[sampul]',
                'errors' => [
                    'max_size'=> '* ukuran gambar terlalu besar',
                    'mime_in'=> '* itu bukan gambar',
                    'is_image'=> '* itu bukan gambar'
                ]
            ]
        ])){

            return redirect()->to('/komik/edit/'.$this->request->getVar('slug'))->withInput();

        }

        $filesampul = $this->request->getFile('sampul');

        // cek gambar diganti apa engga atau tetep gambar yang lama
        if($filesampul->getError() == 4){
            // jika gambar tidak diganti
            $namasampul = $this->request->getVar('sampulLama');
        }else {
            // jika gambarnya baru

              // generate nama sampul random
              $namasampul = $filesampul->getRandomName();
    
              // pindahkan file ke folder img
              $filesampul->move('img',$namasampul);
              
            //   hapus file yang lama
            unlink('img/'.$this->request->getVar('sampulLama'));
        }


        $slug = url_title($this->request->getVar('judul'),'-',true);

        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namasampul
        ]);

        session()->setFlashdata('pesan','Data berhasil diubah.');

        return redirect()->to('/komik');
    }
}