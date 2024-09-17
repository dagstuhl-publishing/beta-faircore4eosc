@extends("layouts.app")

@section("content")
    <h1>Archives</h1>

    <p>TODO</p>

    <div class="mb-3">
        <form method="POST">
            @csrf
            <p>
                <input class="form-control" type="text" name="originUrl" placeholder="Repository URL">
            </p>
            <p class="text-center">
                <button class="btn btn-primary" type="submit">
                    Request Archival
                </button>
            </p>
        </form>
    </div>

    {!! $archives->links() !!}

    <table class="table align-middle">
        <thead>
            <tr>
                <th scope="col" class="w-100">Origin</th>
                <th scope="col">Status</th>
                <th scope="col">Requested</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($archives as $archive)
                <tr>
                    <td>
                        <a href="{{ $archive->originUrl }}" class="font-monospace">
                            {{ $archive->originUrl }}
                        </a>
                    </td>
                    <td class="text-nowrap">
                        <span title="{{ $archive->saveRequestStatus ?? "pending" }} / {{ $archive->saveTaskStatus ?? "not created" }}">
                            @switch($archive->saveRequestStatus)
                                @case("accepted")
                                    @switch($archive->saveTaskStatus)
                                        @case("succeeded")
                                            <i class="bi bi-check-circle-fill"></i> Succeeded
                                            @break
                                        @case("failed")
                                            <i class="bi bi-exclamation-circle-fill"></i> Failed
                                            @break
                                        @default
                                            <i class="bi bi-three-dots"></i> Accepted
                                            @break
                                    @endswitch
                                    @break
                                @case("rejected")
                                    <i class="bi bi-exclamation-circle-fill"></i> Rejected
                                    @break
                                @default
                                    <i class="bi bi-three-dots"></i> Pending
                                    @break
                            @endswitch
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <span title="{{ $archive->created_at }}">
                            {{ $archive->created_at->diffForHumans() }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <a class="btn btn-primary @if($archive->swhIdContext === null) disabled @endif" href="{{ $archive->getBrowseUrl() }}">
                            <i class="bi bi-box-arrow-up-right"></i> Browse
                        </a>
                        <a class="btn btn-primary @if($archive->swhIdContext === null) disabled @endif"
                            href="{{ route("swh-deposits.new", [ "swhId" => $archive->swhIdContext ]) }}"
                        >
                            <i class="bi bi-plus-lg"></i> Metadata
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="5">
                        No archivals requested
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {!! $archives->links() !!}
@endsection
