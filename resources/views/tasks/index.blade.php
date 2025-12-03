<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>To-Do List - Tiara Edition</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- SortableJS -->
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

  <style>
    :root {
      --bg: #f7f7ff;
      --card: #ffffff;
      --accent-1: #ff7de0;
      --accent-2: #7d7dff;
      --muted: #6b6b6b;
    }
    [data-theme="dark"] {
      --bg: #0f1221;
      --card: #0b0d18;
      --muted: #9aa0b4;
    }

    body {
      background: linear-gradient(135deg, #f8e8ff 0%, #e3f2ff 100%);
      min-height: 100vh;
      font-family: "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      transition: background .4s ease;
      background-color: var(--bg);
    }
    .navbar {
      background: rgba(255,255,255,0.9);
      border-radius: 0 0 18px 18px;
      box-shadow: 0 6px 22px rgba(17,17,17,0.08);
    }
    [data-theme="dark"] .navbar {
      background: rgba(20,20,30,0.6);
    }
    .title-gradient {
      background: linear-gradient(45deg, var(--accent-1), var(--accent-2));
      -webkit-background-clip: text;
      color: transparent;
      font-weight: 800;
      letter-spacing: -0.5px;
    }
    .card {
      border-radius: 18px;
      background: var(--card);
      box-shadow: 0 12px 30px rgba(15,15,30,0.06);
      transition: transform .25s ease, box-shadow .25s ease;
    }
    .card:hover { transform: translateY(-4px); box-shadow: 0 18px 40px rgba(15,15,30,0.09); }

    .task-done { text-decoration: line-through; opacity: 0.6; }
    .btn-custom { border-radius: 14px; padding: .45rem 1rem; }
    .cute-badge {
      background: linear-gradient(90deg,#ffeef8,#ffd7ef);
      color: #b30074;
      border-radius: 10px;
      padding: 4px 8px;
      font-size: 12px;
      font-weight: 600;
    }

    .avatar {
      width:44px; height:44px; border-radius:50%; object-fit:cover; box-shadow:0 6px 18px rgba(0,0,0,0.12)
    }

    .fade-in { animation: fadeIn .5s ease both; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(6px);} to { opacity:1; transform:none; } }

    @media (max-width: 720px) {
      .desktop-only { display: none; }
    }
  </style>
</head>
<body data-theme="{{ request()->cookie('theme', 'light') === 'dark' ? 'dark' : 'light' }}">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-4 py-3 mb-4">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
      <i class="fa-solid fa-heart title-gradient fs-4"></i>
      <span class="title-gradient fs-5">Tiara To-Do-List</span>
    </a>

    <div class="d-flex align-items-center gap-3">
      <!-- theme toggle -->
      <button id="themeToggle" class="btn btn-light btn-sm btn-custom desktop-only" title="Dark / Light">
        <i id="themeIcon" class="fa-solid fa-moon"></i>
      </button>

      <!-- avatar -->
      <img src="{{ asset('images/tiara.jpg') }}" alt="profile"
           class="avatar"
           alt="Tiara"
           style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
    </div>
  </div>
</nav>

<div class="container">
  <div class="row g-4">

    <!-- LEFT: MAIN -->
    <div class="col-lg-8">
      <div class="card p-4 fade-in mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0"><i class="fa-solid fa-plus"></i> Add Tasks</h5>
          <small class="text-muted">Aesthetic Edition</small>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" class="row g-2 align-items-center">
          @csrf
          <div class="col-md-6">
            <input type="text" name="name" class="form-control" placeholder="Task Name..." required>
          </div>
          <div class="col-md-3">
            <input type="number" name="priority" class="form-control" placeholder="Priority (1-5)">
          </div>
          <div class="col-md-3">
            <input type="date" name="date" class="form-control">
          </div>

          <div class="col-md-6">
            <select name="category" class="form-select">
              <option value="">Select Category (optional)</option>
              <option value="School">School</option>
              <option value="Home">Home</option>
              <option value="Personal">Personal</option>
              <option value="The Other">The Other</option>
            </select>
          </div>

          <div class="col-12 text-end">
            <button class="btn btn-primary btn-custom"><i class="fa-solid fa-check"></i> Add</button>
          </div>
        </form>
      </div>

      <div class="card p-3 fade-in">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0"><i class="fa-solid fa-list-check"></i> Task List</h5>
          <div class="d-flex gap-2">
            <select id="filterCategory" class="form-select form-select-sm">
              <option value="">Filter: All Categories</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
              @endforeach
            </select>
            <button id="clearFilter" class="btn btn-outline-secondary btn-sm">Reset</button>
          </div>
        </div>

        <ul id="taskList" class="list-group" style="min-height:80px;">
          @foreach ($tasks as $task)
            <li class="list-group-item d-flex justify-content-between align-items-center" 
                data-id="{{ $task->id }}" 
                data-cat="{{ $task->category }}">

              <div>
                <div class="{{ $task->is_done ? 'task-done' : '' }}">
                  <strong>{{ $task->name }}</strong>
                  <div class="small text-muted">
                    <span class="cute-badge">{{ $task->category ?? 'Whithout Category' }}</span>
                    &nbsp; • &nbsp; Priority: {{ $task->priority }}
                    &nbsp; • &nbsp; {{ $task->date ?? 'There Is No Date' }}
                  </div>
                </div>
              </div>

              <div class="d-flex gap-2">

                <!-- DONE BUTTON -->
                <form action="{{ route('tasks.update', $task) }}" method="POST" class="m-0">
                  @csrf 
                  @method('PUT')
                  <button class="btn btn-success btn-sm btn-custom" title="Mark It's Done">
                    <i class="fa-solid fa-check"></i>
                  </button>
                </form>

                <!-- ⭐⭐⭐ BUTTON EDIT (DITAMBAHKAN) ⭐⭐⭐ -->
                <a href="{{ route('tasks.edit', $task->id) }}" 
                   class="btn btn-warning btn-sm btn-custom" 
                   title="Edit Task">
                  <i class="fa-solid fa-pen"></i>
                </a>
                <!-- END EDIT -->

                <!-- DELETE BUTTON -->
                <button class="btn btn-danger btn-sm btn-custom btn-delete" 
                        data-id="{{ $task->id }}" 
                        title="Delet">
                  <i class="fa-solid fa-trash"></i>
                </button>

              </div>
            </li>
          @endforeach
        </ul>

        <div class="text-muted mt-2"><small>Drag & Drop Task To Sort (drag & drop).</small></div>
      </div>
    </div>

    <!-- RIGHT: DASHBOARD -->
    <div class="col-lg-4">
      <div class="card p-4 mb-3 fade-in">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h6 class="mb-0 title-gradient">Hallo Tiara ✨</h6>
            <small class="text-muted">Summary Of Your Assignment</small>
          </div>
          <div class="text-end">
            <div class="fs-6 fw-bold">{{ $total ?? 0 }}</div>
            <small class="text-muted">Total</small>
          </div>
        </div>

        <div class="mt-3">
          <canvas id="taskChart" height="160"></canvas>
        </div>
      </div>

      <div class="card p-3 fade-in">
        <h6 class="mb-2">Quick Statistics</h6>
        <div class="d-flex justify-content-between">
          <div>
            <div class="text-muted small">Done</div>
            <div class="fw-bold text-success">{{ $done ?? 0 }}</div>
          </div>
          <div>
            <div class="text-muted small">Not Yet</div>
            <div class="fw-bold text-danger">{{ $pending ?? 0 }}</div>
          </div>
        </div>
      </div>

      <div class="card p-3 mt-3 fade-in">
        <h6 class="mb-2">Tips Aesthetic</h6>
        <p class="small text-muted mb-0">Use Categories And Drag Task To Priority Order For Efficiency Don't Forget To Take A Break, Tiara Pretty💗</p>
      </div>
    </div>
  </div>
</div>

<!-- CSRF token for JS -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Scripts -->
<script>
  const themeToggle = document.getElementById('themeToggle');
  const themeIcon = document.getElementById('themeIcon');
  const body = document.body;

  function setTheme(theme) {
    if (theme === 'dark') {
      body.setAttribute('data-theme', 'dark');
      themeIcon.classList.remove('fa-moon'); themeIcon.classList.add('fa-sun');
      document.cookie = "theme=dark; path=/";
    } else {
      body.setAttribute('data-theme', 'light');
      themeIcon.classList.remove('fa-sun'); themeIcon.classList.add('fa-moon');
      document.cookie = "theme=light; path=/";
    }
  }

  (function(){
    const cookieTheme = document.cookie.split('; ').find(r=>r.startsWith('theme='));
    const theme = cookieTheme ? cookieTheme.split('=')[1] : 'light';
    setTheme(theme);
  })();

  themeToggle.addEventListener('click', () => {
    const cur = body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    setTheme(cur === 'dark' ? 'light' : 'dark');
  });

  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const id = btn.getAttribute('data-id');
      Swal.fire({
        title: 'Are You Sure Want To Delet It?',
        text: "The Task Will Be Permanently Deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delet',
        confirmButtonColor: '#d33',
      }).then((result) => {
        if (result.isConfirmed) {
          const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          fetch(`/tasks/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            }
          }).then(r => {
            if (r.ok) location.reload();
          });
        }
      })
    });
  });

  const ctx = document.getElementById('taskChart').getContext('2d');
  const chartData = @json($chart['data'] ?? [0,0]);
  const chartLabels = @json($chart['labels'] ?? ['Done','Not Yet']);
  const taskChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: chartLabels,
      datasets: [{
        data: chartData,
        backgroundColor: ['#7d7dff', '#ff7de0'],
        borderWidth: 0
      }]
    },
    options: {
      cutout: '70%',
      plugins: { legend: { position: 'bottom', labels: { color: 'var(--muted)' } } }
    }
  });

  const taskList = document.getElementById('taskList');
  const sortable = Sortable.create(taskList, {
    animation: 150,
    onEnd: function (evt) {
      const ordered = Array.from(taskList.querySelectorAll('li')).map(li => li.getAttribute('data-id'));
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      fetch('{{ route("tasks.reorder") }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ ordered })
      }).then(r => r.json()).then(data => {
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Saved Order', showConfirmButton: false, timer: 900 });
      });
    }
  });

  const filter = document.getElementById('filterCategory');
  const clearFilter = document.getElementById('clearFilter');
  filter.addEventListener('change', () => {
    const v = filter.value;
    document.querySelectorAll('#taskList li').forEach(li => {
      if (!v || li.getAttribute('data-cat') === v) {
        li.style.display = '';
      } else {
        li.style.display = 'none';
      }
    });
  });
  clearFilter.addEventListener('click', () => {
    filter.value = '';
    filter.dispatchEvent(new Event('change'));
  });

  document.querySelectorAll('.fade-in').forEach((el, i) => { el.style.animationDelay = (i*50) + 'ms'; });
</script>

</body>
</html>
