<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{
    public $presents = ['money', 'balli', 'gift'];
    public $gifts = ['A', 'B', 'C', 'D', 'E'];

    public function infoUser($login)
    {
        return DB::table('users')->where([['name', $login],])->get()->first();
    }

    public function infoBase(): object
    {
        return DB::table('base')->where([['name', 'base'],])->get()->first();
    }

    public function money(): int
    {
        $balance = $this->infoBase()->balance_money;
        return $balance > 100 ? rand(1, 100) : rand(1, $balance);
    }

    public function balli(): int
    {
        return rand(100, 200);
    }

    public function gift(): string
    {
        return $this->gifts[array_rand($this->gifts)];
    }

    public function getRandomPresent(): string
    {
        return $this->presents[array_rand($this->presents)];
    }

    public function getPresent() : void
    {
        $this->clearSession();
        session(['got_gift' => true]);
        $this->checkBalanceBase();
        $present = $this->getRandomPresent();
        session([$present => $this->$present()]);
    }

    public function clearSession() : void
    {
        foreach ($this->presents as $val) {session([$val => false]);}
    }

    public function refusePresent() : void
    {
        $this->clearSession();
        session(['got_gift' => false]);
        session(['msg' => 'Вы отказались от приза!']);
    }

    public function checkBalanceBase() : void
    {
        if ($this->infoBase()->balance_money == 0) {unset($this->presents[array_flip($this->presents)['money']]);}
        if ($this->infoBase()->balance_gifts == 0) {unset($this->presents[array_flip($this->presents)['gift']]);}
    }

    public function updateBalanceMoney($login, $money=null) : void
    {
        !is_null($money) ? : $money=session('money');
        if (!session('obmen')) {
            DB::table('users')->where('name', $login)->update(['balance_money' => self::infoUser($login)->balance_money+=$money]);}
        DB::table('base')->where('name', 'base')->update(['balance_money' => self::infoBase()->balance_money-=$money]);
        (!session('obmen')) ? session(['msg' => 'Деньги были переведены на Ваш счет в банке']) : session(['msg' => 'Выигранные деньги были переведены на баллы лояльности']);
    }

    public function updateBalanceBalli($login) : void
    {
        DB::table('users')->where('name', $login)->update(['balance_balli' => self::infoUser($login)->balance_balli+=session('balli')]);
        session(['msg' => 'Вам были начислены баллы лояльности']);
    }

    public function updateBalanceGift($login) : void
    {
        $balance_gifts = json_decode(self::infoUser($login)->balance_gifts, true);
        ($balance_gifts == '') ? $balance_gifts[0] = session('gift') : $balance_gifts[] = session('gift');
        DB::table('users')->where('name', $login)->update(['balance_gifts' => json_encode($balance_gifts)]);
        DB::table('base')->where('name', 'base')->update(['balance_gifts' => self::infoBase()->balance_gifts-=1]);
        session(['msg' => 'Ваш подарок будет отправлен Вам по почте!']);
    }


}


