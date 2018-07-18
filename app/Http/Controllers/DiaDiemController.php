<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DiaDiem;

class DiaDiemController extends Controller
{

    public function index()
    {
        $dsdd = Diadiem::all();
        return view('admin.page_admin.danhsachdiadiem', compact('dsdd'));
    }

    public function create()
    {
        return view('admin.page_admin.themdiadiem');
    }

    public function store(Request $request)
    {
        $this-> validate($request,
            [
                'tendiadiem'=>'required|unique:diadiem,tendiadiem'
            ],
            [
                'tendiadiem.required'=>'Vui long nhap ten dia diem',
                'tendiadiem.unique'=>'Dia diem nay da co trong danh sach dia diem'
            ]);
        $dd = new Diadiem();
        $dd->tendiadiem = $request->tendiadiem;
        $dd->save();
        return redirect()->back()->with('thanhcong','Them dia diem thanh cong');
    }

    // public function show($id)
    // {

    // }


    public function edit($id)
    {
        $dd = Diadiem::find($id);
        return view('admin.page_admin.themdiadiem',compact('dd'));
    }

    public function update(Request $request, DiaDiem $diadiem)
    {
        $this-> validate($request,
            [
                'tendiadiem'=>'required|unique:diadiem,tendiadiem'
            ],
            [
                'tendiadiem.required'=>'Vui long nhap ten dia diem',
                'tendiadiem.unique'=>'Dia diem nay da co trong danh sach dia diem'
            ]);
        $diadiem->tendiadiem = $request->tendiadiem;
        $diadiem->save();
        return redirect()->back()->with('ok','Sua dia diem thanh cong');
    }

    public function destroy(Diadiem $diadiem)
    {
        $diadiem->delete();
        return redirect()->back()->with('thongbao','Xoa thanh cong');
    }
}
