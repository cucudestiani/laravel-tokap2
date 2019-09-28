<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produk;

class ProdukController extends Controller
{
    public function daftar(Request $req)
    {

    	/*menghubungkan tabel produk dengan kategori*/
    	$data = Produk::join('kategori','kategori.id','produk.id_kategori')
    	->where('nama_produk','like',"%{$req->keyword}%")
    	->select('produk.*','nama_kategori')
    	->orderBy('updated_at','desc')
    	->paginate(10);

    	return view('admin.pages.produk.daftar',['data'=>$data]);
    }

    /* Fungsi add/tambah*/
    public function add()
    {
    	return view('admin.pages.produk.add');
    }

    /*fungsi simpan*/
    public function save(Request $req)
    {
        \Validator::make($req->all(),[
            'kode'=>'required|between:3,100|unique:produk,kode_produk',
            'nama_produk'=>'required|between:3,100',
            'kategori'=>'required|numeric',
            'harga'=>'required|numeric',
            'stok'=>'required|numeric',
            'gambar'=>'required|image',
        ])->validate();
    
     /*ganti nama file*/
    $filename = rand(1,999).'_'.str_replace(' ','',$req->gambar->getClientOriginalName());

     /*simpan file*/
     $req->file('gambar')->storeAs('public/gambar-produk',$filename);

     $result = new Produk;
     $result->kode_produk = $req->kode;
     $result->nama_produk = $req->nama_produk;
     $result->id_kategori = $req->kategori;
     $result->harga = $req->harga;
     $result->stok = $req->stok;
     $result->gambar_produk = $filename;

     if($result->save()){
        return redirect()->route('admin.produk')->with('result','success');
     } else {
        return back()->with('result','fail')->withInput();
     }
    }
}
