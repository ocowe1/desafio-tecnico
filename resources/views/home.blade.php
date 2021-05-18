@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('erro'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Atenção!</strong> {{ session('erro') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @elseif(session('sucesso'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Atenção!</strong> {{ session('sucesso') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($tipo == 1)
                        {{ __('Olá '. $name) }}
                    @else
                        {{ __('Bem-vindo (a) ' . $name) }}
                    @endif
                    <br>

                    <b>Seu saldo é de:</b> R$ {{ $saldo }}
                    <br>

                    @if($tipo == 1)
                            <hr>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#transferir">Transferir</button>
                    @endif

                    <hr>

                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Destinatário</th>
                                <th scope="col">Valor</th>

                                @if($tipo ==2)
                                    <th scope="col"></th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($log as $l)
                                <tr>
                                    <td>{{$l->name}}</td>
                                    <td>{{ $l->valor }}</td>
                                    @if($tipo == 2)
                                        <td><a type="button" href=" {{ route('estornar', ['id' => $l->id] ) }} " class="btn btn-primary" >Estornar</a></td>
                                    @endif
                                </tr>
                            @endforeach

                            </tbody>
                        </table>



                        <div class="modal fade" id="transferir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Transferir</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="post" action="{{ route('transferir') }}">
                                        <div class="modal-body">
                                            @csrf
                                            <div class="form-group">
                                                <label for="valor">Valor</label>
                                                <input type="text" class="form-control" id="valor" name="valor" onKeyPress="return(moeda(this,'.',',',event))" autocomplete="off">
                                            </div>

                                            <div class="form-group">
                                                <label for="usuario">Usuários</label>
                                                <select class="form-control" id="usuario" name="usuario">
                                                    @foreach($usuarios as $u)
                                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Transferir</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function moeda(a, e, r, t) {
        let n = ""
            , h = j = 0
            , u = tamanho2 = 0
            , l = ajd2 = ""
            , o = window.Event ? t.which : t.keyCode;
        if (13 == o || 8 == o)
            return !0;
        if (n = String.fromCharCode(o),
        -1 == "0123456789".indexOf(n))
            return !1;
        for (u = a.value.length,
                 h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
            ;
        for (l = ""; h < u; h++)
            -1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
        if (l += n,
        0 == (u = l.length) && (a.value = ""),
        1 == u && (a.value = "0" + r + "0" + l),
        2 == u && (a.value = "0" + r + l),
        u > 2) {
            for (ajd2 = "",
                     j = 0,
                     h = u - 3; h >= 0; h--)
                3 == j && (ajd2 += e,
                    j = 0),
                    ajd2 += l.charAt(h),
                    j++;
            for (a.value = "",
                     tamanho2 = ajd2.length,
                     h = tamanho2 - 1; h >= 0; h--)
                a.value += ajd2.charAt(h);
            a.value += r + l.substr(u - 2, u)
        }
        return !1
    }
</script>
@endsection
