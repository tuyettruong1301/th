<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Diadiem;
use App\Comment;
use App\Bill;
use App\Tour;

class AdminController extends Controller
{
    public function trangchu(){
    	return view('admin.layout_admin.index');
    }

    public function getDSkhach(){
    	$dskhach = User::where('quyen',1)->get();
    	return view('admin.page_admin.danhsachnguoidung', compact('dskhach'));
    }

    public function getDShdv(){
    	$dshdv = User::where('quyen',2)->get();
    	return view('admin.page_admin.danhsachnguoidung', compact('dshdv'));
    }

    public function Xoakhach($idk){
    	User::find($idk)->delete(); 	
    	return redirect()->back()->with('thongbao','Xoa thanh cong');
    }

    public function Xoahdv($idhdv){
    	User::find($idhdv)->delete();
    	return redirect()->back()->with('thongbao','Xoa thanh cong');
    }

    public function ChapnhanHDV($idhdv){
        $cn = User::find($idhdv);
        $cn->status=2;
        $cn->save();
        return redirect()->back()->with('success','Thanh cong');
    }

    public function DSBinhluan(){
        $comment = Comment::all();
        return view('admin.page_admin.dsbinhluan', compact('comment'));
    }

    public function Xoabinhluan($idbl){
        Comment::find($idbl)->delete();
        Comment::where('parent_id',$idbl)->delete();
        return redirect()->back()->with('thongbao','Xoa binh luan thanh cong');
    }

    public function ThongkeDonhang(){
        $bill = Bill::all();
        return view('admin.page_admin.thongke', compact('bill'));
    }

    public function ThongkeDoanhthu(){
        $doanhthu = Bill::where('tinhtrangdon',3)->get();
        return view('admin.page_admin.thongke', compact('doanhthu'));
    }
}
