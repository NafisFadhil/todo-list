<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Todo App</h1>
            <div class="flex gap-4">
                @auth
                    <span>Hello, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-blue-500 hover:text-blue-700">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700">Login</a>
                    <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-md">
        <h1 class="text-2xl font-bold mb-6">Todo List</h1>
        
        <form action="{{ route('todos.store') }}" method="POST" class="mb-6">
            @csrf
            <div class="flex flex-col gap-2">
                <div class="flex">
                    <input type="text" name="task" placeholder="Add new task" 
                           class="flex-grow px-4 py-2 border rounded-l focus:outline-none">
                    <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-r hover:bg-blue-600">
                        Add
                    </button>
                </div>
                <input type="datetime-local" name="due_date" 
                       class="px-4 py-2 border rounded focus:outline-none">
            </div>
        </form>

        <ul class="bg-white rounded shadow">
            @foreach($todos as $todo)
                <li class="border-b last:border-b-0">
                    <div class="flex items-center justify-between px-4 py-3">
                        <div class="flex items-center">
                            <form action="{{ route('todos.update', $todo) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="checkbox" 
                                       onchange="this.form.submit()"
                                       {{ $todo->completed ? 'checked' : '' }}
                                       class="mr-3 h-5 w-5">
                            </form>
                            <div>
                                <span class="{{ $todo->completed ? 'line-through text-gray-400' : '' }}">
                                    {{ $todo->task }}
                                </span>
                                @if($todo->due_date)
                                    <div class="text-sm text-gray-500">
                                        Due: {{ \Carbon\Carbon::parse($todo->due_date)->format('M d, Y H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @if(!$todo->completed)
                                <a href="{{ route('todos.edit', $todo) }}" 
                                   class="text-blue-500 hover:text-blue-700">
                                    Edit
                                </a>
                            @endif
                            <form action="{{ route('todos.destroy', $todo) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-500 hover:text-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</body>
</html>
