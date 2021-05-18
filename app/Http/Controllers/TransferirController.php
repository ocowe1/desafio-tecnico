<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Auth;

class TransferirController extends Controller
{

    private $request;
    private $usersRepository;

    public function __construct(Request $request, UsersRepository $usersRepository)
    {
        $this->middleware('auth');
        $this->request = $request;
        $this->usersRepository = $usersRepository;
    }

    public function transferir() {
        $validar = $this->request->validate([
            'valor' => ['required'],
            'usuario' => ['required']
        ]);

        $usuarioSaldo = $this->usersRepository->getUsuarioSaldo(Auth::user()['id']);

        foreach($usuarioSaldo as $s){

            $strSaldo = $this->sanitizeString($s->saldo);
            $strValor = $this->sanitizeString($validar['valor']);

            if($strSaldo < $strValor){
                return redirect ( 'home')->with ('erro', 'O saldo é insuficiente para completar esta trasação, sentimos muito.');
            }

            $fields1['novoSaldo'] = $strSaldo - $strValor;
            $fields1['id'] = Auth::user()['id'];
            $fields['transferir'] = $strValor;
            $fields['id'] = $validar['usuario'];

            $this->usersRepository->atualizarSaldo($fields1);
            $confirmar = $this->confirmarTransferencia($fields);

            $log['id_recebendo'] = $validar['usuario'];
            $log['id_enviando'] = Auth::user()['id'];
            $log['valor'] = $strValor;
            $saveLog = $this->usersRepository->saveLog($log);

        }

        if (!isset($confirmar)){
            return  redirect ( 'home')->with ('erro', 'Oops, a transferência não foi autorizada.');
        }
        return  redirect ( 'home')->with ('sucesso', 'Transferência realizada com sucesso.');

    }

    function confirmarTransferencia($fields) : Response {
        $this->usersRepository->transferir($fields);
        return app(Client::class)
            ->request('POST', 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6', $fields);
    }

    function sanitizeString($str) {
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        $str = preg_replace('/[^a-z0-9]/i', '', $str);
        return $str;
    }

    public function estornar($id){
        $logData = $this->usersRepository->getLogInfo($id);

        $usuarioSaldo = $this->usersRepository->getUsuarioSaldo(Auth::user()['id']);

        foreach($usuarioSaldo as $s) {
            foreach ($logData as $l) {
                $fields1['novoSaldo'] = $s->saldo - $l->valor;
                $fields1['id'] = Auth::user()['id'];

                $fields['transferir'] = $l->valor;
                $fields['id'] = $l->id_enviando;

                $this->usersRepository->atualizarSaldo($fields1);
                $confirmar = $this->confirmarTransferencia($fields);
            }
        }

        $this->usersRepository->removeTransferencia($id);

        if (!isset($confirmar)){
            return  redirect ( 'home')->with ('erro', 'Oops, a transferência não foi autorizada.');
        }
        return  redirect ( 'home')->with ('sucesso', 'Transferência realizada com sucesso.');
    }

}
