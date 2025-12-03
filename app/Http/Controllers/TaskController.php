<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('order_index')->get();

        $total = $tasks->count();
        $done = $tasks->where('is_done', true)->count();
        $pending = $total - $done;

        $chart = [
            'labels' => ['Done', 'Not Yet'],
            'data' => [$done, $pending],
        ];

        $categories = $tasks->pluck('category')->unique()->filter()->values();

        return view('tasks.index', compact('tasks', 'total', 'done', 'pending', 'chart', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $maxOrder = Task::max('order_index') ?? 0;

        Task::create([
            'name' => $request->name,
            'priority' => $request->priority ?? 1,
            'date' => $request->date,
            'category' => $request->category ?? null,
            'order_index' => $maxOrder + 1,
        ]);

        return redirect()->back();
    }

    // ⬇️ FITUR EDIT
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    // ⬇️ UPDATE DATA (bukan toggle)
    public function editUpdate(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'nullable|integer',
            'date' => 'nullable|date',
            'category' => 'nullable|string',
        ]);

        $task->update([
            'name' => $request->name,
            'priority' => $request->priority,
            'date' => $request->date,
            'category' => $request->category,
        ]);

        return redirect('/')->with('success', 'Task berhasil diupdate!');
    }

    // toggle selesai
    public function update(Request $request, Task $task)
    {
        $task->update([
            'is_done' => !$task->is_done,
        ]);

        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back();
    }

    public function reorder(Request $request)
    {
        $ordered = $request->input('ordered');
        if (is_array($ordered)) {
            foreach ($ordered as $index => $id) {
                Task::where('id', $id)->update(['order_index' => $index]);
            }
        }
        return response()->json(['status' => 'ok']);
    }
}
