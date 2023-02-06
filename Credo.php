<?php


namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class Credo extends Controller {

public $array_gifts = ['A', 'B', 'C', 'D', 'E'];


public function info($login) {
    $user = DB::table( 'migrations' )->where( [
        [ 'login', '=', $login ], ] )->get()->first();
    $base = DB::table( 'migrations' )->where( [
        [ 'login', '=', 'base' ], ] )->get()->first();
    $obj = (object) ['user'=> $user, 'base'=> $base];
    return $obj;
}

public function cabinet_user() {

    $login = session()->get('login');
    $info = self::info($login);
    if ($info) {
        return view('user.dashboard.credo-user', ['user' => $info->user, 'base' => $info->base]);
    }

    else {return view('signup');}
}


public function get_present() {
    $login = session()->get('login');
    $info = self::info($login);
    if ($info) {

        session(['money' => false]);
        session(['balli' => false]);
        session(['gift' => false]);

     function checkPresents($max_money = 100, $max_gifts = 1)
     {
         if ( ($max_money > 0) && ($max_gifts > 0) ) {$presents = ['money', 'balli', 'gift'];}
         else if ( ($max_money == 0) && ($max_gifts > 0) ) {$presents = ['balli', 'gift'];}
         else if ( ($max_money > 0) && ($max_gifts == 0) ) {$presents = ['money', 'balli'];}
         else {$presents = ['balli'];}

         $present = $presents[array_rand($presents)];

         $gifts = ['A', 'B', 'C', 'D', 'E'];

         if ($present == 'money') {
             $money = rand(1, $max_money);
             session(['money' => $money]);
         }

         if ($present == 'balli') {
             $balli = rand(100, 200);
             session(['balli' => $balli]);
         }

         if ($present == 'gift') {
             $gift = $gifts[array_rand($gifts)];
             session(['gift' => $gift]);
         }
     }
        $balance_base_money = $info->base->balance_money;
        $balance_base_gifts = $info->base->balance_gifts;

        if ( ($balance_base_money > 100) && ($balance_base_gifts > 0) ) {checkPresents();}
        else if ( ($balance_base_money > 0) && ($balance_base_money <= 100) && ($balance_base_gifts > 0) ) {checkPresents($balance_base_money, 1);}
        else if ( ($balance_base_money > 100) && ($balance_base_gifts == 0) ) {checkPresents(100, 0);}
        else if ( ($balance_base_money > 0) && ($balance_base_money <= 100) && ($balance_base_gifts == 0) ) {checkPresents($balance_base_money, 0);}
        else if ( ($balance_base_money == 0) && ($balance_base_gifts > 0) ) {checkPresents(0, $balance_base_gifts);}
        else  /*( ($balance_base_money == 0) && ($balance_base_gifts == 0) )*/ {checkPresents(0, 0);}

        session(['got_gift' => true]);

        return view('user.dashboard.credo-user', ['user' => $info->user, 'base' => $info->base]);
    }

    else {return view('signup');}
}


public function get_action(Request $request) {

    $login = session()->get('login');
    $info = self::info($login);
    if ($info) {

    $user = (array) $info->user;
    $base = (array) $info->base;

    $input = $request->all();

    var_dump($input);
    var_dump(session()->get('obmen'));

if ( $input['refuse'] == 'false') {

if (session()->get('obmen') === true)  {
    if (session()->get('make_obmen') == true) {
    //return redirect()->route('credo-user');}
    if (isset($input['toBank'])) {
        $balance_money = $user['balance_money'];
        $balance_money = $balance_money + $input['toBank'];
        DB::table('migrations')->where('login', $login)->update(['balance_money' => $balance_money]);

        $balance_money_base = $base['balance_money'];
        $balance_money_base = $balance_money_base - $input['toBank'];
        DB::table('migrations')->where('login', 'base')->update(['balance_money' => $balance_money_base]);
        session(['msg' => 'Деньги были переведены на Ваш счет в банке']);
        session(['make_obmen' => false]);
        return redirect()->route('credo-user');
    }
    else {
        $balance_balli = $user['balance_balli'];
        $balance_balli = $balance_balli + session()->get('balli');
        DB::table('migrations')->where('login', $login)->update(['balance_balli' => $balance_balli]);
        $balance_money_base = $base['balance_money'];
        $balance_money_base = $balance_money_base - session()->get('money');
        DB::table('migrations')->where('login', 'base')->update(['balance_money' => $balance_money_base]);
        session(['msg' => 'Выигранные деньги были переведены на баллы лояльности']);
        session(['make_obmen' => false]);
        return redirect()->route('credo-user');}
    }

    else {
        //return redirect()->route('credo-user');}
        session(['make_obmen' => true]);
        return view('user.dashboard.credo-user', ['user' => $info->user, 'base' => $info->base]);}
}

else if (session()->get('obmen') === false) {
    $balance_balli = $user['balance_balli'];
    $balance_balli = $balance_balli + session()->get('balli');
    DB::table('migrations')->where('login', $login)->update(['balance_balli' => $balance_balli]);
    session(['msg' => 'Вам были начислены баллы лояльности']);
    session(['make_obmen' => false]);
    return redirect()->route('credo-user');
}

else {

    $balance_gifts = json_decode($user['balance_gifts'], true);
    if ($balance_gifts == '') {$balance_gifts = []; $balance_gifts[0] = session()->get('gift');}
    else {$balance_gifts[] = session()->get('gift');}
    $balance_gifts = json_encode($balance_gifts);

    DB::table('migrations')->where('login', $login)->update(['balance_gifts' => $balance_gifts]);

    $balance_gifts_base = $base['balance_gifts']-1;

    DB::table('migrations')->where('login', 'base')->update(['balance_gifts' => $balance_gifts_base]);

    session(['msg' => 'Ваш подарок будет отправлен Вам по почте!']);
    return redirect()->route('credo-user');
}

}

else {
    session(['msg' => 'Вы отказались от приза!']);
    return redirect()->route('credo-user');}
    }

    else {return view('signup');}
}


public function cabinet_admin() {

    $login = session()->get('login');
    $info = self::info($login);
    if ($info) {
        $users = DB::table( 'migrations' )->where( [
            [ 'login', '!=', 'admin' ],
            [ 'login', '!=', 'base' ],
        ] )->get()->toArray();//->first();
        return view('user.dashboard.credo-admin', ['users' => $users, 'base' => $info->base]);
    }
    else {return view('signup');}
}


public function get_action_admin(Request $request) {
    $login = session()->get('login');
    $info = self::info($login);
    if ($info) {
        $users = DB::table( 'migrations' )->where( [
            [ 'login', '!=', 'admin' ],
            [ 'login', '!=', 'base' ],
        ] )->get()->toArray();

$command = $request['command'];

if (isset($request['base'])) {
    DB::table( 'migrations' )->where('login', 'base')
        ->update(['balance_money' => 1000, 'balance_gifts' => 10,]);
    return redirect()->route('credo-admin');}

if ($command == null) {return redirect()->route('credo-admin');}

if (strpos($command, ';') != false)
{$commands = explode(';', $command);
 if($commands[count($commands)-1] == '') {unset($commands[count($commands)-1]);}
}
else {$commands[0] = $command;}


session(['command' => $command]);
session(['commands' => $commands]);
session(['all_money' => 0]);
session(['all_gifts' => 0]);

function checkCode () {
$commands = session()->get('commands');

foreach($commands as $val) {
    if (corr($val)) {$flag = true;}
    else {$flag = false; break;}
}
session(['flag' => $flag]);
return $flag;
}

function checkUser ($login) {
    $user = DB::table( 'migrations' )->where( [['login', '=', $login]])->get()->first();
    $user = (array) $user;
    if ( ($user) && ($user['login'] != 'admin') && ($user['login'] != 'base') ) {return true;}
    else {return false;}
}

function corr ($comm) {

$array_gifts = (new Credo)->array_gifts;
$divider = explode(':', $comm);

if (checkUser($divider[0])) {
    if ( (ctype_digit($divider[1])) && (!isset($divider[2])) ) {
        $all_money = session()->get('all_money');
        $all_money += $divider[1];
        session(['all_money' => $all_money]);
        return true;}
    else if ( (ctype_digit($divider[1])) && (ctype_digit($divider[2])) && (!isset($divider[3])) ) {
        $all_money = session()->get('all_money');
        $all_money += $divider[1];
        session(['all_money' => $all_money]);
        return true;}
    else if ( ( in_array($divider[1], $array_gifts) ) && (!isset($divider[2])) ) {
        $all_gifts = session()->get('all_gifts');
        $all_gifts += 1;
        session(['all_gifts' => $all_gifts]);
        return true;}
    else if ( (ctype_digit($divider[1])) && (ctype_digit($divider[2])) && (in_array($divider[3], $array_gifts)) ) {
        $all_money = session()->get('all_money');
        $all_money += $divider[1];
        session(['all_money' => $all_money]);
        $all_gifts = session()->get('all_gifts');
        $all_gifts += 1;
        session(['all_gifts' => $all_gifts]);
        return true;}
    else {
        session(['err2' => 'Ошибка в коде пользователя "'.$divider[0].'"']); return false;}
}

else {
    session(['err1' => 'Такого пользователя "'.$divider[0].'" в базе нет']);
     return false;
}


}


function handler ($comm) {
    $array_gifts = (new Credo)->array_gifts;
    $divider = explode(':', $comm);

    if (checkUser($divider[0])) {

        if ( (ctype_digit($divider[1])) && (!isset($divider[2])) ) {
            $login = $divider[0]; $money = $divider[1];
            updateBD($login, $money, 0, '');
        }

        else if ( (ctype_digit($divider[1])) && (ctype_digit($divider[2])) && (!isset($divider[3])) ) {
            $login = $divider[0]; $money = $divider[1]; $balli = $divider[2];
            updateBD($login, $money, $balli, '');
        }

        else if ( ( in_array($divider[1], $array_gifts) ) && (!isset($divider[2])) ) {
            $login = $divider[0]; $gift = $divider[1];
            updateBD($login, 0, 0, $gift);
        }

        else if ( (ctype_digit($divider[1])) && (ctype_digit($divider[2])) && (in_array($divider[3], $array_gifts)) ) {
            $login = $divider[0]; $money = $divider[1]; $balli = $divider[2]; $gift = $divider[3];
            updateBD($login, $money, $balli, $gift);
        }

        else {}

    }
}

function updateBD ($login, $money, $balli, $gift) {

    $info = (new Credo)->info($login);

    $user = DB::table( 'migrations' )->where( [['login', '=', $login]])->get()->first();
    $user = (array) $user;
    $user_money = $user['balance_money']+$money;
    $user_balli = $user['balance_balli']+$balli;
    $balance_gifts = json_decode($user['balance_gifts'], true);
    if ($balance_gifts == '') { if ($gift != '') {$balance_gifts = []; $balance_gifts[0] = $gift;}}
    else { if ($gift != '') {$balance_gifts[]=$gift; }}
    if ($balance_gifts != '') {$gifts = json_encode($balance_gifts);}
    else {$gifts='';}

    $sql1 = DB::table('migrations')->where('login', $login)
        ->update(['balance_money' => $user_money, 'balance_balli' => $user_balli, 'balance_gifts' => $gifts,]);

    $base = DB::table( 'migrations' )->where( [['login', 'base']])->get()->first();
    $base = (array) $base;
    $base_money = $base['balance_money']-$money;
    if ($gift != '') {$base_gifts = $base['balance_gifts']-1;}
    else {$base_gifts = $base['balance_gifts'];}

    $sql2 = DB::table('migrations')->where('login', 'base')
        ->update(['balance_money' => $base_money, 'balance_gifts' => $base_gifts,]);

    if (($sql1) && ($sql2)) {session(['msg' => 'Команда успешно выполнена']);}
    else {session(['err1' => 'Ошибка при вставки данных в базу']);}

    return redirect()->route('credo-admin');
}

if (checkCode()) {

    $base = DB::table( 'migrations' )->where( [['login', '=', 'base']])->get()->first();
    $base = (array) $base;
    var_dump(session()->get('all_money'));
    $a = session()->get('all_money') - $base['balance_money'];
    $b = session()->get('all_gifts') - $base['balance_gifts'];

    if ( ($a>0) || ($b>0) ) {
        if ($a>0) {session(['err1' => 'Денег на всех не хватает. Нехватка: '.$a.' $']);}
        if ($b>0) {session(['err2' => 'Подарков на всех не хватает. Нехватка: '.$b.' штук']);}
        return redirect()->route('credo-admin');}

foreach($commands as $val) {
handler($val);}

    return redirect()->route('credo-admin');

}

else {return redirect()->route('credo-admin');}

        return view('user.dashboard.credo-admin', ['users' => $users, 'base' => $info->base]);
    }
    else {return view('signup');}
}

}
