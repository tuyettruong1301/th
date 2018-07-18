<div class="col-md-3 col-xs-3 col-sm-3">
    <ul class="list-group" id="menu" style="list-style: none">
        <li> 
            <a href="trang-chu" class="list-group-item menu1 active" style="margin-bottom: 5px;">Trang chủ</a>
        </li>  
        <li> 
            <a href="quy-dinh" class="list-group-item menu1 active" style="margin-bottom: 5px;">Quy định</a>
        </li> 
        <li href="#" class="list-group-item menu1 active">Địa điểm</li>  

        <ul>
            @foreach($diadiem as $dd)
            <li class="list-group-item menu1" style="margin-left: -20px">
                <a href="{{route('diadiem',$dd->id)}}">{{$dd->tendiadiem}}</a>
            </li>
            @endforeach
        </ul>
    </ul>
</div>