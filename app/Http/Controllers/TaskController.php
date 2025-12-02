<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // ambil semua, urut berdasarkan order_index
        $tasks = Task::orderBy('order_index')->get();

        // statistik untuk dashboard
        $total = $tasks->count();
        $done = $tasks->where('is_done', true)->count();
        $pending = $total - $done;

        // data chart (label dan data)
        $chart = [
            'labels' => ['Selesai', 'Belum Selesai'],
            'data' => [$done, $pending],
        ];

        // kategori list
        $categories = $tasks->pluck('category')->unique()->filter()->values();

        return view('tasks.index', compact('tasks', 'total', 'done', 'pending', 'chart', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // set order_index ke akhir
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

    public function update(Request $request, Task $task)
    {
        // toggle selesai
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

    // route untuk menyimpan urutan setelah drag & drop
    public function reorder(Request $request)
    {
        $ordered = $request->input('ordered'); // array of ids in order
        if (is_array($ordered)) {
            foreach ($ordered as $index => $id) {
                Task::where('id', $id)->update(['order_index' => $index]);
            }
        }
        return response()->json(['status' => 'ok']);
    }
}
