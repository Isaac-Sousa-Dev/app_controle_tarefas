<?php

namespace App\Http\Controllers;

use App\Exports\TarefasExport;
use App\Mail\NovaTarefaMail;
use App\Models\Tarefa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class TarefaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        $tarefas = Tarefa::where('user_id', $user_id)->paginate(10);
        return view('tarefa.index', ['tarefas' => $tarefas]);

        /*
        $id = Auth::user()->id;
        $name = Auth::user()->name;
        $email = Auth::user()->email;

        return "ID: $id | Nome: $name | E-mail: $email";
        /*
        if(Auth::check()){
            $id = Auth::user()->id;
            $name = Auth::user()->name;
            $email = Auth::user()->email;

            return "ID: $id | Nome: $name | E-mail: $email";
        } else {
            return 'Voce não está logado no sistema';
        }
        */

        /*
        if(auth()->check()){
            $id = auth()->user()->id;
            $name = auth()->user()->name;
            $email = auth()->user()->email;

            return "ID: $id | Nome: $name | E-mail: $email";
        } else {
            return 'Voce não está logado no sistema';
        }
        */
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tarefa.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $regras = [
            'tarefa' => 'required|min:2|max:100',
            'data_limite_conclusao' => 'required'
        ];

        $feedback = [
            'required' => 'O campo :attribute é obrigatório',
            'tarefa.min' => 'No mínimo 2 caracteres',
            'tarefa.max' => 'No máximo 100 caracteres',
            'data_limite_conclusao.required' => 'Campo Data obrigatório!'
        ];
      
        $request->validate($regras, $feedback);

        $dados = $request->all();
        $dados['user_id'] = auth()->user()->id;

        
        $tarefa = Tarefa::create($dados);
        $destinatario = auth()->user()->email;
        Mail::to($destinatario)->send(new NovaTarefaMail($tarefa));
        return redirect()->route('tarefa.show', ['tarefa' => $tarefa->id]); 

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function show(Tarefa $tarefa)
    {
        return view('tarefa.show', ['tarefa' => $tarefa]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarefa $tarefa)
    {
        $user_id = auth()->user()->id;
        if($tarefa->user_id == $user_id) {
            return view('tarefa.edit', ['tarefa' => $tarefa]);
        }

        return view('acesso-negado');
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarefa $tarefa)
    {
        $user_id = auth()->user()->id;
        if(!$tarefa->user_id == $user_id ) {

            return view('acesso-negado');
            
        }

        $tarefa->update($request->all());
        return redirect()->route('tarefa.show', ['tarefa' => $tarefa->id]);

        

       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tarefa  $tarefa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarefa $tarefa)
    {
        $user_id = auth()->user()->id;
        if(!$tarefa->user_id == $user_id ) {

            return view('acesso-negado');
            
        }
        $tarefa->delete();
        return redirect()->route('tarefa.index');
    }

    public function exportacao($extensao) {
        $nome_arquivo = 'lista_de_tarefas';

        
        if($extensao == 'xlsx') {
            $nome_arquivo .= '.'.$extensao;
        } elseif ($extensao == 'csv') {
            $nome_arquivo .= '.'.$extensao;
        } elseif ($extensao == 'pdf'){
            $nome_arquivo .= '.'.$extensao;
        } else {
            return redirect()->route('tarefa.index');
        }

        return Excel::download(new TarefasExport, $nome_arquivo);
    }

    public function exportar() {
        $tarefas = auth()->user()->tarefas()->get();
        $pdf = PDF::loadView('tarefa.pdf', ['tarefas' => $tarefas]);

        $pdf->setPaper('a4', 'portrait');
        // tipo de papel: a4, letter
        // orientação: landscape (paisagem), portrait (retrato)
        //return $pdf->download('lista_de_tarefas.pdf');
        return $pdf->stream('lista_de_tarefas.pdf');
    }
}
