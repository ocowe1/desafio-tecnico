<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UsersRepository;

class HomeController extends Controller
{

    private $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->middleware('auth');
        $this->usersRepository = $usersRepository;
    }

    public function index()
    {
        //Envio de dados do usuário para identificação na Home.
        $user_data = Auth::user();
        $name = $user_data['name'];
        $tipo = $user_data['tipo'];
        $saldo = $user_data['saldo'];
        $usuarios = $this->usersRepository->getUsers();
        $log = $this->usersRepository->getLog($user_data['id']);

        return view('home', compact(['name', 'tipo', 'saldo', 'usuarios', 'log']));
    }

}
