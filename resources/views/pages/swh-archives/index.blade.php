@extends("layouts.app")

@section("content")
    <h2>Archives</h2>

    <p>
        Here, you can request archival of existing repositories, e.g. GitHub, using the repository's URL.
        After archival, you can visit the archived repository on Software Heritage,
        and you can add additional metadata by creating a metadata deposit.
        Archiving is realized using Dagstuhl's <a href="https://github.com/dagstuhl-publishing/swh-archive-client">swh-archive-client</a>.
    </p>

    <x-demo-note />

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
                            @switch($archive->saveRequestStatus?->value)
                                @case("accepted")
                                    @switch($archive->saveTaskStatus?->value)
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
