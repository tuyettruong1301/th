<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;    
use App\User;
use App\Tour;
use App\DiaDiem;
use App\Bill;
use App\Comment;
use App\Rate;
use App\ImageTour;
use Hash;
use Auth;

class PageController extends Controller
{
    public function getTrangchu(){
        $tour=Tour::select('tour.id','users_id','hoten','tentour','giatour','hinhanh','tendiadiem')
                ->join('users','tour.users_id','=','users.id')
                ->join('diadiem','tour.diadiem_id','=','diadiem.id')->paginate(6);
        return view('page_client.index',compact('tour'));
    }

    public function getChitiet($idtour){
        $cttour = Tour::select('tour.id','users_id','hoten','tentour','giatour','mota','sokhachmax','tendiadiem','hinhanh')
                ->join('users','tour.users_id','=','users.id')
                ->join('diadiem','tour.diadiem_id','=','diadiem.id')
                ->where('tour.id',$idtour)->first();
        $comment= Comment::select('comment.id','email','noidung','comment.created_at','users_id')
                ->join('users','users.id','=','comment.users_id')
                ->where('tour_id',$idtour)->where('parent_id',0)->paginate(6);
        $traloi = Comment::select('email','comment.id','parent_id','comment.created_at','noidung','users_id')->join('users','users.id','=','comment.users_id')->get();
        $image = ImageTour::where('tour_id',$idtour)->get();
        $rate = Rate::select('rate.id','tour_id','users_id','sodiem')->where('tour_id',$idtour)->get();

        // $bill de lam gi
        
        $bill = Bill::select('users_id')->where('tour_id', $idtour)->where('tinhtrangdon',3)->get();

        if(Auth::check()){
            $iduser = Auth::user()->id;
            $checkRate = Rate::where('tour_id',$idtour)->where('users_id',$iduser)->get();
            $checkBill = Bill::where('tour_id',$idtour)->where('users_id',$iduser)->where('tinhtrangdon',0)->get();
            return view('page_client.chitiet', compact('cttour','comment','traloi','bill','rate','image','checkRate','checkBill'));
        }else{
            return view('page_client.chitiet', compact('cttour','comment','traloi','bill','rate','image'));
        }       
    }

    public function getDiadiem($iddd){
        $dd = DiaDiem::select('tendiadiem')->where('id',$iddd)->first();
        $idd=Tour::select('tour.id','users_id','hoten','tentour','giatour','hinhanh','tendiadiem')
                ->join('users','tour.users_id','=','users.id')
                ->join('diadiem','tour.diadiem_id','=','diadiem.id')
                ->where('diadiem.id',$iddd)->paginate(6);

        return view('page_client.diadiem',compact('idd','dd'));
    }

    public function postDattour($idtour, Request $request){
        $request->session()->flash('errorDatTour','');
        $this-> validate($request,
            [
                'timeBD'=>'required|date',
                'sokhachdangky'=>'required',
            ],
            [
                'timeBD.required'=>'Vui long nhap thoi gian bat dau',
                'timeBD.date'=>'Khong dung dinh dang date',
                'sokhachdangky.required'=>'Vui long nhap so khach dang ky',
            ]);
        $bill = new Bill();
        $bill->tour_id = $request->idtour;
        $bill->users_id = $request->idkhach;
        $bill->tongtien = $request->giatour;
        $bill->tinhtrangdon = 0;
        $bill->timeBD = $request->timeBD;

        $tour = Tour::find($idtour);
        if($tour->sokhachmax < $request->sokhachdangky || $request->sokhachdangky < 0) return redirect()->back()->with('loi','So khach dang ky phai nho hon hoac bang so khach max.');
        $bill->sokhachdangky = $request->sokhachdangky;
        $bill->save();
        return redirect()->back()->with('successDatTour','Gui don dat tour thanh cong');
    }

    public function getTourOfHdv($idhdv){
        $tour=Tour::select('tour.id','users_id','hoten','tentour','giatour','mota','tendiadiem','hinhanh')
                ->join('users','tour.users_id','=','users.id')
                ->join('diadiem','tour.diadiem_id','=','diadiem.id')
                ->where('users_id',$idhdv)->paginate(6);

        return view('page_client.tour_cua_hdv', compact('tour'));
    }

    public function getQuydinh(){
        return view('page_client.quydinh');
    }

