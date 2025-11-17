<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Task;
use App\Models\User;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     * getでtasks/にアクセスされた場合の「一覧表示処理」
     */
    public function index()
    {
        $data = [];
        if (Auth::check()) {
        $user = Auth::user();

        // feed_tasks が正常に呼ばれる部分
        $tasks = $user->feed_tasks()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'user' => $user,
            'tasks' => $tasks,
        ];

        return view('welcome', $data);
    }

    // 未ログイン時
    return view('welcome');
}

    /**
     * Show the form for creating a new resource.
     * getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
     */
    public function create()
    {
        $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * postでtasks/にアクセスされた場合の「新規登録処理」
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        // タスクを作成
        $request->user()->tasks()->create([
        'status' => $request->status,
        'content' => $request->content,
        'user_id' => auth()->id(),
    ]);

    // トップページへリダイレクトさせる
    return redirect('/');
    }

    /**
     * Display the specified resource.
     * getでtasks/（任意のid）にアクセスされた場合の「取得表示処理」
     */
    public function show(string $id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);

        // メッセージ詳細ビューでそれを表示
        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * getでtasks/（任意のid）/editにアクセスされた場合の「更新画面表示処理」
     */
    public function edit(string $id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);

        // メッセージ編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * putまたはpatchでtasks/（任意のid）にアクセスされた場合の「更新処理」
     */
    public function update(Request $request, string $id)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // タスクを更新
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     * deleteでtasks/（任意のid）にアクセスされた場合の「削除処理」
     */
    public function destroy(string $id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // 認証済みユーザー（閲覧者）がそのタスクの所有者である場合はタスクを削除
        if (Auth::id() === $task->user_id) {
            $task->delete();
            return back()
                ->with('success','Delete Successful');
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
