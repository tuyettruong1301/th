<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background-color: #F5ECCE">
    <div class="container" >
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <div class="col-md-6 col-sm-6 col-xs-6">
                <form action="{{route('tim-kiem')}}" style="margin-top: 8px;">
                    <input type="text" name= "timkiem" class="form-control" placeholder="nhap thong tin tim kiem" style="width: 70%;float: left">
                    <button type="submit" class="btn btn-default" style="margin-left: 5px">Search</button>                                     
                </form>
            </div>
            <ul class="nav navbar-nav pull-right">
                @if(Auth::check())
                    @if(Auth::User()->anhdaidien != "")
                        <li><p style="margin-top: 5px"><a href="{{route('thong-tin-ca-nhan')}}"> <img src="upload/{{Auth::User()->anhdaidien}}" width="50" height="45">  {{Auth::User()->hoten}}</a></p></li>
                    @else
                        <li><a href="{{route('thong-tin-ca-nhan')}}"><i class="fa fa-user"></i> {{Auth::User()->hoten}} </a></li>
                    @endif
                    @if(Auth::User()->quyen == 2)                    
                        <li><a href="{{route('trang-chu-hdv')}}">Quan ly tour</a></li>
                    @elseif(Auth::User()->quyen == 3)
                        <li><a href="{{route('trang-chu-admin')}}">Trang quan ly</a></li>
                    @elseif(Auth::User()->quyen == 1)
                        <li><a href="{{route('lich-su')}}"><i class="glyphicon glyphicon-shopping-cart"></i> Lich su dat tour</a></li>
                    @endif
                    <li><a href="{{ route('dang-xuat')}}">Đăng xuất</a></li>
                @else
                    <li><a href="" data-toggle="modal" data-target="#DangKyKhach">Đăng ký Khách</a></li>
                    <li><a href="" data-toggle="modal" data-target="#DangKyHDV">Đăng ký HDV</a></li>
                    <li><a href="" data-toggle="modal" data-target="#DangNhap">Đăng nhập</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<div class="modal" id="DangKyKhach">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn btn-danger" data-dismiss="modal" style="float: right; padding: 3px 20px; font-weight: bold;">X</button>
            <!-- Modal Header -->
            <div class="modal-header" style="background-color: #66FFFF">  
                <div align="center" style="font-size: 32px; font-weight: bold; color: red">Dang ky Khach du lich</div>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                @if(Session::has('thanhcongkhach'))
                    <div class="alert alert-success text-center">{{Session::get('thanhcongkhach')}}</div>
                @endif

                <form action="{{route('dang-ky-khach')}}" method="POST">
                    <fieldset style="color: blue; font-style: italic;">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <label>Ho ten</label> 
                        <span style="color: red; margin-left: 20px">{{$errors->first('hoten')}}</span>
                        <input class="form-control" name="hoten" type="text" value="{{ old('hoten') }}"><br>

                        <label>Email</label> <span id="msgbox"></span>
                        <span style="color: red; margin-left: 20px">{{$errors->first('email')}}</span>           
                        <input class="form-control" name="email" type="email" value="{{ old('email') }}" id="email">
                        <br>

                        <label>Mat khau</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('password')}}</span>   
                        <input class="form-control" name="password" type="password"><br>

                        <label>Nhap lai mat khau</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('passwordAgain')}}</span>   
                        <input class="form-control" name="passwordAgain" type="password"><br>

                        <label>So dien thoai</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('sodienthoai')}}</span>   
                        <input type="text" name="sodienthoai" class="form-control" value="{{ old('sodienthoai') }}"><br>

                        <div align="center"><button type="submit" class="btn btn-lg btn-success btn-block" style="width: 20%">Đăng ký</button></div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@if(count($errors)>0)
    @if(Session::has('message2'))
    <script>
        $(document).ready(function(){
            $("#DangKyHDV").modal();
        });
    </script>
    @elseif(Session::has('message3'))
    <script>
        $(document).ready(function(){
            $("#DangNhap").modal();
        });
    </script>
    @else
    <script>
        $(document).ready(function(){
            $("#DangKyKhach").modal();
        });
    </script>
    @endif
@endif

@if(Session::has('loiDangNhap'))
    <script>
        $(document).ready(function(){
            $("#DangNhap").modal();
        });
    </script>
@elseif(Session::has('thanhcongkhach'))
    <script>
        $(document).ready(function(){
            $("#DangKyKhach").modal();
        });
    </script>
@elseif(Session::has('thanhconghdv'))
    <script>
        $(document).ready(function(){
            $("#DangKyHDV").modal();
        });
    </script>
@endif

<div class="modal" id="DangKyHDV">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn btn-danger" data-dismiss="modal" style="float: right; padding: 3px 20px; font-weight: bold;">X</button>
            <!-- Modal Header -->
            <div class="modal-header" style="background-color: #66FFFF">  
                <div align="center" style="font-size: 32px; font-weight: bold; color: red">Dang ky Huong dan vien</div>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                @if(Session::has('thanhconghdv'))
                    <div class="alert alert-success text-center">{{Session::get('thanhconghdv')}}</div>
                @endif

                <form action="{{route('dang-ky-hdv')}}" method="POST">
                    <fieldset style="color: blue; font-style: italic;">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">

                        <label>Ho ten</label> 
                        <span style="color: red; margin-left: 20px">{{$errors->first('hoten')}}</span>
                        <input class="form-control" name="hoten" type="text" value="{{ old('hoten') }}"><br>

                        <label>Email</label> <span id="msgbox1"></span>
                        <span style="color: red; margin-left: 20px">{{$errors->first('email')}}</span>
                        <input class="form-control" name="email" type="email" value="{{ old('email') }}" id="email1"><br>

                        <label>Mat khau</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('password')}}</span>
                        <input class="form-control" name="password" type="password"><br>

                        <label>Nhap lai mat khau</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('passwordAgain')}}</span>
                        <input class="form-control" name="passwordAgain" type="password"><br>

                        <label>So dien thoai</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('sodienthoai')}}</span>
                        <input type="text" name="sodienthoai" class="form-control" value="{{ old('sodienthoai') }}"><br>

                        <label>Dia chi</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('diachi')}}</span>
                        <input type="text" name="diachi" class="form-control" value="{{ old('diachi') }}"><br>

                        <div align="center"><button type="submit" class="btn btn-lg btn-success btn-block" style="width: 20%">Đăng ký</button></div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="DangNhap">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn btn-danger" data-dismiss="modal" style="float: right; padding: 3px 20px; font-weight: bold;">X</button>
            <!-- Modal Header -->
            <div class="modal-header" style="background-color: #66FFFF">  
                <div align="center" style="font-size: 32px; font-weight: bold; color: red">Dang Nhap</div>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                @if(Session::has('loiDangNhap'))
                    <div class="alert alert-danger text-center">{{Session::get('loiDangNhap')}}</div>
                @endif

                <form action="{{route('dang-nhap')}}" method="POST">
                    <fieldset style="color: blue; font-style: italic;">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">

                        <label>Email</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('email')}}</span>
                        <input class="form-control" name="email" type="email" value="{{ old('email') }}"><br>

                        <label>Mat khau</label>
                        <span style="color: red; margin-left: 20px">{{$errors->first('password')}}</span>
                        <input class="form-control" name="password" type="password"><br>

                        <div align="center"><button type="submit" class="btn btn-lg btn-success btn-block" style="width: 20%">Đăng nhap</button></div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>



