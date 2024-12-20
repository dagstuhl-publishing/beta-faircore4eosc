@extends("layouts.app")

@section("content")
    <div class="d-flex align-items-center mb-3">
        <h2 class="flex-grow-1 m-0">Deposits</h2>
        <a class="btn btn-primary" href="{{ route("swh-deposits.new") }}">
            <i class="bi bi-cloud-arrow-up-fill"></i> New Deposit
        </a>
    </div>

    <p>
        Here, you can archive projects by uploading the source code as a zip-archive and creating a deposit.
        Deposits have associated metadata in form of Codemeta-JSON.
        You can also create deposits referencing an existing artifact identified by its SWHID,
        which allows you to provide additional metadata for this artifact.
        The depositing is realized using Dagstuhl's <a href="https://github.com/dagstuhl-publishing/swh-deposit-client">swh-deposit-client</a>.
    </p>

    <x-demo-note />

    {!! $deposits->links() !!}

    <table class="table align-middle">
        <thead>
            <tr>
                <th scope="col">UUID</th>
                <th scope="col" class="w-100">Archive/Origin</th>
                <th scope="col">Status</th>
                <th scope="col">Uploaded</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($deposits as $deposit)
                <tr>
                    <td>
                        <a href="{{ route("swh-deposits.show", [ "deposit" => $deposit ]) }}" class="font-monospace">
                            {{ substr($deposit->uuid, 0, 18) }}<wbr>{{ substr($deposit->uuid, 18) }}<wbr>
                        </a>
                    </td>
                    <td>
                        @if($deposit->archiveFilename !== null)
                            <i class="bi bi-file-earmark-zip-fill"></i>
                            <a href="{{ asset("storage/deposits/{$deposit->archivePath}") }}" download="{{ $deposit->archiveFilename }}">{{ $deposit->archiveFilename }}</a>
                            ({{ $deposit->getFormattedArchiveSize() }})
                        @else
                            {{ $deposit->originSwhId }}
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <span title="{{ $deposit->depositStatus?->value ?? "pending" }}">
                            @switch($deposit->depositStatus?->value)
                                @case("partial")
                                @case("deposited")
                                @case("verified")
                                @case("loading")
                                    <i class="bi bi-three-dots"></i> Deposited
                                    @break
                                @case("rejected")
                                    <i class="bi bi-exclamation-circle-fill"></i> Rejected
                                    @break
                                @case("done")
                                    <i class="bi bi-check-circle-fill"></i> Done
                                    @break
                                @case("failed")
                                    <i class="bi bi-exclamation-circle-fill"></i> Failed
                                    @break
                                @default
                                    <i class="bi bi-three-dots"></i> Pending
                                    @break
                            @endswitch
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <span title="{{ $deposit->created_at }}">
                            {{ $deposit->created_at->diffForHumans() }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <a class="btn btn-primary @if($deposit->depositSwhIdContext === null) disabled @endif" href="{{ $deposit->getBrowseUrl() }}">
                            <i class="bi bi-box-arrow-up-right"></i> Browse
                        </a>
                        <x-export-metadata-dropdown :deposit="$deposit" text="Metadata" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="5">
                        No deposits created
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {!! $deposits->links() !!}
@endsection
