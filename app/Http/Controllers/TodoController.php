<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TodoController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct()
    {
        //
    }

    public function index()
    {
        $todos = auth()->user()->todos()->latest()->get();
        return view('todos.index', compact('todos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|string|max:255',
            'due_date' => 'nullable|date'
        ]);

        auth()->user()->todos()->create($request->all());
        return redirect()->route('todos.index');
    }

    public function update(Request $request, Todo $todo)
    {
        $todo->update([
            'completed' => $request->has('completed') 
                ? $request->completed 
                : !$todo->completed
        ]);
        return redirect()->route('todos.index');
    }

    public function edit(Todo $todo)
    {
        $this->authorize('update', $todo);
        
        if ($todo->completed) {
            abort(403, 'Completed todos cannot be edited');
        }
        return view('todos.edit', compact('todo'));
    }

    public function updateTask(Request $request, Todo $todo)
    {
        $this->authorize('update', $todo);
        
        if ($todo->completed) {
            abort(403, 'Completed todos cannot be edited');
        }

        $request->validate([
            'task' => 'required|string|max:255',
            'due_date' => 'nullable|date'
        ]);

        $todo->update($request->only(['task', 'due_date']));
        return redirect()->route('todos.index');
    }

    public function destroy(Todo $todo)
    {
        $this->authorize('delete', $todo);
        $todo->delete();
        return redirect()->route('todos.index');
    }

    public function toggle(Todo $todo)
    {
        $this->authorize('update', $todo);
        $todo->update(['completed' => !$todo->completed]);
        return redirect()->route('todos.index');
    }
}