    public function postDangkykhach(Request $req){
        $req->session()->flash('message1','');
        $this->validate($req,
            [
                'hoten'=>'required',
                'email'=> 'required|email|unique:users,email',
                'password'=>'required|max:30|min:6',
                'passwordAgain'=> 'same:password',
                'sodienthoai'=>'required',
            ],
            [
                'hoten.required'=>'Vui long nhap Ho ten',
                'email.required'=>'Vui long nhap Email',
                'email.email'=>'Dinh dang email khong dung, vui long nhap lai',
                'email.unique'=>'Email nay da co nguoi su dung',
                'password.required'=>'Vui long nhap password',
                'password.max'=>'Password toi da 30 ky tu',
                'password.min'=>'Password toi thieu 6 ky tu',
                'passwordAgain.same'=>'Mat khau xac nhan khong hop le',
                'sodienthoai.required'=>'Vui long nhap so dien thoai',
            ]);
        $users = new User();
        $users->hoten = $req->hoten;
        $users->email = $req->email;
        $users->password= Hash::make($req->password);
        $users->sodienthoai = $req->sodienthoai;
        $users->quyen = 1; 
        $users->save();
        return redirect()->back()->with('thanhcongkhach','Dang ky thành công');
    }

    public function postDangkyhdv(Request $req){
        $req->session()->flash('message2','');
        $this -> validate($req,
            [
                'hoten'=> 'required',
                'email'=> 'required|email|unique:users,email',
                'password'=> 'required|max:30|min:6',
                'passwordAgain'=> 'same:password',
                'sodienthoai'=> 'required',
                'diachi'=> 'required',
            ],
            [
                'hoten.required'=> 'Vui long nhap ho ten',
                'email.required'=> 'Vui long nhap email',
                'email.email'=> 'Khong dung dinh dang email, vui long nhap lai',
                'email.unique'=> 'Email nay da co nguoi su dung',
                'password.required'=> 'Vui long nhap mat khau',
                'password.min'=> 'Mat khau toi thieu 6 ky tu',
                'password.max'=> 'Mat khau toi da 30 ky ty',
                'passwordAgain.same'=> 'Mat khau xac nhan khong hop le',
                'sodienthoai.required'=> 'Vui long nhap so dien thoai',
                'diachi.required'=> 'Vui long nhap dia chi',
            ]);
        $users = new User();
        $users->hoten = $req->hoten;
        $users->email = $req->email;
        $users->password = Hash::make($req->password);
        $users->sodienthoai = $req->sodienthoai;
        $users->diachi = $req->diachi;
        $users->quyen = 2;
        $users->save();

        return redirect()->back()->with('thanhconghdv','Dang ky tai khoan thanh cong');
    }

    public function postDangnhap(Request $req){
        $req->session()->flash('message3','');
        $this -> validate($req,
            [
                'email'=> 'required|email',
                'password'=> 'required|max:30|min:6',
            ],
            [
                'email.required'=> 'Vui long nhap email',
                'email.email'=> 'Khong dung dinh dang email, vui long nhap lai',
                'password.required'=> 'Vui long nhap mat khau',
                'password.min'=> 'Mat khau toi thieu 6 ky tu',
                'password.max'=> 'Mat khau toi da 30 ky ty',
            ]);
        $check_user = array('email'=>$req->email,'password'=>$req->password);
        $check_admin = array('email'=>$req->email,'password'=>$req->password,'quyen'=>3);
        if(Auth::attempt($check_admin))
            return redirect()->route('trang-chu-admin');
        else if(Auth::attempt($check_user))
            return redirect()->route('trang-chu');
        else
            return redirect()->back()->with('message','Sai tài khoản hoặc mật khẩu!');
    }

    public function getDangxuat(){
        Auth::logout();
        return redirect()->route('trang-chu');
    }

    public function postBinhluan($idtour, Request $request){
        $this -> validate($request,
            [
                'noidung'=>'required'
            ],
            [
                'noidung.required'=>'Ban chua nhap noi dung binh luan'
            ]);
        $iduser = Auth::user()->id;

        $comment = new Comment();
        $comment->noidung = $request->noidung;
        $comment->users_id = $iduser;
        $comment->parent_id = 0;
        $comment->tour_id = $idtour;
        $comment->save();
        return redirect()->back()->with('thanhcong','Gui binh luan thanh cong');
    }

    public function getThongtincanhan(){
        $user = Auth::user();
        return view('page_client.thongtincanhan', compact('user'));
    }

    public function getThongtinHDV($idhdv){
        $cthdv = User::where('id',$idhdv)->get();
        return view('page_client.thongtincanhan', compact('cthdv'));
    }

