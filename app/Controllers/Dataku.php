<?php

namespace App\Controllers;

use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\LoginModel;

class Dataku extends BaseController
{

    protected $dataMahasiswa;
    protected $dataDosen;
    protected $admin;

    public function __construct()
    {
        $this->dataMahasiswa = new MahasiswaModel();
        $this->dataDosen = new DosenModel();
        $this->admin = new LoginModel();
    }

    public function index()
    {
        if (session()->get('nama') == '') {
            session()->setFlashdata('gagal', 'Anda Harus Login !!!');
            return redirect()->to('login/loginWeb');
        }

        $data = [
            'title' => 'Tentang Saya',
            'cumatabel' => $this->dataMahasiswa->findAll(),
            'cumatabeldua' => $this->dataDosen->findAll(),
            'gabung' => $this->dataDosen->gabungTabel()
        ];

        return view('data/index', $data);
    }

    public function indexMahasiswa()
    {
        if (session()->get('nama') == '') {
            session()->setFlashdata('gagal', 'Anda Harus Login !!!');
            return redirect()->to('login/loginWeb');
        }

        $data = [
            'title' => 'saya sayang ibuku',
            'bimbinganMhs' => $this->dataDosen->gabungTabel()
        ];

        return view('data/indexmhs', $data);
    }

    public function ubah($id)
    {
        if (session()->get('nama') == '') {
            session()->setFlashdata('gagal', 'Anda Harus Login !!!');
            return redirect()->to('login/loginWeb');
        }
        $data = [
            'title' => 'Ubah Data',
            'datanya' => $this->dataMahasiswa->getMahasiswaCek($id),
            'datanyaDosen' => $this->dataDosen->getDosenCek($id)
        ];

        $dosenUbahHalaman = $this->request->getVar('ubahDosen');

        if (isset($dosenUbahHalaman) == true) {
            return view('data/ubahdosen', $data);
        } else {
            return view('data/ubah', $data);
        }
    }

    public function detail($id)
    {
        if (session()->get('nama') == '') {
            session()->setFlashdata('gagal', 'Anda Harus Login !!!');
            return redirect()->to('login/loginWeb');
        }
        $data = [
            'title' => 'Detail Mahasiswa',
            'tampildetail' => $this->dataMahasiswa->getMahasiswaCek($id)
        ];

        return view('data/detail', $data);
    }

    public function detailDosen($id)
    {
        if (session()->get('nama') == '') {
            session()->setFlashdata('gagal', 'Anda Harus Login !!!');
            return redirect()->to('login/loginWeb');
        }
        $data = [
            'title' => 'Detail Dosen',
            'tampildetaildosen' => $this->dataDosen->getDosenCek($id)
        ];

        return view('data/detailDosen', $data);
    }

    public function tambahMahasiswa()
    {
        if (session()->get('nama') == '') {
            session()->setFlashdata('gagal', 'Anda Harus Login !!!');
            return redirect()->to('login/loginWeb');
        }
        $data = [
            'title' => 'Tambah Data Mahasiswa',
            'validasi' => \Config\Services::validation()
        ];

        return view('data/mahasiswa', $data);
    }
    public function tambahDosen()
    {
        if (session()->get('nama') == '') {
            session()->setFlashdata('gagal', 'Anda Harus Login !!!');
            return redirect()->to('login/loginWeb');
        }
        $data = [
            'title' => 'Tambah Data Dosen',
            'validasidua' => \Config\Services::validation()
        ];

        return view('data/dosen', $data);
    }

    // Registrasi Admin
    public function registrasiAdmin()
    {
        $data = [
            'title' => 'Registrasi Admin'
        ];

        return view('data/registrasi', $data);
    }

    public function simpanRegistrasi()
    {
        $this->admin->save([
            'nama' => $this->request->getVar('nama'),
            'nim' => $this->request->getVar('nim'),
            'level' => $this->request->getVar('adm')
        ]);
    }

