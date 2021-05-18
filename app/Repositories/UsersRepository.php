<?php

namespace App\Repositories;

use App\User;
use App\Models\Log;
use Illuminate\Support\Facades\DB;

class UsersRepository extends Repository {

    private $user;
    private $log;

    public function __construct(User $user, Log $log)
    {
        $this->user = $user;
        $this->log = $log;
    }

    public function getUsers(){
        return $this->user->select('id', 'name')->get();
    }

    public function getUsuarioSaldo($id){
        return $this->user
            ->where('id', '=', $id)
            ->get('saldo');
    }

    public function atualizarsaldo($fields){
        return $this->user->where('id', $fields['id'])->update(['saldo' => $fields['novoSaldo']]);
    }

    public function transferir($fields){
        return $this->user->where('id', $fields['id'])->increment('saldo', $fields['transferir']);
    }

    public function saveLog($fields){
        return $this->log->insert([
            'id_enviando' => $fields['id_enviando'],
            'id_recebendo' => $fields['id_recebendo'],
            'valor' => $fields['valor']
        ]);
    }

    public function getLog($id){
        return $this->log->select('logs.id', 'id_enviando', 'id_recebendo', 'name', 'valor')
        ->join('users', 'users.id', '=', 'id_recebendo')
        ->where('id_recebendo', '=', $id)
        ->orWhere('id_enviando', '=', $id)
        ->get();
    }

    public function getLogInfo($id){
        return $this->log->where('id', '=', $id)->get();
    }

    public function removeTransferencia($id){
        return $this->log->where('id', '=', $id)->delete();
    }
}
