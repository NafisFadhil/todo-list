<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Todo</title>
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
        <h1 class="text-2xl font-bold mb-6">Edit Todo</h1>
        
        <form action="{{ route('todos.updateTask', $todo) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex flex-col gap-2">
                <div class="flex">
                    <input type="text" name="task" value="{{ $todo->task }}" 
                           class="flex-grow px-4 py-2 border rounded-l focus:outline-none">
                </div>
                <input type="datetime-local" name="due_date" 
                       value="{{ $todo->due_date ? \Carbon\Carbon::parse($todo->due_date)->format('Y-m-d\TH:i') : '' }}"
                       class="px-4 py-2 border rounded focus:outline-none">
                <button type="submit" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Update
                </button>
            </div>
        </form>
    </div>
</body>
</html>
