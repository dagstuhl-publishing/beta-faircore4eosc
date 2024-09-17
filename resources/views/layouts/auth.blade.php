@extends("layouts.base")

@section("body")
    <main style="padding-top: 5rem">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    @yield("content")
                </div>
            </div>
        </div>
    </main>
@endsection
