<?php

namespace App\Http\Controllers\note;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\support\Facades\Auth;


class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Menampilkan data catatan sesuai dengan user yang sedang login
        $notes = Note::where('user_id',Auth::user()->id)->get();
        if ($notes -> count()>0) {
            return response()->json([
                'status'=> true,
                'data'=> $notes
            ],200);
        }else{
            return response()->json([
                'status'=> false,
                'message'=> 'Data Catatan masih kosong'
            ]);
        }
    }

    public function store(Request $request)
    {
        //validasi data yang di inputkan
        $validator = Validator::make($request->all(),[
            'title'=> ['required','max:180'],
            'content' => ['required']
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()
            ], 400);
        }

        //Menyimpan catatan sesuai dengan user yang sedang login
        $notes = new Note();
        $notes->user_id = Auth::user()->id;
        $notes->title=$request->title;
        $notes->content=$request->content;
        $notes->category=$request->category;
        $notes->label=$request->label;
        $simpan = $notes->save();
        if($simpan){
            return response()->json([
                'status'=> true,
                'message'=> 'Berhasil tambah data Catatan'      
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //Mencari data id catatan sesuai dengan user yang sedang login
        $notes = Note::where('user_id',Auth::user()->id)->find($id);
        if($notes == null){
            return response()->json([
                'status'=>false,
                'message'=> 'ID tidak ditemukan'
            ],404);
        }else{
            return response()->json([
                'status'=> true,
                'data'=>$notes
            ],200);
        }
    }

    public function update(Request $request, $id)
    {
        $notes = Note::where('user_id',Auth::user()->id)->find($id);
        if($notes == null){
            return response()->json([
                'status' => false,
                'message' => 'Id tidak ditemukan'
            ],404);
        }else{
            $validator = Validator::make($request->all(),[
                'title'=> ['required','max:100'],
                'content' => ['required'],
                
            ],[
                'title.required'=> 'Judul Catatan harus diisi',
                'title.max'=> 'Maksimum judul 100 karakter',
                'content.required'=> 'Konten pada catatan harus diisi'
            ]);

            if($validator -> fails()){
                return response()->json([
                    'status'=> false,
                    'message'=> $validator->errors()
                ],400);
            }
            //Mengupdate catatan sesuai dengan user yang sedang login
            $notes -> title = $request-> title;
            $notes->content=$request->content;
            $notes->category=$request->category;
            $notes->label=$request->label;
            if($notes -> save()){
                return response()->json([
                    'status'=> true,
                    'message'=> 'Catatan berhasil diupdate'
                ],200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $notes = Note::where('user_id',Auth::user()->id)->find($id);
        if ($notes == null) {
            return response()->json([
                'status'=>false,
                'message'=> 'Id tidak ditemukan'
            ],400);
        }

        $delete= $notes-> delete();
        if ($delete) {
            return response()->json([
                'status'=> true,
                'message'=> 'Catatan berhasil dihapus'
            ],200);
        }

        return response()->json([
            'status'=> false,
            'message'=> 'Gagal menghapus catatan'
        ], 500);
    }
}
