<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardInfoController extends Controller
{
    /* ===== Helpers ===== */

    protected function ensureAdmin(): void
    {
        $u = Auth::user();
        if (!$u || (int)$u->id_level !== 1) {
            abort(403, 'Hanya Admin yang diizinkan mengelola informasi dashboard.');
        }
    }

    protected function normDate(?string $v): ?string
    {
        if (!$v) return null;
        try { return Carbon::parse($v)->toDateString(); } catch (\Throwable $e) { return null; }
    }

    protected function normOrder($v): int
    {
        // pastikan integer >= 0
        if ($v === null || $v === '') return 0;
        $n = (int)$v;
        return $n < 0 ? 0 : $n;
    }

    /* ===== CRUD ===== */

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $q      = trim((string)$request->get('q', ''));
        $status = $request->get('status'); // 1/0/null
        $target = $request->get('target'); // pegawai/pimpinan/semua/null

        $infos = DB::table('dashboard_info')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('judul', 'like', "%{$q}%")
                      ->orWhere('konten', 'like', "%{$q}%");
                });
            })
            ->when($status !== null && $status !== '', fn($qq) => $qq->where('aktif', (int)$status))
            ->when($target !== null && $target !== '', fn($qq) => $qq->where('target', $target))
            // aman bila ada NULL
            ->orderByRaw('COALESCE(urutan, 999999) ASC')
            ->orderByDesc('id')
            ->paginate(12)
            ->appends($request->query());

        $breadcrumb = (object)[
            'title' => 'Informasi Dashboard',
            'list'  => ['Admin', 'Informasi Dashboard']
        ];
        $activeMenu = 'dashboard_info';

        return view('dashboard_info.index', compact('infos','breadcrumb','activeMenu','q','status','target'));
    }

    public function create()
    {
        $this->ensureAdmin();

        $breadcrumb = (object)[
            'title' => 'Tambah Informasi',
            'list'  => ['Admin', 'Informasi Dashboard', 'Tambah']
        ];
        $activeMenu = 'dashboard_info';

        return view('dashboard_info.create', compact('breadcrumb','activeMenu'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'judul'      => 'required|string|max:150',
            'konten'     => 'required|string',
            'target'     => 'required|in:pegawai,pimpinan,semua',
            'aktif'      => 'nullable|boolean',
            'urutan'     => 'nullable|integer|min:0',
            'selamanya'  => 'nullable|boolean',
            'mulai'      => 'nullable|date',
            'selesai'    => 'nullable|date|after_or_equal:mulai',
        ]);

        $isForever = $request->boolean('selamanya');

        $payload = [
            'judul'      => $request->judul,
            'konten'     => $request->konten,
            'mulai'      => $isForever ? null : $this->normDate($request->mulai),
            'selesai'    => $isForever ? null : $this->normDate($request->selesai),
            'target'     => $request->target,
            'aktif'      => $request->boolean('aktif') ? 1 : 0,
            'urutan'     => $this->normOrder($request->input('urutan', 0)),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('dashboard_info', 'created_by')) $payload['created_by'] = Auth::id();
        if (Schema::hasColumn('dashboard_info', 'updated_by')) $payload['updated_by'] = Auth::id();

        DB::table('dashboard_info')->insert($payload);

        return redirect()->route('dashboard-info.index')->with('success','Informasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->ensureAdmin();

        if (!$id || !is_numeric($id)) abort(404);

        $row = DB::table('dashboard_info')->where('id', (int)$id)->first();
        abort_if(!$row, 404);

        $breadcrumb = (object)[
            'title' => 'Edit Informasi',
            'list'  => ['Admin', 'Informasi Dashboard', 'Edit']
        ];
        $activeMenu = 'dashboard_info';

        return view('dashboard_info.edit', compact('row','breadcrumb','activeMenu'));
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();

        if (!$id || !is_numeric($id)) {
            return back()->withErrors(['id' => 'ID tidak valid.'])->withInput();
        }

        $current = DB::table('dashboard_info')->where('id', (int)$id)->first();
        if (!$current) {
            return back()->withErrors(['id' => 'Data tidak ditemukan.'])->withInput();
        }

        $request->validate([
            'judul'      => 'required|string|max:150',
            'konten'     => 'required|string',
            'target'     => 'required|in:pegawai,pimpinan,semua',
            'aktif'      => 'nullable|boolean',
            'urutan'     => 'nullable|integer|min:0',
            'selamanya'  => 'nullable|boolean',
            'mulai'      => 'nullable|date',
            'selesai'    => 'nullable|date|after_or_equal:mulai',
        ]);

        $isForever = $request->boolean('selamanya');

        $payload = [
            'judul'      => $request->judul,
            'konten'     => $request->konten,
            'mulai'      => $isForever ? null : $this->normDate($request->mulai),
            'selesai'    => $isForever ? null : $this->normDate($request->selesai),
            'target'     => $request->target,
            'aktif'      => $request->boolean('aktif') ? 1 : 0,
            // JANGAN timpa ke 0 jika field tak terkirim -> gunakan nilai lama
            'urutan'     => $request->filled('urutan') ? $this->normOrder($request->urutan) : (int)$current->urutan,
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('dashboard_info', 'updated_by')) $payload['updated_by'] = Auth::id();

        $affected = DB::table('dashboard_info')->where('id', (int)$id)->update($payload);

        if ($affected === 0) {
            return back()->withErrors(['update' => 'Tidak ada perubahan yang disimpan.'])->withInput();
        }

        return redirect()->route('dashboard-info.index')->with('success','Informasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->ensureAdmin();
        if (!$id || !is_numeric($id)) return back()->withErrors(['id' => 'ID tidak valid.']);
        DB::table('dashboard_info')->where('id', (int)$id)->delete();
        return redirect()->route('dashboard-info.index')->with('success','Informasi dihapus.');
    }

    /* ===== Dipakai dashboard pegawai/pimpinan ===== */

    public static function getForDashboard(string $role='pegawai', int $limit=5)
    {
        $today = Carbon::now()->toDateString();

        return DB::table('dashboard_info')
            ->where('aktif',1)
            ->where(function($q) use($today){ $q->whereNull('mulai')->orWhere('mulai','<=',$today); })
            ->where(function($q) use($today){ $q->whereNull('selesai')->orWhere('selesai','>=',$today); })
            ->where(function($q) use($role){ $q->where('target','semua')->orWhere('target',$role); })
            ->orderByRaw('COALESCE(urutan, 999999) ASC')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
