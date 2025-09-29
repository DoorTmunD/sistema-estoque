<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAdminController extends Controller
{
    public function __construct()
    {
        // Permite só para admin ou super-admin
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !in_array($user->nivel, ['super-admin', 'adm'])) {
                abort(403, 'Acesso restrito à administração de usuários.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        // Lista só níveis que esse usuário pode criar:
        $nivelOptions = $this->getNivelOptions();
        return view('admin.users.create', compact('nivelOptions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $user = Auth::user();
        $nivelOptions = $this->getNivelOptions();

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'nivel' => ['required', 'in:' . implode(',', $nivelOptions)],
            'password' => 'required|confirmed|min:6'
        ]);
        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $nivelOptions = $this->getNivelOptions($user);
        return view('admin.users.edit', compact('user', 'nivelOptions'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $nivelOptions = $this->getNivelOptions($user);

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nivel' => ['required', 'in:' . implode(',', $nivelOptions)],
            'password' => 'nullable|confirmed|min:6'
        ]);

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->nivel = $data['nivel'];
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído com sucesso!');
    }

    /**
     * Define quais níveis o usuário autenticado pode criar ou editar
     */
    protected function getNivelOptions(User $target = null)
    {
        $authNivel = Auth::user()->nivel ?? 'common';
        $levels = [
            'super-admin' => ['super-admin', 'adm', 'operador', 'common'],
            'adm'         => ['adm', 'operador', 'common'],
        ];

        $options = $levels[$authNivel] ?? ['common'];

        // Admin não pode subir outro admin para super-admin
        if ($authNivel === 'adm' && $target && $target->nivel === 'super-admin') {
            abort(403, 'Você não pode editar super-admins.');
        }

        return $options;
    }
}