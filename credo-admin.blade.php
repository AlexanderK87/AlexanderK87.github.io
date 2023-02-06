
@extends('main-credo')

@section('content')

    <div class="ml-3 font-weight-bold">
        <!--<p>Панель Админа</p>-->

        <table>
            <tr> <th>User логин</th> <th>Баланс в USD</th> <th>Баллы</th> <th>Подарки</th> </tr>
            @foreach($users as $val)
            <tr><td>{{$val->login}}</td><td>{{$val->balance_money}}</td><td>{{$val->balance_balli}}
                        </td><td>{{$val->balance_gifts}}</td></tr>
            @endforeach
        </table>

        <hr>

        <div class="row ml-1">
        <p>Общий бюджет: {{$base->balance_money}} $ Подарки на складе: {{$base->balance_gifts}} </p>
            <form action="{{route('get-action-admin')}}" method="post">
                @csrf
                <input type="hidden" size="40px" name="base" value="true">
                <button type="submit" class="ml-2">Обновить запас базы</button>
            </form>
        </div>

        <p><b>Командная строка</b></p>

        @if((session()->get('err1')))
            <span style="color:red">{{ session()->get('err1') }}</span><br><br>
        @endif
        @if((session()->get('err2')))
            <span style="color:red">{{ session()->get('err2') }}</span><br><br>
        @endif
        @if((session()->get('err1')) || (session()->get('err2')))
            <span style="color:brown">{{ session()->get('command') }}</span><br><br>
        @endif
        @if((session()->get('msg')))
            <span style="color:green">{{ session()->get('msg') }}</span><br><br>
        @endif
        <? session()->forget('err1'); session()->forget('err2'); session()->forget('command'); session()->forget('msg'); ?>


        <form action="{{route('get-action-admin')}}" method="post">
            @csrf
            <input type="text" size="40px" name="command" placeholder="">
            <button type="submit">Выполнить=></button>
        </form>


        <div class="instructions">
            <p>Инструкция по пользованию командной строки</p>
            <table>
            <tr><td>Допустимые команды.<br>
            login:0; - отправляет деньги<br>
            login:0:0; - отправляет деньги и баллы<br>
            login:gift; - отправляет подарок<br>
            login:0:0:gift; - отправляет деньги, баллы и подарок<br></td>
            <td>
            Примеры запросов:<br>
            login:0:0:A;<br>
            login:10:20;<br>
            login1:C;login2:10:15:E; и т.д.<br>
            <br>
            </td>
            </table><br>
            Можно указывать НЕСКОЛЬКО user'ов одновременно
        </div>

    </div>
@endsection

<style>
    .instructions td {font-weight: bold;}
    button {
        font-weight: bold;
    }
</style>