    public function simpan()
    {

        $mhs = $this->request->getVar('mahasiswa');

        if (isset($mhs)) {
            if (!$this->validate([
                'nama' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus di isi',
                        'is_unique' => '{field} sudah ada'
                    ]
                ],
                'nim' => [
                    'rules' => 'required|min_length[9]|max_length[9]|is_unique[mahasiswa.nim]',
                    'errors' => [
                        'required' => '{field} harus di isi',
                        'min_length' => 'karakter harus 9 digit',
                        'max_length' => 'karakter harus 9 digit',
                        'is_unique' => '{field} sudah ada'
                    ]
                ],
                'ipk' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus di isi'
                    ]
                ]
            ])) {
                return redirect()->to('/dataku/tambahMahasiswa')->withInput();
            }
        } else {
            if (!$this->validate([
                'nama' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus di isi',
                        'is_unique' => '{field} sudah ada'
                    ]
                ],
                'nik' => [
                    'rules' => 'required|min_length[9]|max_length[9]|is_unique[dosen.nik]',
                    'errors' => [
                        'required' => '{field} harus di isi',
                        'min_length' => 'karakter harus 9 digit',
                        'max_length' => 'karakter harus 9 digit',
                        'is_unique' => '{field} sudah ada'
                    ]
                ],
                'bidangkeahlian' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} harus di isi',
                        'is_unique' => '{field} sudah ada'
                    ]
                ]
            ])) {
                return redirect()->to('/dataku/tambahDosen')->withInput();
            }
        }

        $mahasiswa = $this->request->getVar('mahasiswa');

        if (isset($mahasiswa)) {
            $this->dataMahasiswa->save([
                'nama' => $this->request->getVar('nama'),
                'nim' => $this->request->getVar('nim'),
                'ipk' => $this->request->getVar('ipk'),
                'jk' => $this->request->getVar('jk'),
                'level' => $this->request->getVar('mhs')
            ]);
        } else {
            $this->dataDosen->save([
                'nama' => $this->request->getVar('nama'),
                'nik' => $this->request->getVar('nik'),
                'bidangkeahlian' => $this->request->getVar('bidangkeahlian'),
                'jk' => $this->request->getVar('jk'),
                'level' => $this->request->getVar('dsn')
            ]);
        }

        return redirect()->to('/dataku');
    }

    public function hapus($id)
    {
        if (session()->get('nama') == '') {
            session()->setFlashdata('gagal', 'Anda Harus Login !!!');
            return redirect()->to('login/loginWeb');
        }

        $hapusLurr = $this->request->getVar('mahasiswahapus');
        $hapusLurrdua = $this->request->getVar('dosenhapus');

        if (isset($hapusLurr) == true) {
            $this->dataMahasiswa->delete($id);
        } else {
            $this->dataDosen->delete($id);
        }

        session()->setFlashdata('pesan', 'Data berhasil di Hapus.');
        return redirect()->to('/dataku');
    }

    public function update($id)
    {

        $mahasiswaUbah = $this->request->getVar('mahasiswaubah');
        $dosenUbah = $this->request->getVar('dosenUbah');

        if (isset($mahasiswaUbah) == true) {
            // update mahasiswa
            $this->dataMahasiswa->save([
                'id' => $id,
                'nama' => $this->request->getVar('nama'),
                'nim' => $this->request->getVar('nim'),
                'ipk' => $this->request->getVar('ipk'),
                'jk' => $this->request->getVar('jk')
            ]);
            session()->setFlashdata('pesan', 'Data berhasil di ubah.');
            // klo dah simpen data kembali ke halaman /buku/index
            return redirect()->to('/dataku');
        } else {
            // update dosen
            $this->dataDosen->save([
                'id' => $id,
                'nama' => $this->request->getVar('nama'),
                'nik' => $this->request->getVar('nik'),
                'bidangkeahlian' => $this->request->getVar('bidangkeahlian'),
                'jk' => $this->request->getVar('jk')
            ]);

            session()->setFlashdata('pesan', 'Data berhasil di ubah.');
            return redirect()->to('/dataku');
        }
    }
}
