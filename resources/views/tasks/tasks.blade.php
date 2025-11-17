<div class="mt-4">
    @if (isset($tasks))
        <ul class="list-none">
            @foreach ($tasks as $task)
                <li class="flex items-start gap-x-2 mb-4">
                    {{-- 投稿の所有者のメールアドレスをもとにGravatarを取得して表示 --}}
                    <div class="avatar">
                        <div class="w-12 rounded">
                            <img src="{{ Gravatar::get($task->user->email) }}" alt="" />
                        </div>
                    </div>
                    <div>
                        <div>
                            {{-- タスクの所有者のユーザー詳細ページへのリンク --}}
                            <a class="link link-hover text-info" href="{{ route('users.show', $task->user->id) }}">{{ $task->user->name }}</a>
                            <span class="text-muted text-gray-500">posted at {{ $task->created_at }}</span>
                        </div>
                        <div>
                            {{-- タスク内容 --}}
                            <p class="mb-0">{!! nl2br(e($task->content)) !!}</p>
                            <p class="mb-0 text-sm text-gray-500">Status: {{ $task->status }}</p>
                        </div>

                        <div class="flex">
                            <div class="w-fit mr-1">
                                {{-- タスク削除ボタン（自分のタスクのみ） --}}
                                @if (Auth::id() == $task->user_id)
                                    <div class="mt-2">
                                        <form method="POST" action="{{ route('tasks.destroy', $task->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-soft btn-error btn-xs normal-case"
                                                onclick="return confirm('Delete task id = {{ $task->id }} ?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
            @endforeach
        </ul>

        {{-- ページネーションのリンク --}}
        {{ $tasks->links() }}
    @endif
</div>
