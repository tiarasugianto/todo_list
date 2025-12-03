<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <div class="container">
        <div class="card p-4 shadow">
            <h4 class="mb-3">Edit Task</h4>

            <form action="{{ route('tasks.editUpdate', $task) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Task Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $task->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <input type="number" name="priority" class="form-control" value="{{ $task->priority }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $task->date }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ $task->category }}">
                </div>

                <button class="btn btn-primary">Save Changes</button>
                <a href="/" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
