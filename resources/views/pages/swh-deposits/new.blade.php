@extends("layouts.app")

@push("scripts")
    <script type="text/javascript">
        const licenses = {!! json_encode($licenses) !!};
        const languages = {!! json_encode($languages) !!};
        const initialSwhId = {!! json_encode($swhId) !!};

        document.addEventListener("DOMContentLoaded", function(event) {
            initSwhDepositForm("#swhDepositForm", licenses, languages, initialSwhId);
        });
    </script>
@endpush

@section("content")
    <h2>New Deposit</h2>

    <form class="mb-3" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="swhDepositForm"></div>
    </form>
@endsection
