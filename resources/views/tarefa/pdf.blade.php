<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
        <style>
            .page-break {
                page-break-after: always
            }
            .titulo {
                text-align: center;
                border: 1px;
                background-color: #ccc;
                width: 100%;
                text-transform: uppercase;
                font-weight: bold;
                margin-bottom: 25px;
            }

            .tabela {
                width: 100%
            }

            table th{
                text-align: left
            }
        </style>
    </head>
    <body>
        <div class="titulo">
            <h2>Lista de Tarefas</h2>
        </div>

        <table class="tabela">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tarefa</th>
                    <th>Data Limite Conclusão</th>
                </tr>
            </thead>

            <tbody>
                @foreach($tarefas as $key => $tarefa)
                    <tr>
                        <td>{{ $tarefa->id }}</td>
                        <td>{{ $tarefa->tarefa }}</td>
                        <td>{{ date('d/m/Y', strtotime($tarefa->data_limite_conclusao)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>
        <h2>Página 2</h2>
    </body>
</html>


