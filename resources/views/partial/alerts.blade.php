@if(count($errors) > 0 || Session::has('error') || Session::has('success') || Session::has('info'))
    <div class="alerts">
        @if (count($errors) > 0)
            <div class="alert warning">
                <h6>Whoops!</h6>
                <p>There were some problems with your input.</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (Session::has('error'))
            <div class="alert error">
                <h6>Error</h6>
                <p>{{Session::pull('error')}}</p>
            </div>
        @elseif (Session::has('success'))
            <div class="alert success">
                <h6>Success!</h6>
                <p>{{Session::pull('success')}}</p>
            </div>
        @elseif (Session::has('info'))
            <div class="alert">
                <p>{{Session::pull('info')}}</p>
            </div>
        @endif
    </div>
@endif