@if (Auth::user()->id != $user->id)
    @if (Auth::user()->is_following($user->id))
        <!-- フォロー中なのでアンフォローボタン表示 -->
        {!! Form::open(['route'=>['user.unfollow', $user->id], 'method'=>'delete']) !!}
            {!! Form::submit('Unfollow', ['class' => "btn btn-danger btn-block"]) !!}
        {!! Form::close() !!}
    @else
        <!-- 未フォローなのでフォローボタン表示 -->
        {!! Form::open(['route'=>['user.follow', $user->id]]) !!}
            {!! Form::submit('Follow', ['class' => "btn btn-primary btn-block"]) !!}
        {!! Form::close() !!}
    @endif
@else
    <!-- 自分自身の場合は表示されない -->
@endif