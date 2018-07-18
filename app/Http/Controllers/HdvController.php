<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Tour;
use App\Diadiem;
use App\Bill;
use App\ImageTour;
use Auth;

class HdvController extends Controller
{
    public function trangchu(){
    	return view('layout_hdv.index');
    }

    public function getDSdontour(){
        $bill = Tour::where('users_id', Auth::user()->id)->first()->bill;
        return view('page_hdv.danhsachdondattour', compact('bill'));
    }

    public function getDSdontourmoi(){
        $bill = Tour::where('users_id', Auth::user()->id)->first()->bill;
        $tmp = 'tmp';
        return view('page_hdv.danhsachdondattour', compact('bill','tmp'));
    }

    public function getChapnhandon($idd){
        $don = Bill::find($idd);
        $don -> tinhtrangdon = 1;
        $don -> save();
        return redirect()->back()->with('chapnhan','Chap nhan don dat tour thanh cong');
    }

    public function getTuchoidon($idd){
        $don = Bill::find($idd);
        $don -> tinhtrangdon = 2;
        $don -> save();
        return redirect()->back()->with('tuchoi','Tu choi don dat tour thanh cong');
    }

    public function getThemAnh($idtour){
        $idt = Tour::find($idtour);
        $checkImage = ImageTour::where('tour_id',$idtour)->get();
        return view('page_hdv.themanhtour', compact('idt','checkImage'));
    }

    public function postThemAnh($idtour, Request $request){
        if($request->hasFile('image')){
            $file = $request->file('image');
            $duoi = $file->getClientOriginalExtension();
            if($duoi != 'jpg' && $duoi != "png" && $duoi != "jpeg"){
                return redirect()->back()->with('loi','Định dạng ảnh phải là jpg,png,jpeg');
            }

            $name = $file->getClientOriginalName();
            $image= str_random(4)."_".$name;
            while(file_exists("upload".$image)){
                $image= str_random(4)."_".$name;
            }
            
            $file->move("upload",$image);

            $imagetour = new ImageTour();
            $imagetour->image = $image;
            $imagetour->tour_id = $idtour;
            $imagetour->save();
            return redirect()->back()->with('success','Them hinh anh thanh cong.');
        }else{
            return redirect()->back()->with('error','Them hinh anh that bai.');
        }
    }

    public function getXoaAnh($idm){
        ImageTour::find($idm)->delete();
        return redirect()->back()->with('delImage','Xoa anh thanh cong');
    }

}