    public function postSuathongtin(Request $request){
       $this -> validate($request,
            [
                'hoten'=>'required',
                'sodienthoai'=>'required|numeric',
            ],
            [
                'hoten.required'=>'Vui long nhap ho ten',
                'sodienthoai.required'=>'Vui long nhap so dien thoai',
                'sodienthoai.numeric'=>'So dien thoai la 1 day so',
            ]);     
        $user = Auth::user();
        $user->hoten = $request->hoten;

        if($request->checkpassword == "on"){
            $this->validate($request,
                [
                    'password'=>'required|min:6|max:30',
                    'passwordAgain' =>'required|same:password'
                ],
                [
                    'password.required' => 'Bạn chưa nhập mật khẩu moi',
                    'password.min' => 'Mật khẩu moi toi thieu 6 kí tự',
                    'password.max' => 'Mật khẩu moi tối đa 30 kí tự',
                    'passwordAgain.required' => 'Bạn chưa nhập lại mật khẩu',
                    'passwordAgain.same' => 'Xac nhan mat khau moi khong đúng'
                ]);
            $user->password = bcrypt($request->password);
        }
        if($request->hasFile('anhdaidien')){
            $file = $request->file('anhdaidien');
            $duoi = $file->getClientOriginalExtension();
            if($duoi != 'jpg' && $duoi != "png" && $duoi != "jpeg"){
                return redirect()->back()->with('loi','Định dạng ảnh phải là jpg, png, jpeg');
            }

            $name = $file->getClientOriginalName();
            echo $name;
            $anhdaidien= str_random(4)."_".$name;
            while(file_exists("upload".$anhdaidien)){
                $anhdaidien= str_random(4)."_".$name;
            }
            
            $file->move("upload",$anhdaidien);
            $user->anhdaidien = $anhdaidien;
        }

        $user->sodienthoai=$request->sodienthoai;
        $user->diachi = $request->diachi;
        $user->namsinh = $request->namsinh;
        if($request->gioitinh != "")
            $user->gioitinh = $request->gioitinh;
        $user->save();
        return redirect()->route('thong-tin-ca-nhan')->with('thanhcong','Sua thong tin thanh cong');
    }

    public function getTimkiem(Request $request){
        $this->validate($request,
            [
                'timkiem' => 'required'
            ],
            [
                'timkiem.required'=> 'Vui long nhap thong tin can tim kiem'
            ]);
        $tk = $request->timkiem;
        $ketqua = Tour::select('tour.id','tentour','hinhanh','giatour','hoten','tendiadiem','users_id')->where('tentour','like','%'.$tk.'%')
                ->orwhere('giatour',$tk)
                ->join('diadiem','tour.diadiem_id','=','diadiem.id')
                ->join('users','tour.users_id','=','users.id')
                ->orwhere('tendiadiem','like','%'.$tk.'%')
                ->paginate(6);
        $count  = Tour::where('tentour','like','%'.$tk.'%')
                ->orwhere('giatour',$tk)
                ->join('diadiem','tour.diadiem_id','=','diadiem.id')
                ->orwhere('tendiadiem','like','%'.$tk.'%')
                ->get();
        return view('page_client.timkiem',compact('ketqua','count','tk'));
    }

    public function getLichsu(){
        $iduser = Auth::user()->id;
        $lichsu = Bill::select('tour_id','sokhachdangky','tongtien','tinhtrangdon','tentour','tour.users_id','hinhanh','bill.id','email')
            ->where('bill.users_id',$iduser)
            ->join('tour','tour.id','=','bill.tour_id')
            ->join('users','tour.users_id','=','users.id')->paginate(6);
        return view('page_client.lichsudattour', compact('lichsu'));
    }

    public function getTraloi($idbl){
        $bl = Comment::select('id','noidung')->where('id',$idbl)->first();
        return view('page_client.traloibinhluan',compact('bl'));
    }

    public function postTraloi($idbl, Request $request){
        $iduser= Auth::user()->id;
        $idtour = Comment::find($idbl)->tour_id;

        $this->validate($request,
            [
                'traloi'=>'required'
            ],
            [   
                'traloi.required'=>'Vui long nhap cau tra loi'
            ]);
        $traloi = new Comment();
        $traloi->parent_id = $idbl;
        $traloi->users_id = $iduser;
        $traloi->tour_id = $idtour;
        $traloi->noidung = $request->traloi;
        $traloi->save();
        return redirect()->route('chitiet',$idtour);
    }

    public function Danhgia($idtour, Request $request){
        $iduser = Auth::user()->id;
        if($request->sodiem == 0) return redirect()->back()->with('errorRate','Loi danh gia!');
        else{
            $rate = new Rate();
            $rate->tour_id = $idtour;
            $rate->users_id = $iduser;
            $rate->sodiem = $request->sodiem;
            $rate->save();
            return redirect()->back()->with('successRate','Cam on ban da danh gia tour.');
        }
    }


}
