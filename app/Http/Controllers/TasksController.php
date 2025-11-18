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
            // 認証済みユーザーを取得
        $user = Auth::user();

        $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

        return view('tasks.index', [
            'user' => $user,
            'tasks' => $tasks,
        ]);
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
        // 認証済みユーザー（閲覧者）の投稿として作成（リクエストされた値をもとに作成）
        $request->user()->tasks()->create([
            'status' => $request->status,
            'content' => $request->content,
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

        // 自分のタスクでなければトップへ
        if (Auth::id() !== $task->user_id) {
            return redirect('/');
        }

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

        // 自分のタスクでなければトップへ
        if (Auth::id() !== $task->user_id) {
        return redirect('/');
        }

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

        // 自分のタスクでなければトップへ
        if (Auth::id() !== $task->user_id) {
        return redirect('/');
        }

        // タスクを更新
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }


    public function destroy(string $id)
    {
        // idの値で投稿を検索して取得
        $task = Task::findOrFail($id);

        // 認証済みユーザー（閲覧者）がその投稿の所有者である場合は投稿を削除
        if (Auth::id() === $task->user_id) {
            $task->delete();
            return redirect()
                ->with('success','Delete Successful');
        }

        // 前のURLへリダイレクトさせる
        return redirect()
                ->with('success','Delete Successful');
    }

}