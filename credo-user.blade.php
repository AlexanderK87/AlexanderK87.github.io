
@extends('main-credo')

@section('content')

    <div class="ml-3 font-weight-bold">
    <p>Кабинет пользователя {{$user->login}} </p>

    <div>
        Ваш баланс в банке: <span> {{$user->balance_money}} </span>$<br>
        Ваши баллы лояльности: <span> {{$user->balance_balli}} </span><br>
        Ваши подарки: <span> {{$user->balance_gifts}} </span>
    </div>

    <br>

    <form action="{{route('get-present')}}">
        <button>Испытай удачу!</button>
    </form>


    @if(session()->get('msg')) {{session()->get('msg')}} <?session(['msg' => false]);?> @endif


    @if (session()->get('got_gift'))
        <p>Поздравляем, Вы выиграли приз!</p>
        @if(session()->get('money')) <?session(['obmen' => true]);?> <p>Деньги: {{session()->get('money')}} $</p>@endif
        @if(session()->get('balli')) <?session(['obmen' => false]);?> <p>Бонусные баллы: {{session()->get('balli')}} </p>@endif
        @if(session()->get('gift')) <?session()->forget('obmen');?> <p>Приз: {{session()->get('gift')}} </p>@endif

        <form action="{{route('get-action')}}" method="post" style="display:inline; margin-right: 10px;">
            @csrf
          <input type="hidden" name="refuse" value="false">
          <button type="submit">Забрать приз!</button>
          </form>
        <form action="{{route('get-action-refuse')}}" method="post" style="display:inline;">
            @csrf
          <input type="hidden" name="refuse" value="true">
          <button type="submit"> Отказаться от приза!</button></form>

        <?session(['got_gift' => false]);?>

    @endif

    {{--@if (session()->get('make_obmen'))--}}
    @if (session()->get('make_obmen'))
        <?
        //session(['make_obmen' => false]);
        //session(['obmen' => false]);
        $money = session()->get('money');
        session(['balli' => 2*$money]);
        ?>
        Выигранные деньги: {{session()->get('money')}} $. Что с ними сделать?<br><br>
        <form action="{{route('get-action')}}" method="post" style="display:inline; margin-right: 10px;">
            @csrf
          <input type="hidden" name="refuse" value="false">
          <input type="hidden" name="toBank" value="{{$money}}">
          <button type="submit">Перевести на банковский счет</button>
          </form>
        <form action="{{route('get-action')}}" method="post" style="display:inline">
            @csrf
          <input type="hidden" name="refuse" value="false">
          <button type="submit">Обменять на баллы лояльности х2</button>
          </form>
    @endif


    <hr>

    <p>Информация из базы [для удобства тестирующих]</p>

    <div>
        Имеющийся запас денег на розыгрыш: <span> {{$base->balance_money}} </span>$<br>
        Подарки на складе: <span> {{$base->balance_gifts}} </span> штук
    </div>
    </div>
@endsection

<style>
button {
    font-weight: bold;
}
</style>
