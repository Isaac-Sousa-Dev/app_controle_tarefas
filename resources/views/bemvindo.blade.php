<h1>Site da Aplicação</h1>

@auth

    <h1>Usuário autenticado</h1>
    <p>ID {{ Auth::user()->id }}</p>
    <p>Nome {{ Auth::user()->name }}</p>
    <p>E-mail {{ Auth::user()->email }}</p>


@endauth

@guest 

    Olá visitante, tudo bem?
    <br>
    <br>

@endguest