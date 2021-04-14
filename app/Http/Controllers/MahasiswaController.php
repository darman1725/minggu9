<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\MahasiswaController;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Contact;
use App\Models\Kelas;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {    
        //fungsi eloquent menampilkan data menggunakan pagination         
        /*$mahasiswas = Mahasiswa::all(); 
        // Mengambil semua isi tabel         
        $posts = Mahasiswa::orderBy('Nim', 'desc')->paginate(5);         
        return view('mahasiswas.index', compact('mahasiswas'));         
        with('i', (request()->input('page', 1) - 1) * 5); */
        
        //yang semula Mahasiswa::all, diubah menjadi with() yang menyatakan relasi
        $mahasiswas = Mahasiswa::with('kelas')->get();
        $paginate = Mahasiswa::orderBy('Nim', 'asc')->paginate(3);
        return view('mahasiswas.index', ['mahasiswa' => $mahasiswas, 'paginate' => $paginate]);
        
    }

    public function users(Request $request)
    {
        $mahasiswas = Mahasiswa::paginate(5);
        return view('mahasiswas.users', compact('mahasiswas'));
    }

    public function cari(Request $request) 
    {
        //fungsi eloquent menampilkan data menggunakan pagination         
        /*$mahasiswas = Mahasiswa::all(); 
        // Mengambil semua isi tabel         
        $posts = Mahasiswa::orderBy('Nim', 'desc')->paginate(5);         
        return view('mahasiswas.index', compact('mahasiswas'));         
        with('i', (request()->input('page', 1) - 1) * 5); */

        $keyword = $request->get('keyword');
        $paginate = Mahasiswa::all(); 

        if($keyword) {
            $paginate = Mahasiswa::where("Nama","LIKE","%$keyword%")->get();
            return view('mahasiswas.users', compact('paginate'));  
        }
        else if(!$keyword){
            return redirect()->route('mahasiswas.index')             
            ->with('error', 'Mahasiswa Tidak Ditemukan');
        } 
        else{
            return redirect()->route('mahasiswas.index')             
            ->with('error', 'Mahasiswa Tidak Ditemukan'); 
        } 
    }

    public function create()
    {          
        //mendapatkan data dari tabel kelas
        $kelas = Kelas::all();
        return view('mahasiswas.create', ['kelas' => $kelas]); 
    }

    public function store(Request $request)
    {
       //melakukan validasi data         
       $request->validate([             
        'Nim' => 'required',             
        'Nama' => 'required', 
        'Tanggal_Lahir' => 'required',
        'Email' => 'required',
        'Kelas' => 'required',             
        'Jurusan' => 'required',             
        'No_Handphone' => 'required',         
    ]); 

    $Mahasiswa = new Mahasiswa;
    $Mahasiswa->nim = $request->get('Nim');
    $Mahasiswa->nama = $request->get('Nama');
    $Mahasiswa->tanggal_lahir = $request->get('Tanggal_Lahir');
    $Mahasiswa->email = $request->get('Email');
    $Mahasiswa->jurusan = $request->get('Jurusan');
    $Mahasiswa->no_handphone = $request->get('No_Handphone');
    $Mahasiswa->save(); 

    $kelas = new Kelas;
    $kelas->id = $request->get('Kelas');

    //fungsi eloquent untuk menambah data dengan relasi belongsTo
    $Mahasiswa->kelas()->associate($kelas);
    $Mahasiswa->save(); 

    //jika data berhasil ditambahkan, akan kembali ke halaman utama         
    return redirect()->route('mahasiswas.index')             
    ->with('success', 'Mahasiswa Berhasil Ditambahkan');   
    }

    public function show($Nim)
    {
        //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa         
        $Mahasiswa = Mahasiswa::with('kelas')->where('Nim', $Nim)->first();

        return view('mahasiswas.detail', ['Mahasiswa' => $Mahasiswa]);   
    }

    public function edit($Nim)
    {
        //mendapatkan data dari tabel kelas
        $Mahasiswa = Mahasiswa::with('kelas')->where('Nim', $Nim)->first();
        $kelas = Kelas::all();        
        return view('mahasiswas.edit', compact('Mahasiswa','kelas')); 
    }

    public function update(Request $request, $Nim)
    {
        //melakukan validasi data         
         $request->validate([             
             'Nim' => 'required',             
             'Nama' => 'required', 
             'Tanggal_Lahir' => 'required',
             'Email' => 'required',            
             'Kelas' => 'required',             
             'Jurusan' => 'required',             
             'No_Handphone' => 'required', 
        ]);
        
        $Mahasiswa = Mahasiswa::with('kelas')->where('Nim', $Nim)->first();
        $Mahasiswa->nim = $request->get('Nim');
        $Mahasiswa->nama = $request->get('Nama');
        $Mahasiswa->tanggal_lahir = $request->get('Tanggal_Lahir');
        $Mahasiswa->email = $request->get('Email');
        $Mahasiswa->jurusan = $request->get('Jurusan');
        $Mahasiswa->no_handphone = $request->get('No_Handphone');
        $Mahasiswa->save();

        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');

        //fungsi eloquent untuk mengupdate data dengan relasi belongsTo
        $Mahasiswa->kelas()->associate($kelas);
        $Mahasiswa->save();
          
        //jika data berhasil diupdate, akan kembali ke halaman utama  
        return redirect()->route('mahasiswas.index')             
            ->with('success', 'Mahasiswa Berhasil Diupdate'); 
    }

    public function destroy($Nim)
    {
       //fungsi eloquent untuk menghapus data          
       Mahasiswa::find($Nim)->delete();         
       return redirect()->route('mahasiswas.index')             
           -> with('success', 'Mahasiswa Berhasil Dihapus');  
    }
};


